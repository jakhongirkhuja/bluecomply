<?php

namespace App\Http\Requests\Driver;

use Illuminate\Foundation\Http\FormRequest;

class DriverTagRequest extends FormRequest
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
        if ($this->isMethod('post')) {

            return [
                'driver_id' => 'required|exists:drivers,id',
                'tag'       => 'required|array',
                'tag.*'     => 'string|max:200',
            ];
        }
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return [
                'driver_id' => 'required|exists:drivers,id',
                'tag'       => 'required|string|max:200',
            ];
        }

        return [];
    }
}
