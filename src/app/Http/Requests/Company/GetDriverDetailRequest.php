<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $categoryMap = [
            'notes' => ['manual', 'completed'],
            'tasks' => ['onboarding', 'compliance', 'completed'],
            'documents' => ['1','2','3','4','5'],
            'drugandalcohol' => ['compliance'],
            'clearinghouse' => ['compliance'],
            'employment' => ['internal','outgoing','incoming'],
            'incidents' => ['manual'],
            'systemlog' => [],
        ];

        return [
            'category' => [
                'required',
                'string',
                Rule::in(array_keys($categoryMap)),
            ],

            'under_category' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($categoryMap) {
                    $category = request('category');

                    if (
                        isset($categoryMap[$category]) &&
                        !in_array($value, $categoryMap[$category], true)
                    ) {
                        $fail("The {$attribute} is invalid for category {$category}.");
                    }
                },
            ],
        ];
    }
    public function validationData()
    {
        return $this->query();
    }
}
