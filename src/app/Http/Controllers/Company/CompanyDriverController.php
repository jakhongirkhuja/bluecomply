<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Driver\Driver;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CompanyDriverController extends Controller
{
    public function __construct()
    {
    }
    public function getDrivers(){
        $drivers = Driver::with(['license','med','drugTest'])->latest()->paginate();
        return response()->success($drivers, Response::HTTP_OK);
    }
}
