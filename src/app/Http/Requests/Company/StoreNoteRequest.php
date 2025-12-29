<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class StoreNoteRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'user_id' => 'required|array',
            'user_id.*' => 'integer|exists:users,id',
            'priority' => 'required|in:low,medium,high',
            'show_at' => 'required|date|date_format:Y-m-d',
            'driver_id' => 'required|exists:drivers,id',
        ];
    }
}
