<?php

namespace Webkul\Pos\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * UserForm FormRequest
 *
 * @author Vivek Sharma <viveksh047@webkul.com> @vivek-webkul
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class UserForm extends FormRequest
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
        $this->rules = [
            'outlet_id' => 'required',
            'username'  => 'required',
            'password'  => 'nullable|confirmed',
            'firstname' => 'required',
            'lastname'  => 'required',
            'email'     => 'email|unique:pos_users,email',
            'status'    => 'sometimes'
        ];

        if ($this->method() == 'PUT') {
            $this->rules['email'] = 'email|unique:pos_users,email,' . $this->route('id');
        }

        return $this->rules;
    }
}
