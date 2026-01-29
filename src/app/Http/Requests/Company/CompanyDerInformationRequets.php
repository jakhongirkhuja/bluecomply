<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class CompanyDerInformationRequets extends FormRequest
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
        $company_id = $this->route('company_id');
        return [
            'der_name'=>'required',
            'der_last_name'=>'required',
            'der_email'=>'required|email|unique:companies,der_email,'.$company_id,
            'der_phone'=>'required|numeric|unique:companies,der_phone,'.$company_id,
            'der_address'=>'required',
            'der_alternative_phone'=>'required|numeric',
        ];
    }
}
