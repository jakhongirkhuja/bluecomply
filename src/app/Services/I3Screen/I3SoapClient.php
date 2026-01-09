<?php

namespace App\Services\I3Screen;

use Illuminate\Support\Facades\Http;

class I3SoapClient
{
    public function send(string $xml)
    {
        return Http::withHeaders([
            'Content-Type' => 'text/xml; charset=utf-8',
        ])->post(config('i3.endpoint'), $xml)->body();
    }
}
