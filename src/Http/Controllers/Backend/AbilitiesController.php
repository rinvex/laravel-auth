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
use Rinvex\Fort\Http\Controllers\AuthorizedController;
use Rinvex\Fort\Http\Requests\Backend\AbilityStoreRequest;
use Rinvex\Fort\Http\Requests\Backend\AbilityUpdateRequest;

class AbilitiesController extends AuthorizedController
{
    /**
     * {@inheritdoc}
     */
    protected $resource = 'abilities';

    /**
     * {@inheritdoc}
     */
    protected $resourceActionWhitelist = ['grant'];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $abilities = Ability::paginate(config('rinvex.fort.backend.items_per_page'));

        return view('rinvex/fort::backend/abilities.index', compact('abilities'));
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
        return view('rinvex/fort::backend/abilities.show', compact('ability'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->form('create', 'store', new Ability());
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
     * Store a newly created resource in storage.
     *
     * @param \Rinvex\Fort\Http\Requests\Backend\AbilityStoreRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(AbilityStoreRequest $request)
    {
        return $this->process($request);
    }

    /**
     * Update the given resource in storage.
     *
     * @param \Rinvex\Fort\Http\Requests\Backend\AbilityUpdateRequest $request
     * @param \Rinvex\Fort\Models\Ability                             $ability
     *
     * @return \Illuminate\Http\Response
     */
    public function update(AbilityUpdateRequest $request, Ability $ability)
    {
        return $this->process($request, $ability);
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
        $result = $ability->delete();

        return intend([
            'route' => 'rinvex.fort.backend.abilities.index',
            'with'  => ['warning' => trans('rinvex/fort::backend/messages.ability.deleted', ['abilityId' => $result->id])],
        ]);
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
        return view('rinvex/fort::backend/abilities.form', compact('ability', 'resources', 'mode', 'action'));
    }

    /**
     * Process the form for store/update of the given resource.
     *
     * @param \Illuminate\Http\Request    $request
     * @param \Rinvex\Fort\Models\Ability $ability
     *
     * @return \Illuminate\Http\Response
     */
    protected function process(Request $request, Ability $ability = null)
    {
        // Store data into the entity
        $input = $request->only($ability->getFillable());
        $result = is_null($ability) ? Ability::create($input) : $ability->update($input);

        // Repository `store` method returns false if no attributes
        // updated, happens save button clicked without chaning anything
        $with = ! is_null($ability)
            ? ($result === false
                ? ['warning' => trans('rinvex/fort::backend/messages.ability.nothing_updated', ['abilityId' => $ability->id])]
                : ['success' => trans('rinvex/fort::backend/messages.ability.updated', ['abilityId' => $result->id])])
            : ['success' => trans('rinvex/fort::backend/messages.ability.created', ['abilityId' => $result->id])];

        return intend([
            'route' => 'rinvex.fort.backend.abilities.index',
            'with'  => $with,
        ]);
    }
}
