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
use Rinvex\Fort\Models\Ability;
use Rinvex\Fort\Http\Controllers\AuthorizedController;

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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::paginate(config('rinvex.fort.backend.items_per_page'));

        return view('rinvex/fort::backend/roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->form('create', 'store', new Role());
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
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->process($request, new Role());
    }

    /**
     * Update the given resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Rinvex\Fort\Models\Role $role
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
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
        $role->delete();

        return intend([
            'route' => 'rinvex.fort.backend.roles.index',
            'with'  => ['warning' => trans('rinvex/fort::messages.role.deleted', ['roleId' => $role->id])],
        ]);
    }

    /**
     * Show the form for create/update of the given resource.
     *
     * @param string                   $mode
     * @param string                   $action
     * @param \Rinvex\Fort\Models\Role $role
     *
     * @return \Illuminate\Http\Response
     */
    protected function form($mode, $action, Role $role)
    {
        $abilityList = Ability::all()->groupBy('resource')->map(function ($ability) {
            return $ability->pluck('name', 'id');
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
    protected function process(Request $request, Role $role)
    {
        // Prepare required input fields
        $input = $request->all();

        // Save role
        ! $role->exists ? $role = $role->create($input) : $role->update($input);

        // Sync abilities
        if ($request->user($this->getGuard())->can('grant-abilities')) {
            $role->abilities()->sync((array) array_pull($input, 'abilityList'));
        }

        return intend([
            'route' => 'rinvex.fort.backend.roles.index',
            'with'  => ['success' => trans('rinvex/fort::messages.role.saved', ['roleId' => $role->id])],
        ]);
    }
}
