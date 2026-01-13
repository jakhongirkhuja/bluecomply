<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\ScheduleDrugTestRequest;
use App\Models\Company\Company;
use App\Models\Company\DrugTestOrder;
use App\Models\Driver\Driver;
use App\Services\I3Screen\I3OrderXmlBuilder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DrugTestOrderController extends Controller
{
    protected function safe(callable $callback)
    {
        try {
            return $callback();
        } catch (\Throwable $e) {
            report($e);

            return response()->error(
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
    public function show(DrugTestOrder $drug_alcohol){
        dd('sds');
        return response()->success($drug_alcohol);
    }
    public function store(ScheduleDrugTestRequest $request)
    {
        $data = $request->validated();
        return $this->safe(function () use ($request) {

            $data = $request->validated();

            return DB::transaction(function () use ($data) {

                $company_id = Company::where('user_id', auth()->id())
                    ->value('id');

                $driver = Driver::with(['license', 'personal_information'])
                    ->where('company_id', $company_id)
                    ->where('id', $data['driver_id'])
                    ->firstOrFail();

                $order = DrugTestOrder::create([
                    ...$data,
                    'company_id'   => $company_id,
                    'reference_id' => Str::uuid(),
                ]);

                $xml = I3OrderXmlBuilder::build($order, $driver);

//                $response = Http::withHeaders([
//                    'Content-Type' => 'text/xml',
//                ])->post(config('i3.endpoint'), $xml)->body();

//                $parsed = simplexml_load_string($response);
                // $case = (string)$parsed->BackgroundReportPackage->ProviderReferenceId->IdValue;
                $case = '1123SAFFAS';

                $order->update([
                    'i3_case_number' => $case,
                ]);

                return response()->success([
                    'order_id'        => $order->id,
                    'i3_case_number'  => $case,
                    'scheduling_url'  => $this->schedulingUrl($case),
                ], Response::HTTP_CREATED);
            });
        });

    }

    private function schedulingUrl(string $case)
    {
        $token = Str::random(20);
        $hash = md5(
            base64_encode(
                sha1(
                    config('i3.password') .
                    $token .
                    config('i3.user') .
                    $case,
                    true
                )
            )
        );

        return "https://schedule.i3screen.net/Applicant?s={$case}&t={$token}&h={$hash}";
    }
}
