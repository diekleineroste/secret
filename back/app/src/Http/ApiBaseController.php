<?php

namespace Http;

abstract class ApiBaseController
{
    protected ?array $httpBody;

    public function __construct()
    {
        $this->httpBody = json_decode(file_get_contents('php://input'), true);

        // CORS: API response can be shared with javascript code from origin ALLOW_ORIGIN
        header('Access-Control-Allow-Origin: ' . ALLOW_ORIGIN);

        // set the Content-type header of the HTTP response to JSON
        header('Content-type: application/json; charset=UTF-8');
    }

    protected function message(int $httpCode, string $message = null, string $data = null)
    {
        http_response_code($httpCode);
        if ($httpCode > 199 && $httpCode < 300)
            $success = true;
        else
            $success = false;

        if ($message && $data) $answer = ['success' => $success, 'message' => $message, 'data' => $data];
        else if ($message) $answer = ['success' => $success, 'message' => $message];
        else if ($data) $answer = ['success' => $success, 'data' => $data];
        else $answer = ['success' => $success];

        echo json_encode($answer);
    }

    public function methodNotAllowed()
    {
        $this->message(405, 'HTTP request method ' .  $_SERVER['REQUEST_METHOD'] . ' not allowed.');
    }
}
