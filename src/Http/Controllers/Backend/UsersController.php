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
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->process($request, new User());
    }

    /**
     * Update the given resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Rinvex\Fort\Models\User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
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
            'with'  => ['warning' => trans('rinvex/fort::messages.user.deleted', ['userId' => $user->id])],
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
        $input = $request->all();
        $input['email_verified'] = $request->get('email_verified', false);
        $input['phone_verified'] = $request->get('phone_verified', false);

        // Remove empty password fields
        if (! $input['password']) {
            unset($input['password']);
        }

        // Save user
        ! $user->exists ? $user = $user->create($input) : $user->update($input);

        // Sync abilities
        if ($request->user($this->getGuard())->can('grant-abilities')) {
            $user->abilities()->sync((array) array_pull($input, 'abilityList'));
        }

        // Sync roles
        if ($request->user($this->getGuard())->can('assign-roles')) {
            $user->roles()->sync((array) array_pull($input, 'roleList'));
        }

        return intend([
            'route' => 'rinvex.fort.backend.users.index',
            'with'  => ['success' => trans('rinvex/fort::messages.user.saved', ['userId' => $user->id])],
        ]);
    }
}
