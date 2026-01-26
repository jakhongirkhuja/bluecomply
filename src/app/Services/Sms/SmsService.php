<?php

namespace App\Services\Sms;
use Twilio\Rest\Client;
class SmsService
{
    protected static $client;

    public function __construct()
    {
        self::$client = new Client(env('TWILIO_SID'), env('TWILIO_TOKEN'));
    }

    public static function sendSms($to, $message)
    {
        if (self::$client === null) {
            self::$client = new Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
        }

        self::$client->messages->create($to, [
            'from' => env('TWILIO_PHONE_NUMBER'),
            'body' => 'Your verification code is ' . $message . '. Please do not share this code with anyone.',
        ]);
    }
}
