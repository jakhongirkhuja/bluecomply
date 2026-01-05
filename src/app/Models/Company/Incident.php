<?php

namespace App\Models\Company;

use App\Models\Driver\Driver;
use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{

    protected $fillable = [
        'type',
        'driver_id',
        'company_id',
        'date',
        'time',

        'street',
        'city',
        'state_id',
        'zip',

        'description',

        'dot_reportable',
        'injuries',
        'injury_types',
        'at_fault',
        'preventable',
        'fatalities',

        'third_party_required',
        'third_party_name',
        'third_party_contact',
        'third_party_notes',

        'tow_required',
        'towing_company_name',
        'towing_company_contact',
        'towing_company_address',

        'police_involved',
        'police_report_number',
        'hazmat_release',

        'damage_category',
        'accident_description',

        'damage_category_id',
        'specific_category_id',

        'post_accident_test',
        'test_explanation',
        'truck',
        'truck_id',
        'truck_unit_number',
        'truck_make',
        'truck_vin',
        'truck_plate',
        'truck_plate_state_id',

        'trailer',
        'trailer_id',
        'trailer_unit_number',
        'trailer_make',
        'trailer_vin',
        'trailer_plate',
        'trailer_plate_state_id',

        'citation_category_id',
        'issuing_agency_id',
        'citation_number',
        'citation_notes',
        'citation_amount',
        'officer_name',
        'court_date',
        'lawyer_hired',
        'lawyer_name',
        'lawyer_contact',
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i',

        'dot_reportable' => 'boolean',
        'injuries' => 'boolean',
        'at_fault' => 'boolean',
        'preventable' => 'boolean',
        'fatalities' => 'boolean',

        'third_party_required' => 'boolean',
        'tow_required' => 'boolean',
        'police_involved' => 'boolean',
        'hazmat_release' => 'boolean',

        'post_accident_test' => 'boolean',

        'injury_types' => 'array',
        'damage_category' => 'array',
        'citation_amount' => 'decimal:2',
        'court_date' => 'date',
        'lawyer_hired' => 'boolean',

    ];
    protected $appends = [
        'identifier_formatted',
    ];
    protected $hidden = [
        'identifier'
    ];
    public function getIdentifierFormattedAttribute(): string
    {
        return 'IN-' . $this->identifier;
    }
    protected static function boot()
    {
        parent::boot();

        // Перед созданием модели
        static::creating(function ($incident) {
            if (!$incident->identifier) {
                $incident->identifier = self::generateIdentifier();
            }
        });
    }

    protected static function generateIdentifier(): int
    {
        $last = self::latest('identifier')->first();
        return $last ? $last->identifier + 1 : 3958;
    }
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
    public function files(){
        return $this->hasMany(IncidentFile::class);
    }
}
