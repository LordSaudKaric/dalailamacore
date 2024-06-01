<?php
namespace Dalailama\Http;

class Server
{
    /**
     * Server constructor
     */
    private function __construct(){}

    /**
     * Get All server variables
     * @return array
     */
    public static function all()
    {
        return $_SERVER;
    }

    /**
     * Check that the server has the key
     * @param $key
     * @return bool
     */
    public static function has($key)
    {
        return array_key_exists($key, $_SERVER);
    }

    /**
     * Get the value from the Server by the key
     * @param $key
     * @return mixed|null
     */
    public static function get($key)
    {
        return self::has($key) ? $_SERVER[$key] : null;
    }

    /**
     * Get the path info of the path
     * @param $path
     * @return array|string
     */
    public static function path_info($path)
    {
        return pathinfo($path);
    }
}