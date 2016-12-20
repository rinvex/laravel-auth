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

class PasswordResetRequest extends FormRequest
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
        return $this->isMethod('post') ? [
            'token'    => 'required|regex:/^[0-9a-zA-Z]+$/',
            'email'    => 'required|email|max:255|exists:'.config('rinvex.fort.tables.users').',email',
            'password' => 'required|confirmed|min:'.config('rinvex.fort.passwordreset.minimum_characters'),
        ] : [
            'token' => 'required|regex:/^[0-9a-zA-Z]+$/',
            'email' => 'required|email|max:255|exists:'.config('rinvex.fort.tables.users').',email',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getRedirectUrl()
    {
        if ($this->isMethod('post')) {
            return parent::getRedirectUrl();
        }

        return route('rinvex.fort.frontend.passwordreset.request');
    }
}
