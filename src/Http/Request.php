<?php

namespace Dalailama\Http;

class Request
{
    protected static $url;
    protected static $base_url;
    protected static $full_url;
    protected static $query_string;
    protected static $script_name;

    /**
     * Request constructor
     */
    private function __construct(){}

    /**
     * Set the BaseUrl
     * @return void
     */
    private static function setBaseUrl()
    {
        $protocol    = Server::get('REQUEST_SCHEME') . '://';
        $host        = Server::get('HTTP_HOST');
        $script_name = static::$script_name;

        static::$base_url = "$protocol$host$script_name";
    }

    /**
     * Parse the request
     * set full url
     * set url and query string
     * @return void
     */
    private static function setUrl()
    {
        $request_uri = urldecode(Server::get('REQUEST_URI'));
        $request_uri = str_replace(static::$script_name, '', $request_uri);
        $request_uri = rtrim($request_uri, '/');

        $parsed_url = parse_url($request_uri);

        static::$full_url = $request_uri;
        static::$url = $parsed_url['path'] ?:'/';
        static::$query_string = $parsed_url['query'] ?? '';
    }

    /**
     * Handle the Request
     * @return void
     */
    public static function handle()
    {
        static::$script_name = trim(dirname(Server::get('SCRIPT_NAME')), DS);
        static::setBaseUrl();
        static::setUrl();
    }

    /**
     * Return baseUrl
     * @return mixed
     */
    public static function baseUrl()
    {
        return static::$base_url;
    }

    /**
     * Return the fullUrl
     * @return mixed
     */
    public static function fullUrl()
    {
        return static::$full_url;
    }

    /**
     * Return the url/uri
     * @return mixed
     */
    public static function url()
    {
        return static::$url;
    }

    /**
     * Return the queryString
     * @return mixed
     */
    public static function queryString()
    {
        return static::$query_string;
    }

    /**
     * Return the scriptName
     * @return mixed
     */
    public static function scriptName()
    {
        return static::$script_name;
    }

    /**
     * Return Request method type
     * @return mixed|null
     */
    public static function method()
    {
        return Server::get('REQUEST_METHOD');
    }

    /**
     * Get All Get values
     * @param $key
     * @return mixed|null
     */
    public static function get($key)
    {
        return self::value($key, $_GET);
    }

    /**
     * Get All Posted values
     * @param $key
     * @return mixed|nullž
     */
    public static function post($key)
    {
        return self::value($key, $_POST);
    }

    /**
     * Check if the Request type has the given key
     * @param $type
     * @param $key
     * @return bool
     */
    public static function has($type, $key)
    {
        return array_key_exists($key, $type);
    }

    /**
     * Get Value from the request type by the givne key
     * @param $key
     * @param $type
     * @return mixed|null
     */
    public static function value($key, $type = null)
    {
        $type = $type ?? $_REQUEST;
        return self::has($type, $key) ? $type[$key] : null;
    }

    /**
     * Set Value in the Request
     * @param $key
     * @param $value
     * @return void
     */
    public static function set($key, $value)
    {
        $_REQUEST[$key] = $value;
        $_POST[$key]    = $value;
        $_GET[$key]     = $value;
    }

    /**
     * Get previous value
     * @return mixed|null
     */
    public static function previous()
    {
        return Server::get('HTTP_REFERER');
    }

    /**
     * Get All values
     * @return array
     */
    public static function all()
    {
        return $_REQUEST;
    }
}