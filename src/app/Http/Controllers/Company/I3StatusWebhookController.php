<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\AuditLog;
use App\Models\Company\DrugTestOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class I3StatusWebhookController extends Controller
{
    public function __invoke(Request $request)
    {
        Log::info('i3 status webhook', [
            'payload' => $request->getContent()
        ]);
        $xml = simplexml_load_string($request->getContent());

        $caseNumber = (string) $xml
            ->BackgroundReportPackage
            ->ProviderReferenceId
            ->IdValue;

        $status = (string) $xml
            ->Screenings
            ->Screening
            ->ScreeningStatus
            ->OrderStatus;

        $order = DrugTestOrder::where('i3_case_number', $caseNumber)->first();

        if (!$order) {
            return response()->xml(['return' => 0]);
        }

        $order->update([
            'status' => $this->mapStatus($status)
        ]);

        AuditLog::log(
            subject: $order,
            action: 'STATUS_UPDATE',
            details: "i3 status changed to {$status}"
        );

        return response()->xml(['return' => 1]);
    }

    private function mapStatus(string $status): string
    {
        return match ($status) {
            'ORDERED' => 'created',
            'SCHEDULED' => 'scheduled',
            'IN_PROGRESS' => 'in_progress',
            'COMPLETE' => 'completed',
            default => 'created'
        };
    }
}
