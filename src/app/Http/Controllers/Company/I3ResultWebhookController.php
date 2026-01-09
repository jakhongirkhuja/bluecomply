<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\AuditLog;
use App\Models\Company\DrugTestOrder;
use App\Models\Company\DrugTestResult;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class I3ResultWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        Log::info('i3 result webhook', [
            'payload' => $request->getContent()
        ]);
        $xml = simplexml_load_string($request->getContent());

        $caseNumber = (string) $xml
            ->BackgroundReportPackage
            ->ProviderReferenceId
            ->IdValue;

        $order = DrugTestOrder::where('i3_case_number', $caseNumber)->first();

        if (!$order) {
            return response()->xml(['return' => 0]);
        }

        $result = (string) $xml
            ->Screenings
            ->Screening
            ->ScreeningStatus
            ->Result;

        $pdfBase64 = (string) $xml
            ->BackgroundReportPackage
            ->ReportImages
            ->ReportImage
            ->Image;

        $pdf = base64_decode($pdfBase64);
        $path = "i3/results/{$caseNumber}.pdf";

        Storage::put($path, $pdf);

        DrugTestResult::create([
            'drug_test_order_id' => $order->id,
            'result' => $result,
            'pdf_path' => $path,
            'reported_at' => now(),
        ]);

        $order->update(['status' => 'completed']);

        AuditLog::log(
            subject: $order,
            action: 'RESULT_RECEIVED',
            details: "Final result: {$result}"
        );

        return response()->xml(['return' => 1]);
    }
}
