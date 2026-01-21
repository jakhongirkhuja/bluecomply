<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VehicleAddRequest extends FormRequest
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
        $vehicleId = $this->input('id');
        return [
            'id'            => 'nullable|exists:vehicles,id',
            'type_id'       => 'required|exists:vehicle_types,id',
            'number'        => 'required|string|max:50',
            'status'        => 'required|in:active,inactive,maintenance,out_of_service',
            'make'          => 'required|string|max:100',
            'model'         => 'required|string|max:100',
            'year'          => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'vin' => [
                'required',
                'string',
                'max:17',
                Rule::unique('vehicles', 'vin')->ignore($vehicleId),
            ],
            'plate'         => 'required|string|max:20',
            'state_id'      => 'required|exists:states,id',
            'expire_at'     => 'required_without:id|date_format:Y-m-d|after:tomorrow',
            'inspection_at' => 'required_without:id|date_format:Y-m-d',
            'files_registration'=> 'required_without:id|array',
            'files_registration.*' => [
                'file',
                'mimes:pdf,png,jpg,jpeg',
                'max:10048'
            ],
            'files_inspection'=> 'required_without:id|array',
            'files_inspection.*' => [
                'required',
                'file',
                'mimes:pdf,png,jpg,jpeg',
                'max:10048'
            ],
        ];
    }
}
