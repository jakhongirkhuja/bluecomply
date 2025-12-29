<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class GetDriverDetailRequest extends FormRequest
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
            'category'=>'string|in:notes,tasks,documents,drugandalcohol,clearinghouse,employment,incidents,systemlog',
            'under_category'=>'string|in:onboarding,compliance,manual,completed'
        ];
    }
    public function validationData()
    {
        return $this->query();
    }
}
