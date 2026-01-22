<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class AddUserToCompany extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'=>'required',
            'email'=>'required|email|unique:users,email',
            'phone'=>'required|numeric|unique:users,phone',
            'password'=>'required|min:5',
            'status' => 'required|in:active,inactive',
            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')->whereNotIn('id', [1, 2]),
            ],

        ];
    }
}
