<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
class AdminController extends Controller
{
    public function analytics(){
        return Cache::remember('admin.analytics', now()->addMinutes(5), function () {
            $companies = Company::selectRaw("
            COUNT(*) as total,
            COUNT(*) FILTER (WHERE status = 'active') as active,
            COUNT(*) FILTER (WHERE status = 'trial') as trial
        ")->first();

            return [
                'total'  => (int) $companies->total,
                'active' => (int) $companies->active,
                'trial'  => (int) $companies->trial,
                'users'  => User::where('role_id', '!=', 1)->count(),
            ];
        });
    }
}
