<?php
namespace Dalailama\Cookie;

class Cookie
{
    /**
     * Cookie constructor
     */
    private function __construct(){}

    /**
     * Check that Cookie is set with the given key
     * @param $key
     * @return bool
     */
    public static function has($key)
    {
        return isset($_COOKIE[$key]);
    }

    /**
     * Set new Cookie Key
     * @param $key
     * @param $value
     * @return void
     */
    public static function set($key, $value)
    {
        setcookie($key, $value, strtotime( '+2 days' ), "/", "", false, true);
    }

    /**
     * Get Key from Cookie if is set
     * @param $key
     * @return mixed|null
     */
    public static function get($key)
    {
        return self::has($key) ? $_COOKIE[$key] : null;

    }

    /**
     * Remove Key from Cookie
     * @param $key
     * @return void
     */
    public static function remove($key)
    {
        setcookie($key, '', '-1', "/");
    }

    /**
     * Return all Cookie
     * @return array
     */
    public static function all()
    {
        return $_COOKIE;
    }

    /**
     * Destroy all session
     * @return void
     */
    public static function destroy()
    {
        foreach (self::all() as $key => $value) {
            self::remove($key);
        }
    }
}