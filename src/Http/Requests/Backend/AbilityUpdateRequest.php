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

namespace Rinvex\Fort\Http\Requests\Backend;

use Rinvex\Support\Http\Requests\FormRequest;

class AbilityUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'action'   => 'required|unique:'.config('rinvex.fort.tables.abilities').',action,'.$this->get('id').',id,resource,'.$this->get('resource'),
            'resource' => 'required|unique:'.config('rinvex.fort.tables.abilities').',resource,'.$this->get('id').',id,action,'.$this->get('action'),
            'title'    => 'required',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'action.unique'   => 'The combination of (action & resource) fields has already been taken.',
            'resource.unique' => 'The combination of (action & resource) fields has already been taken.',
        ];
    }
}
