<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class AssignToAssetRequet extends FormRequest
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
        $companyId = $this->route('company_id');

        return [
            'insurances' => 'required|array',
            'insurances.*' => [
                'required',
                'numeric',
                Rule::exists('insurances', 'id')->where(function ($query) use ($companyId) {
                    return $query->where('company_id', $companyId);
                }),
            ],
            'vehicles' => 'required|array',
            'vehicles.*' => [
                'required',
                'numeric',
                Rule::exists('vehicles', 'id')->where(function ($query) use ($companyId) {
                    return $query->where('company_id', $companyId);
                }),
            ],
        ];
    }
}
