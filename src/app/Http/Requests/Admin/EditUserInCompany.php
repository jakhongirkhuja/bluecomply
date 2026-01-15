<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class EditUserInCompany extends FormRequest
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
        $userId = $this->route('id'); // получаем id пользователя из route

        return [
            'name' => 'sometimes|required|string|max:255',
            'email' => [
                'sometimes',
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'phone' => [
                'sometimes',
                'required',
                Rule::unique('users', 'phone')->ignore($userId),
            ],
            'password' => 'nullable|min:5',
            'status' => 'required|in:active,inactive',
            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id')->whereNotIn('id', [1, 2]),
            ],
        ];
    }
}
