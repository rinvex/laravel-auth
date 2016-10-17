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

use Rinvex\Country\Loader;
use Illuminate\Http\Request;
use Rinvex\Fort\Models\User;
use Rinvex\Fort\Contracts\UserRepositoryContract;
use Rinvex\Fort\Http\Controllers\AuthorizedController;
use Rinvex\Fort\Http\Requests\Backend\UserStoreRequest;
use Rinvex\Fort\Http\Requests\Backend\UserUpdateRequest;

class UsersController extends AuthorizedController
{
    /**
     * {@inheritdoc}
     */
    protected $resourceAbilityMap = [
        'activate'   => 'activate',
        'deactivate' => 'deactivate',
    ];

    /**
     * The user repository instance.
     *
     * @var \Rinvex\Fort\Contracts\UserRepositoryContract
     */
    protected $userRepository;

    /**
     * Create a new users controller instance.
     *
     * @return void
     */
    public function __construct(UserRepositoryContract $userRepository)
    {
        parent::__construct();

        $this->authorizeResource(User::class);

        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = $this->userRepository->paginate(config('rinvex.fort.backend.items_per_page'));

        return view('rinvex/fort::backend/users.index', compact('users'));
    }

    /**
     * Display the specified resource.
     *
     * @param \Rinvex\Fort\Models\User $user
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function show(User $user)
    {
        $actions     = ['view', 'create', 'edit', 'delete', 'import', 'export'];
        $resources   = app('rinvex.fort.ability')->findAll()->groupBy('resource');
        $columns     = ['resource', 'view', 'create', 'edit', 'delete', 'import', 'export', 'other'];
        $userCountry = Loader::country($user->country);
        $country     = ! empty($userCountry) ? $userCountry->getName().' '.$userCountry->getEmoji() : null;
        $phone       = ! empty($userCountry) ? $userCountry->getCallingCode().$user->phone : null;

        return view('rinvex/fort::backend/users.show', compact('user', 'resources', 'actions', 'columns', 'country', 'phone'));
    }

    /**
     * Bulk control the given resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function bulk()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->form('create', 'store', $this->userRepository->createModel());
    }

    /**
     * Show the form for copying the given resource.
     *
     * @param \Rinvex\Fort\Models\User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function copy(User $user)
    {
        return $this->form('copy', 'store', $user);
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
        //
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
        //
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
        //
    }

    /**
     * Import the given resources into storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function import()
    {
        //
    }

    /**
     * Export the given resources from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function export()
    {
        //
    }

    /**
     * Show the form for create/edit/copy of the given resource.
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
        }, Loader::countries());

        $abilityList = app('rinvex.fort.ability')->findAll()->groupBy('resource')->map(function ($item) {
            return $item->pluck('title', 'id');
        })->toArray();

        $roleList = app('rinvex.fort.role')->findAll()->pluck('title', 'id')->toArray();

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
    protected function process(Request $request, User $user = null)
    {
        //
    }
}
