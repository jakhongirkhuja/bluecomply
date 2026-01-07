<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoadSideInspectionRequest extends FormRequest
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
            'id'=>'nullable|exists:incidents,id',
            'driver_id' => 'required|exists:drivers,id',

            'report_number'=>'required|string|max:255',
            'date' => 'required|date|date_format:Y-m-d',
            'time'=>'nullable|date_format:h:i A',
            'time_end'=>'nullable|date_format:h:i A',
            'state_id' => 'required|numeric|exists:states,id',
            'shipper_name'=>'required|string|max:255',
            'inspection_level_id'=>'required|numeric|exists:inspection_levels,id',
            'accident_related'=>'required|numeric|between:0,1',
            'violations_exist'=>'required|numeric|between:0,1',
            'violations'=>'required_if:violations_exist,1|array',
            'violations.*.code'=>'required|string|max:250',
            'violations.*.unit'=>'required|string|in:Driver,Vehicle',
            'violations.*.description'=>'required|string',
            'violations.*.violation_category_id'=>'required|numeric|exists:violation_categories,id',
            'violations.*.violation_oos'=>'nullable|numeric|between:0,1',
            'violations.*.violation_corrected'=>'nullable|numeric|between:0,1',
        ];
    }
}
