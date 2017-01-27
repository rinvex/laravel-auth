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

namespace Rinvex\Fort\Http\Requests\Frontend;

use Rinvex\Support\Http\Requests\FormRequest;

class UserSettingsUpdateRequest extends FormRequest
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
     * Process given request data before validation.
     *
     * @param array $data
     *
     * @return array
     */
    public function process($data)
    {
        if (empty($data['password'])) {
            unset($data['password'], $data['password_confirmation']);
        }

        return array_filter_recursive(array_trim_recursive($data));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email'    => 'required|email|max:255|unique:'.config('rinvex.fort.tables.users').',email,'.$this->user()->id,
            'username' => 'required|max:255|unique:'.config('rinvex.fort.tables.users').',username,'.$this->user()->id,
            'phone'    => 'required|numeric|unique:'.config('rinvex.fort.tables.users').',phone,'.$this->user()->id,
            'password' => 'sometimes|required|min:6|confirmed',
        ];
    }
}
