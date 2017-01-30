<?php

/*
 * NOTICE OF LICENSE
 *
 * Part of the Rinvex Fort Package.
 *
 * This source file is subject to The MIT License (MIT)
 * that is bundled with this package in the LICENSE file.
 *
 * Package: Rinvex Fort Package
 * License: The MIT License (MIT)
 * Link:    https://rinvex.com
 */

namespace Rinvex\Fort\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Rinvex\Fort\Models\Role;
use Rinvex\Fort\Models\User;
use Rinvex\Fort\Models\Ability;
use Rinvex\Fort\Http\Controllers\AuthorizedController;
use Rinvex\Fort\Http\Requests\Backend\UserStoreRequest;
use Rinvex\Fort\Http\Requests\Backend\UserUpdateRequest;

class UsersController extends AuthorizedController
{
    /**
     * {@inheritdoc}
     */
    protected $resource = 'users';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::paginate(config('rinvex.fort.backend.items_per_page'));

        return view('rinvex/fort::backend/users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->form('create', 'store', new User());
    }

    /**
     * Show the form for editing the given resource.
     *
     * @param \Rinvex\Fort\Models\User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return $this->form('edit', 'update', $user);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Rinvex\Fort\Http\Requests\Backend\UserStoreRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(UserStoreRequest $request)
    {
        return $this->process($request, new User());
    }

    /**
     * Update the given resource in storage.
     *
     * @param \Rinvex\Fort\Http\Requests\Backend\UserUpdateRequest $request
     * @param \Rinvex\Fort\Models\User                             $user
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        return $this->process($request, $user);
    }

    /**
     * Delete the given resource from storage.
     *
     * @param \Rinvex\Fort\Models\User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(User $user)
    {
        $user->delete();

        return intend([
            'route' => 'rinvex.fort.backend.users.index',
            'with'  => ['warning' => trans('rinvex/fort::backend/messages.user.deleted', ['userId' => $user->id])],
        ]);
    }

    /**
     * Show the form for create/update of the given resource.
     *
     * @param string                   $mode
     * @param string                   $action
     * @param \Rinvex\Fort\Models\User $user
     *
     * @return \Illuminate\Http\Response
     */
    protected function form($mode, $action, User $user)
    {
        $countries = array_map(function ($country) {
            return $country['name'];
        }, countries());

        $abilityList = Ability::all()->groupBy('resource')->map(function ($item) {
            return $item->pluck('name', 'id');
        })->toArray();

        $roleList = Role::all()->pluck('name', 'id')->toArray();

        return view('rinvex/fort::backend/users.form', compact('user', 'abilityList', 'roleList', 'countries', 'mode', 'action'));
    }

    /**
     * Process the form for store/update of the given resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Rinvex\Fort\Models\User $user
     *
     * @return \Illuminate\Http\Response
     */
    protected function process(Request $request, User $user)
    {
        // Prepare required input fields
        $input = $request->only(array_intersect(array_keys($request->all()), $user->getFillable()));
        $roles = $request->user($this->getGuard())->can('assign-roles') ? ['roles' => array_pull($input, 'roleList')] : [];
        $abilities = $request->user($this->getGuard())->can('grant-abilities') ? ['abilities' => array_pull($input, 'abilityList')] : [];

        // Store data into the entity
        $result = ! $user->exists ? Role::create($input + $roles + $abilities) : $user->update($input + $roles + $abilities);

        // Model `update` method returns false if no attributes updated,
        // this happens save button clicked without chaning anything
        $message = $user->exists
            ? ($result === false
                ? ['warning' => trans('rinvex/fort::backend/messages.user.nothing_updated', ['userId' => $user->id])]
                : ['success' => trans('rinvex/fort::backend/messages.user.updated', ['userId' => $user->id])])
            : ['success' => trans('rinvex/fort::backend/messages.user.created', ['userId' => $user->id])];

        return intend([
            'route' => 'rinvex.fort.backend.users.index',
            'with'  => $message,
        ]);
    }
}
