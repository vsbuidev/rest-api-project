<?php
class ApiResponse
{
    public static function sendResponse($status_code, $message, $data = null)
    {
        http_response_code($status_code);
        $response = array(
            'status' => $status_code < 300,
            'message' => $message,
        );
        if ($data !== null) {
            $response['data'] = $data;
        }
        echo json_encode($response);
    }
}
