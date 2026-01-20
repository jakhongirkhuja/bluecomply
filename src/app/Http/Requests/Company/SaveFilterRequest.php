<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;
class SaveFilterRequest extends FormRequest
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
            'id'   => ['nullable', 'exists:saved_filters,id'],
            'name' => ['required_without:id', 'string', 'max:100'],
            'filters' => ['required', 'array'],
            'filters.status' => ['sometimes', 'array'],
            'filters.status.*' => ['in:active,home,do_not_dispatch'],

            'filters.compliance' => ['sometimes', 'array'],
            'filters.compliance.*' => ['in:green,orange,red'],

            'filters.state' => ['sometimes', 'array'],
            'filters.state.*' => ['numeric', 'exists:states,id'],

            'filters.mrv_monitoring' => ['sometimes', 'in:all,active,inactive'],

            'filters.clearinghouse' => [
                'sometimes',
                'in:not_sent,consent_pending,refused'
            ],
            'filters.tags' => ['sometimes', 'array'],
            'filters.tags.*' => ['integer', 'exists:driver_tags,id']
        ];
    }
}
