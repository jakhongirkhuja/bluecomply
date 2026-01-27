<?php

namespace App\Http\Requests\Company;

use Illuminate\Foundation\Http\FormRequest;

class StoreClaimRequest extends FormRequest
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
            'incident_id'=>'required|exists:incidents,id',
            'driver_id'=>'required|exists:drivers,id',
            'claims' => ['required', 'array', 'min:1'],
            'claims.*.type' => [
                'required',
                'in:liability,physical damage,cargo,subrogation,trailer interchange,other'
            ],
            'claims.*.other_type' => [
                'required_if:claims.*.type,Other',
                'nullable',
                'string',
                'max:255'
            ],
            'claims.*.claim_number'     => ['required', 'string', 'max:100'],
            'claims.*.carrier_name'   => ['required', 'string', 'max:100'],
            'claims.*.adjuster_name'     => ['required', 'string', 'max:150'],
            'claims.*.adjuster_contact'  => ['required', 'string', 'max:150'],
            'claims.*.status' => [
                'required',
                'in:open,reviewing,paid,denied,closed'
            ],
            'claims.*.deductible_amount' => ['required', 'numeric', 'min:0'],
            'claims.*.insurance_paid'    => ['required', 'numeric', 'min:0'],

            // 🔹 Liability
            'claims.*.opposing_party_name' => [
                'required_if:claims.*.type,Liability',
                'nullable',
                'string',
                'max:255'
            ],
            'claims.*.opposing_party_insurance' => [
                'required_if:claims.*.type,Liability',
                'nullable',
                'string',
                'max:255'
            ],

            // 🔹 Physical Damage & Trailer Interchange
            'claims.*.repair_vendor_name' => [
                'required_if:claims.*.type,Physical Damage,Trailer Interchange',
                'nullable',
                'string',
                'max:255'
            ],

            // 🔹 Cargo
            'claims.*.shipper_name' => [
                'required_if:claims.*.type,Cargo',
                'nullable',
                'string',
                'max:255'
            ],
            'claims.*.damage_type' => [
                'required_if:claims.*.type,Cargo',
                'nullable',
                'string',
                'max:255'
            ],
            'claims.*.cargo_value' => [
                'required_if:claims.*.type,Cargo',
                'nullable',
                'numeric',
                'min:0'
            ],
            'claims.*.cargo_loss_amount' => [
                'required_if:claims.*.type,Cargo',
                'nullable',
                'numeric',
                'min:0'
            ],

            // 🔹 Subrogation
            'claims.*.internal_claim_number' => [
                'required_if:claims.*.type,Subrogation',
                'nullable',
                'string',
                'max:255'
            ],
            'claims.*.opposing_carrier_name' => [
                'required_if:claims.*.type,Subrogation',
                'nullable',
                'string',
                'max:255'
            ],

            'claims.*.description' => ['required', 'string', 'max:3000'],

            // 📎 Files per claim
            'claims.*.files' => ['nullable', 'array'],
            'claims.*.files.*' => [
                'file',
                'mimes:jpg,jpeg,png,pdf,doc,docx',
                'max:10240'
            ],
        ];
    }
}
