<?php

namespace App\Helpers;

use Midtrans\Config;
use Midtrans\ApiRequestor;

class MidtransHelper
{
    /**
     * Custom API request with SSL fix for development
     */
    public static function post($url, $server_key, $data_hash)
    {
        $curl = curl_init();
        
        $payload = json_encode($data_hash);
        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Basic ' . base64_encode($server_key . ':')
        ];
        
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            // Disable SSL verification for development
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
        ]);
        
        $result = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        
        if ($result === false) {
            $error = curl_error($curl);
            curl_close($curl);
            throw new \Exception('CURL Error: ' . $error);
        }
        
        curl_close($curl);
        
        $result_array = json_decode($result, true);
        
        if ($http_code != 200 && $http_code != 201) {
            $message = 'Midtrans Error';
            if (isset($result_array['error_messages'])) {
                $message = is_array($result_array['error_messages']) 
                    ? implode(', ', $result_array['error_messages'])
                    : $result_array['error_messages'];
            }
            throw new \Exception($message, $http_code);
        }
        
        return $result_array;
    }
    
    /**
     * Get Snap Token with SSL fix
     */
    public static function getSnapToken($params)
    {
        $serverKey = Config::$serverKey;
        
        // Validasi server key tidak kosong
        if (empty($serverKey)) {
            throw new \Exception('Midtrans Server Key tidak ditemukan. Silakan setup di file .env');
        }
        
        $url = Config::getSnapBaseUrl() . '/transactions';
        
        return self::post($url, $serverKey, $params);
    }
}
