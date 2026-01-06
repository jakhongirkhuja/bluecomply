<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class UpdateCompanyRequest extends FormRequest
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
        $companyId = $this->route('company'); // id из route

        return [
            'company_name' => 'sometimes|required|string|max:255',
            'tenet_id' => ['sometimes','required','string','max:50', Rule::unique('companies','tenet_id')->ignore($companyId)],
            'dot_number' => ['sometimes','required','string','max:50', Rule::unique('companies','dot_number')->ignore($companyId)],
            'user_id' => 'sometimes|required|exists:users,id',
            'status' => 'required|string|in:active,suspended',
            'der_name' => 'required|string|max:255',
            'der_email' => 'required|email|max:255',
            'der_phone' => 'required|string|max:50',
            'plan_id' => 'required|exists:plans,id',
            'drivers' => 'required|integer|min:0',
        ];
    }
}
