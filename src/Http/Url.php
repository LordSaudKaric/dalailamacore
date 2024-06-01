<?php
namespace Dalailama\Http;

class Url
{
    /**
     * Url constructor
     */
    private function __construct(){}

    public static function path($path)
    {
        return Request::baseUrl() . '/' . trim($path, '/');
    }

    public static function previous()
    {
        return Request::previous();
    }

    public static function redirect($url) {
        header("Location: $url");
        exit();
    }
}