<?php

namespace App\Jobs;

use App\Services\I3Screen\RandomDrawService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RunRandomDraw implements ShouldQueue
{
    use Queueable;
    public int $companyId;
    public string $service;
    public int $count;

    public function __construct(int $companyId, string $service, int $count)
    {
        $this->companyId = $companyId;
        $this->service = $service;
        $this->count = $count;
    }

    public function handle(): void
    {
        $service = new RandomDrawService();
        $service->run($this->companyId, $this->service, $this->count);
    }
}
