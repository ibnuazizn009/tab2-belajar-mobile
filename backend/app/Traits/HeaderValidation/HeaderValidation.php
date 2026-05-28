<?php

namespace App\Traits\HeaderValidation;

use Carbon\Carbon;
use App;

trait HeaderValidation
{
    public function validateHeaders($timestamp, $signature, $idRegister, $clientKey, $serverKey, $secretKey)
    {
        // Generate the expected signature
        $expectedSignature = $this->generateSignature($idRegister, $clientKey, $serverKey, $secretKey, $timestamp);
        // Check signature
        if ($signature == $expectedSignature) {
            $validTimestamp = strtotime('2004-05-15 00:00:00');
            if ($timestamp == $validTimestamp) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

    public function generateSignature($idRegister, $clientKey, $serverKey, $secretKey, $timestamp)
    {
        $data = $idRegister . $clientKey . $serverKey . $secretKey . $timestamp;
        // Replace 'your-secret-key' with the actual key for encoding/decoding
        return hash_hmac('sha256', $data, $secretKey);
    }
}
