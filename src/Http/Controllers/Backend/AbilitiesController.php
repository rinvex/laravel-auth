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
use Rinvex\Fort\Models\Ability;
use Rinvex\Fort\Contracts\AbilityRepositoryContract;
use Rinvex\Fort\Http\Controllers\AuthorizedController;

class AbilitiesController extends AuthorizedController
{
    /**
     * {@inheritdoc}
     */
    protected $resourceAbilityMap = [
        'give'   => 'give',
        'revoke' => 'revoke',
    ];

    /**
     * The ability repository instance.
     *
     * @var \Rinvex\Fort\Contracts\AbilityRepositoryContract
     */
    protected $abilityRepository;

    /**
     * Create a new abilities controller instance.
     *
     * @return void
     */
    public function __construct(AbilityRepositoryContract $abilityRepository)
    {
        parent::__construct();

        $this->authorizeResource(Ability::class);

        $this->abilityRepository = $abilityRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $abilities = $this->abilityRepository->paginate(config('rinvex.fort.backend.items_per_page'));

        return view('rinvex.fort::backend.abilities.index', compact('abilities'));
    }

    /**
     * Display the specified resource.
     *
     * @param \Rinvex\Fort\Models\Ability $ability
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function show(Ability $ability)
    {
        return view('rinvex.fort::backend.abilities.show', compact('ability'));
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
        return $this->form('create', 'store', $this->abilityRepository->createModel());
    }

    /**
     * Show the form for copying the given resource.
     *
     * @param \Rinvex\Fort\Models\Ability $ability
     *
     * @return \Illuminate\Http\Response
     */
    public function copy(Ability $ability)
    {
        return $this->form('copy', 'store', $ability);
    }

    /**
     * Show the form for editing the given resource.
     *
     * @param \Rinvex\Fort\Models\Ability $ability
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Ability $ability)
    {
        return $this->form('edit', 'update', $ability);
    }

    /**
     * Show the form for create/edit/copy of the given resource.
     *
     * @param string                      $mode
     * @param string                      $action
     * @param \Rinvex\Fort\Models\Ability $ability
     *
     * @return \Illuminate\Http\Response
     */
    protected function form($mode, $action, Ability $ability)
    {
        return view('rinvex.fort::backend.abilities.form', compact('ability', 'resources', 'mode', 'action'));
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
        //
    }

    /**
     * Update the given resource in storage.
     *
     * @param \Illuminate\Http\Request    $request
     * @param \Rinvex\Fort\Models\Ability $ability
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Ability $ability)
    {
        //
    }

    /**
     * Delete the given resource from storage.
     *
     * @param \Rinvex\Fort\Models\Ability $ability
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Ability $ability)
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
