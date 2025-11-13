<?php

namespace App\Helpers;

class GetData {
    public static function getJson(string $url, string $method = 'GET', array $data = [], array $headers = []): array {
        $ch = curl_init();

        $method = strtoupper($method);

        if ($method === 'GET' && !empty($data)) {
            $url .= '?' . http_build_query($data);
        }

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_TIMEOUT => 30,
        ]);

        if ($method !== 'GET' && !empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $defaultHeaders = ['Content-Type: application/json'];
        $headers = !empty($headers) ? array_merge($defaultHeaders, $headers) : $defaultHeaders;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($error) {
            return [
                'status' => 'error',
                'message' => $error,
                'http_code' => $httpCode,
            ];
        }

        return [
            'status' => 'success',
            'data' => json_decode($response, true),
        ];
    }

    public static function getXml(string $url, string $method = 'GET', array $data = [], array $headers = []): array {
        $ch = curl_init();

        $method = strtoupper($method);

        if ($method === 'GET' && !empty($data)) {
            $url .= '?' . http_build_query($data);
        }

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_TIMEOUT => 30,
        ]);

        if ($method !== 'GET' && !empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        $defaultHeaders = ['Accept: application/xml', 'Content-Type: application/x-www-form-urlencoded'];
        $headers = !empty($headers) ? array_merge($defaultHeaders, $headers) : $defaultHeaders;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($error) {
            return [
                'status' => 'error',
                'message' => $error,
                'http_code' => $httpCode,
            ];
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($response);
        if ($xml === false) {
            return [
                'status' => 'error',
                'message' => 'Falha ao parsear XML',
                'http_code' => $httpCode,
            ];
        }

        return [
            'status' => 'success',
            'data' => json_decode(json_encode($xml), true),
        ];
    }
}
