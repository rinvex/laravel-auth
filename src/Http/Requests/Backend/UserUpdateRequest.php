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

class UserUpdateRequest extends FormRequest
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
            'username' => 'required|alpha_dash|max:255|unique:'.config('rinvex.fort.tables.users').',username,'.$this->get('username'),
            'password' => 'required|confirmed|min:'.config('rinvex.fort.passwordreset.minimum_characters'),
            'email'    => 'required|email|max:255|unique:'.config('rinvex.fort.tables.users').',email,'.$this->get('email'),
            'gender'   => 'in:male,female,undisclosed',
        ];
    }
}
