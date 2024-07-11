<?php declare(strict_types=1);
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
    public static function has($key): bool
    {
        return isset($_COOKIE[$key]);
    }

    /**
     * Set new Cookie Key
     * @param $key
     * @param $value
     * @return void
     */
    public static function set($key, $value): void
    {
        setcookie($key, (string)$value, strtotime( '+2 days' ), "/", "", false, true);
    }

    /**
     * Get Key from Cookie if is set
     * @param $key
     * @return mixed|null
     */
    public static function get($key): mixed
    {
        return self::has($key) ? $_COOKIE[$key] : null;

    }

    /**
     * Remove Key from Cookie
     * @param $key
     * @return void
     */
    public static function remove($key): void
    {
        setcookie($key, '', -1, "/");
    }

    /**
     * Return all Cookie
     * @return array
     */
    public static function all(): array
    {
        return $_COOKIE;
    }

    /**
     * Destroy all session
     * @return void
     */
    public static function destroy(): void
    {
        foreach (self::all() as $key => $value) {
            self::remove($key);
        }
    }
}