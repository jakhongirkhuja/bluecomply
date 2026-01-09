<?php

namespace Database\Seeders;

use App\Models\Company\DotAgency;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DotAgencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $agencies = [
            ['code' => 'FMCSA', 'name' => 'Federal Motor Carrier Safety Administration'],
            ['code' => 'FAA',   'name' => 'Federal Aviation Administration'],
            ['code' => 'FRA',   'name' => 'Federal Railroad Administration'],
            ['code' => 'FTA',   'name' => 'Federal Transit Administration'],
            ['code' => 'PHMSA', 'name' => 'Pipeline and Hazardous Materials Safety Administration'],
            ['code' => 'USCG',  'name' => 'United States Coast Guard'],
        ];

        foreach ($agencies as $agency) {
            DotAgency::updateOrCreate(['code' => $agency['code']], $agency);
        }
    }
}
