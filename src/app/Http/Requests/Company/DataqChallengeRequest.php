<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class DataqChallengeRequest extends FormRequest
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
        $id = $this->route('dataq_challenge'); // Get ID from route for update exclusion

        return [
            'request_id'    => ['required', 'string', Rule::unique('dataq_challenges')->ignore($id)],
            'incident_id'   => 'required|exists:incidents,id',
            'inspection_id'   => 'required|exists:incidents,id',
            'driver_id'   => 'required|exists:drivers,id',
            'equipment_type_id'      => 'nullable|exists:equipment_types,id',

            'type_id'       => 'required|exists:challenge_types,id',
            'category_id'   => 'required|exists:challenge_categories,id',
            'explanation'   => 'required|string|min:20',
            'internal_notes'=> 'nullable|string',
            'files' =>'required|array',
            'files.*' => ['file','mimes:pdf,png,jpg,jpeg','max:10048'],
//            'report_number' => 'nullable|string|max:50',

//            'state_id'      => 'nullable|string|max:2',

        ];
    }
}
