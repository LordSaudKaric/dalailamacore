<?php
namespace Dalailama\Http;

class Response
{
    /**
     * Response constructor
     */
    private function __construct(){}

    public static function output($data, $code = 200, $type = 'text/html')
    {
        if (!$data) return;

        if (! is_string($data)) {
            $data = json_encode($data);
        }
        header("Content-Type: $type");
        http_response_code($code);
        echo $data;
    }

    public static function json($data, $code = 200, $type = 'application/json')
    {
        if (!$data) return;
        header("Content-Type: $type");
        http_response_code($code);
        echo json_encode($data);
    }
}