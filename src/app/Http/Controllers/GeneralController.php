<?php

namespace App\Http\Controllers;

use App\Models\Company\Cdlclass;
use App\Models\Company\DocumentCategory;
use App\Models\Company\DocumentType;
use App\Models\Company\DotAgency;
use App\Models\Company\DrugTestResult;
use App\Models\Company\InspectionLevel;
use App\Models\Company\RejectionReason;
use App\Models\Company\Role;
use App\Models\Company\ViolationCategory;
use App\Models\Driver\Vehicle;
use App\Models\General\Cities;
use App\Models\General\ComplianceCategory;
use App\Models\General\DamageCategory;
use App\Models\General\EndorsementType;
use App\Models\General\EquipmentType;
use App\Models\General\RestrictionType;
use App\Models\General\States;
use App\Models\General\VehicleDocumentType;
use App\Models\General\VehicleInsuranceType;
use App\Models\General\VehicleMaintenanceType;
use App\Models\General\VehicleType;
use Illuminate\Http\Request;

class GeneralController extends Controller
{
    public function getData(Request $request)
    {
        $type = $request->query('type');

        $allowed = [
            'states',
            'state-cities',
            'endorsements',
            'equipments',
            'document-categories',
            'document-class',
            'documents',
            'damage-categories',
            'dot-agencies',
            'inspection-levels',
            'rejection-reason',
            'violation-categories',
            'drug-test-reasons',
            'inspection_levels',
            'vehicles',
            'vehicle_types',
            'roles',
            'vehicle_document_types',
            'vehicle_insurance_types',
            'vehicle_maintenance_types',
            'compliance_category',
            'restriction_types'
        ];

        if (!in_array($type, $allowed)) {
            return response()->json(['error' => 'Invalid type'], 422);
        }

        $method = $this->resolveMethod($type);
        if (!method_exists($this, $method)) {
            return response()->json(['error' => 'Method not implemented'], 501);
        }

        return response()->json($this->$method());
    }

    private function resolveMethod($type)
    {
        $type = str_replace(['-', '_'], ' ', $type);
        $parts = explode(' ', $type);
        $method = lcfirst(implode('', array_map('ucfirst', $parts)));
        return $method;
    }

    private function roles(){
        return Role::whereNotIn('id', [1,2])->get();
    }
    private function states()
    {
        return States::all();
    }

    private function stateCities()
    {
        return Cities::all();
    }

    private function endorsements()
    {
        return EndorsementType::all();
    }

    private function equipments()
    {
        return EquipmentType::all();
    }

    private function documentCategories()
    {
        return DocumentCategory::all();
    }

    private function documentClass()
    {
        return Cdlclass::all();
    }

    private function documents()
    {
        return DocumentType::all();
    }

    private function damageCategories()
    {
        return DamageCategory::all();
    }

    private function dotAgencies()
    {
        return DotAgency::all();
    }

    private function inspectionLevels()
    {
        return InspectionLevel::all();
    }

    private function rejectionReason()
    {
        return RejectionReason::all();
    }

    private function violationCategories()
    {
        return ViolationCategory::all();
    }

    private function drugTestReasons()
    {
        return DrugTestResult::all();
    }

    private function vehicleTypes()
    {
        return VehicleType::all();
    }
    private function vehicleDocumentTypes()
    {
        return VehicleDocumentType::all();
    }
    private function vehicleInsuranceTypes()
    {
        return VehicleInsuranceType::all();
    }
    private function vehicleMaintenanceTypes()
    {
        return VehicleMaintenanceType::all();
    }
    private function complianceCategory(){
        return ComplianceCategory::all();
    }
    private function restrictionTypes(){
        return RestrictionType::all();
    }
}
