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
use Rinvex\Fort\Contracts\RoleRepositoryContract;
use Rinvex\Fort\Http\Controllers\AuthorizedController;
use Rinvex\Fort\Http\Requests\Backend\RoleStoreRequest;
use Rinvex\Fort\Http\Requests\Backend\RoleUpdateRequest;

class RolesController extends AuthorizedController
{
    /**
     * {@inheritdoc}
     */
    protected $resource = 'roles';

    /**
     * {@inheritdoc}
     */
    protected $resourceActionWhitelist = ['assign'];

    /**
     * The role repository instance.
     *
     * @var \Rinvex\Fort\Contracts\RoleRepositoryContract
     */
    protected $roleRepository;

    /**
     * Create a new users controller instance.
     *
     * @param \Rinvex\Fort\Contracts\RoleRepositoryContract $roleRepository
     */
    public function __construct(RoleRepositoryContract $roleRepository)
    {
        parent::__construct();

        $this->roleRepository = $roleRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = $this->roleRepository->paginate(config('rinvex.fort.backend.items_per_page'));

        return view('rinvex/fort::backend/roles.index', compact('roles'));
    }

    /**
     * Display the specified resource.
     *
     * @param \Rinvex\Fort\Models\Role $role
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function show(Role $role)
    {
        $resources = app('rinvex.fort.ability')->findAll()->groupBy('resource');
        $actions   = ['list', 'view', 'create', 'update', 'delete', 'import', 'export'];
        $columns   = ['resource', 'list', 'view', 'create', 'update', 'delete', 'import', 'export', 'other'];

        return view('rinvex/fort::backend/roles.show', compact('role', 'resources', 'actions', 'columns'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->form('create', 'store', $this->roleRepository->createModel());
    }

    /**
     * Show the form for copying the given resource.
     *
     * @param \Rinvex\Fort\Models\Role $role
     *
     * @return \Illuminate\Http\Response
     */
    public function copy(Role $role)
    {
        return $this->form('copy', 'store', $role);
    }

    /**
     * Show the form for editing the given resource.
     *
     * @param \Rinvex\Fort\Models\Role $role
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        return $this->form('edit', 'update', $role);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Rinvex\Fort\Http\Requests\Backend\RoleStoreRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(RoleStoreRequest $request)
    {
        return $this->process($request);
    }

    /**
     * Update the given resource in storage.
     *
     * @param \Rinvex\Fort\Http\Requests\Backend\RoleUpdateRequest $request
     * @param \Rinvex\Fort\Models\Role                             $role
     *
     * @return \Illuminate\Http\Response
     */
    public function update(RoleUpdateRequest $request, Role $role)
    {
        return $this->process($request, $role);
    }

    /**
     * Delete the given resource from storage.
     *
     * @param \Rinvex\Fort\Models\Role $role
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Role $role)
    {
        $result = $this->roleRepository->delete($role);

        return intend([
            'route' => 'rinvex.fort.backend.roles.index',
            'with'  => ['rinvex.fort.alert.warning' => trans('rinvex/fort::backend/messages.role.deleted', ['roleId' => $result->id])],
        ]);
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
     * Bulk control the given resources.
     *
     * @return \Illuminate\Http\Response
     */
    public function bulk()
    {
        //
    }

    /**
     * Show the form for create/edit/copy of the given resource.
     *
     * @param string                   $mode
     * @param string                   $action
     * @param \Rinvex\Fort\Models\Role $role
     *
     * @return \Illuminate\Http\Response
     */
    protected function form($mode, $action, Role $role)
    {
        $abilityList = app('rinvex.fort.ability')->findAll()->groupBy('resource')->map(function ($ability) {
            return $ability->pluck('title', 'id');
        })->toArray();

        return view('rinvex/fort::backend/roles.form', compact('role', 'abilityList', 'mode', 'action'));
    }

    /**
     * Process the form for store/update of the given resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Rinvex\Fort\Models\Role $role
     *
     * @return \Illuminate\Http\Response
     */
    protected function process(Request $request, Role $role = null)
    {
        // Prepare required input fields
        $input     = $request->except(['_method', '_token', 'id']);
        $abilities = $request->user($this->getGuard())->can('grant-abilities') ? ['abilities' => array_pull($input, 'abilityList')] : [];

        // Store data into the entity
        $result = $this->roleRepository->store($role, $input + $abilities);

        // Repository `store` method returns false if no attributes
        // updated, happens save button clicked without chaning anything
        $message = ! is_null($role)
            ? ($result === false
                ? ['rinvex.fort.alert.warning' => trans('rinvex/fort::backend/messages.role.nothing_updated', ['roleId' => $role->id])]
                : ['rinvex.fort.alert.success' => trans('rinvex/fort::backend/messages.role.updated', ['roleId' => $result->id])])
            : ['rinvex.fort.alert.success' => trans('rinvex/fort::backend/messages.role.created', ['roleId' => $result->id])];

        return intend([
            'route' => 'rinvex.fort.backend.roles.index',
            'with'  => $message,
        ]);
    }
}
