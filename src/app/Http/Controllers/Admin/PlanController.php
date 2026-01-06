<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePlanRequest;
use App\Http\Requests\Admin\UpdatePlanRequest;
use App\Models\Admin\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        return response()->json(Plan::all());
    }

    public function store(StorePlanRequest $request)
    {
        $plan = Plan::create($request->validated());
        return response()->success($plan, 201);
    }

    public function show($id)
    {
        return response()->success(Plan::findOrFail($id));
    }

    public function update(UpdatePlanRequest $request, $id)
    {
        $plan = Plan::findOrFail($id);
        $plan->update($request->validated());
        return response()->success($plan);
    }

    public function destroy($id)
    {
        Plan::findOrFail($id)->delete();
        return response()->success('Plan deleted successfully', 204);
    }
}
