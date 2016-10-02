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

class RolesController extends AuthorizedController
{
    /**
     * {@inheritdoc}
     */
    protected $resourceAbilityMap = [
        'assign' => 'assign',
        'remove' => 'remove',
    ];

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
     *
     * @return void
     */
    public function __construct(RoleRepositoryContract $roleRepository)
    {
        parent::__construct();

        $this->authorizeResource(Role::class);

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

        return view('rinvex.fort::backend.roles.index', compact('roles'));
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function show($id)
    {
        if (! $role = $this->roleRepository->find($id)) {
            return intend([
                'intended'   => route('rinvex.fort.backend.roles.index'),
                'withErrors' => ['rinvex.fort.role.not_found' => trans('rinvex.fort::backend/messages.role.not_found', ['role' => $id])],
            ]);
        }

        $actions   = ['view', 'create', 'edit', 'delete', 'import', 'export'];
        $resources = app('rinvex.fort.ability')->findAll()->groupBy('resource');
        $columns   = ['resource', 'view', 'create', 'edit', 'delete', 'import', 'export', 'other'];

        return view('rinvex.fort::backend.roles.show', compact('role', 'resources', 'actions', 'columns'));
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
        return $this->form('create', 'store');
    }

    /**
     * Show the form for copying the given resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function copy($id)
    {
        return $this->form('copy', 'store', $id);
    }

    /**
     * Show the form for editing the given resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return $this->form('edit', 'update', $id);
    }

    /**
     * Show the form for create/edit/copy of the given resource.
     *
     * @param string   $mode
     * @param string   $action
     * @param int|null $id
     *
     * @return \Illuminate\Http\Response
     */
    protected function form($mode, $action, $id = null)
    {
        if (! $role = $this->roleRepository->getModelInstance($id)) {
            return intend([
                'intended'   => route('rinvex.fort.backend.roles.index'),
                'withErrors' => ['rinvex.fort.role.not_found' => trans('rinvex.fort::backend/messages.role.not_found', ['role' => $id])],
            ]);
        }

        $resources = app('rinvex.fort.ability')->findAll()->groupBy('resource');

        return view('rinvex.fort::backend.roles.form', compact('role', 'resources', 'mode', 'action'));
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
        dd($request->all());
        //
    }

    /**
     * Update the given resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int                      $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Delete the given resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
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
}
