<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class StoreIncidentBasicRequest extends FormRequest
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
            'type' => 'required|string|in:accident,citations,inspections,claims,other_damage,other_incidents',
            'driver_id' => 'required|exists:drivers,id',
            'date' => 'required|date|date_format:Y-m-d',
            'time' => 'nullable|date_format:h:i A',

            'location' => 'required|string|in:driver,manual',
            'street' => 'required_if:location,manual|string|max:255',
            'city' => 'required_if:location,manual|string|max:255',
            'state_id' => 'required_if:location,manual|numeric|exists:states,id',
            'zip' => 'required_if:location,manual|string|max:20',

            'truck'=>'required|string|in:assets,manual',
            'truck_id'=>'required_if:truck,assets|numeric|exists:vehicles,id',
            'truck_unit_number'=>'required_if:truck,manual|string',
            'truck_make'=>'required_if:truck,manual|string',
            'truck_vin'=>'required_if:truck,manual|string|unique:vehicles,vin',
            'truck_plate'=>'required_if:truck,manual|string',
            'truck_plate_state_id'=>'required_if:truck,manual|numeric|exists:states,id',


            'trailer'=>'required|string|in:assets,manual',
            'trailer_id'=>'required_if:trailer,assets|numeric|exists:vehicles,id',
            'trailer_unit_number'=>'required_if:truck,manual|string',
            'trailer_make'=>'required_if:truck,manual|string',
            'trailer_vin'=>'required_if:truck,manual|string',
            'trailer_plate'=>'required_if:truck,manual|string',
            'trailer_plate_state_id'=>'required_if:truck,manual|numeric|exists:states,id',


        ];
    }
}
