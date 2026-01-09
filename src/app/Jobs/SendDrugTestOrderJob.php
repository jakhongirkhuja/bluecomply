<?php

namespace App\Jobs;

use App\Models\Company\AuditLog;
use App\Models\Company\DrugTestOrder;
use App\Services\I3Screen\I3OrderXmlBuilder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class SendDrugTestOrderJob implements ShouldQueue
{
    use Queueable;
    public $tries = 5;
    public $backoff = [60, 300, 900];
    /**
     * Create a new job instance.
     */
    public function __construct(public DrugTestOrder $order)
    {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $xml = I3OrderXmlBuilder::build($this->order,);

        $response = Http::timeout(30)
            ->withHeaders(['Content-Type' => 'text/xml'])
            ->post(config('i3.endpoint'), $xml);

        if (!$response->successful()) {
            throw new \Exception('i3 order failed');
        }

        AuditLog::log(
            subject: $this->order,
            action: 'ORDER_SENT',
            details: 'Order successfully sent to i3'
        );
    }
    public function failed(Throwable $e)
    {
        AuditLog::log(
            subject: $this->order,
            action: 'ORDER_FAILED',
            details: $e->getMessage()
        );
    }
}
