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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->form('create', 'store', new Ability());
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
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->process($request, new Ability());
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
        $ability->delete();

        return intend([
            'route' => 'rinvex.fort.backend.abilities.index',
            'with'  => ['warning' => trans('rinvex/fort::messages.ability.deleted', ['abilityId' => $ability->id])],
        ]);
    }

    /**
     * Show the form for create/update of the given resource.
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
    protected function process(Request $request, Ability $ability)
    {
        // Prepare required input fields
        $input = $request->all();

        // Verify valid policy
        if (! empty($input['policy']) && (($class = strstr($input['policy'], '@', true)) === false || ! method_exists($class, str_replace('@', '', strstr($input['policy'], '@'))))) {
            return intend([
                'back' => true,
                'withInput'  => $request->all(),
                'withErrors'  => ['policy' => trans('rinvex/fort::messages.ability.invalid_policy')],
            ]);
        }

        // Save ability
        ! $ability->exists ? $ability = $ability->create($input) : $ability->update($input);

        return intend([
            'route' => 'rinvex.fort.backend.abilities.index',
            'with'  => ['success' => trans('rinvex/fort::messages.ability.saved', ['abilityId' => $ability->id])],
        ]);
    }
}
