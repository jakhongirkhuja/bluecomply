<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Model;

class DrugTestResult extends Model
{
    protected $fillable = [
        'drug_test_order_id',
        'result',
        'pdf_path',
        'reported_at',
    ];
}
