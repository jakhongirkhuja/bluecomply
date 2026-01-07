<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class StoreIncidentCitationRequest extends FormRequest
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
            'type' => 'required|string|in:citations,citations2',
            'driver_id' => 'required|exists:drivers,id',
            'date' => 'required|date|date_format:Y-m-d',
            'time' => 'nullable|date_format:h:i A',

            'location' => 'required|string|in:driver,manual',
            'street' => 'required_if:location,manual|string|max:255',
            'city' => 'required_if:location,manual|string|max:255',
            'state_id' => 'required_if:location,manual|string|max:50',
            'zip' => 'required_if:location,manual|string|max:20',

            'truck'=>'required|string|in:assets,manual',
            'truck_id'=>'required_if:truck,assets|numeric|exists:vehicles,id',
            'truck_unit_number'=>'required_if:truck,manual|string',
            'truck_make'=>'required_if:truck,manual|string',
            'truck_vin'=>'required_if:truck,manual|string',
            'truck_plate'=>'required_if:truck,manual|string',
            'truck_plate_state_id'=>'required_if:truck,manual|numeric|exists:states,id',


            'trailer'=>'required|string|in:assets,manual',
            'trailer_id'=>'required_if:trailer,assets|numeric|exists:vehicles,id',
            'trailer_unit_number'=>'required_if:truck,manual|string',
            'trailer_make'=>'required_if:truck,manual|string',
            'trailer_vin'=>'required_if:truck,manual|string',
            'trailer_plate'=>'required_if:truck,manual|string',
            'trailer_plate_state_id'=>'required_if:truck,manual|numeric|exists:states,id',

            'citation_category_id' => 'required|numeric|exists:citation_categories,id',
            'citation_notes' => 'required|string|max:2000',
            'citation_number' => 'required|string|max:100',
            'issuing_agency_id' => 'required|exists:agencies,id',
            'citation_amount' => 'required|numeric|min:0|max:999999.99',
            'officer_name' => 'required|string|max:150',
            'court_date' => 'required|date|date_format:Y-m-d',
            'lawyer_hired' => 'required|numeric|between:0,1',
            'lawyer_name' => 'nullable|required_if:lawyer_hired,1|string|max:150',
            'lawyer_contact' => 'nullable|required_if:lawyer_hired,1|string|max:150',

        ];
    }
}
