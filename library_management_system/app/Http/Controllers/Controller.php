<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function jsonResponse(string $status, string $message, int $code = 200, array $data = [])
    {
        $response = [
            'status' => $status,
            'message' => $message,
        ];

        if (!empty($data)) {
            $response['data'] = $data;
        }

        return response()->json($response, $code);
    }
}
