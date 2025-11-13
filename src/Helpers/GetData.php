<?php

namespace App\Helpers;

class GetData {
    public static function getJson(string $url, string $method = 'GET', array $data = [], array $headers = []): array {
        $ch = curl_init();

        $method = strtoupper($method);

        // se for GET e tiver dados, adiciona na URL
        if ($method === 'GET' && !empty($data)) {
            $url .= '?' . http_build_query($data);
        }

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_TIMEOUT => 30,
        ]);

        // se não for GET, envia os dados no corpo
        if ($method !== 'GET' && !empty($data)) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        // define os cabeçalhos
        $defaultHeaders = ['Content-Type: application/json'];
        if (!empty($headers)) {
            $headers = array_merge($defaultHeaders, $headers);
        } else {
            $headers = $defaultHeaders;
        }

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
}
