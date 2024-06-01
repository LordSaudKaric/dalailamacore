<?php
namespace Dalailama\Session;

class Session
{
    /**
     * Session constructor
     */
    private function __construct(){}

    /**
     * start the session
     * @return void
     */
    public static function start()
    {
        if (! session_id()) {
            ini_set('session.use_trans_sid', 1);
            session_start();
        }
    }

    /**
     * Check that session is set with the given key
     * @param $key
     * @return bool
     */
    public static function has($key)
    {
        return isset($_SESSION[$key]);
    }

    /**
     * Set new Session Key
     * @param $key
     * @param $value
     * @return void
     */
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
     * Get Key from Session if is set
     * @param $key
     * @return mixed|null
     */
    public static function get($key)
    {
        return self::has($key) ? $_SESSION[$key] : null;

    }

    /**
     * Remove the Key from Session
     * @param $key
     * @return void
     */
    public static function remove($key)
    {
        unset($_SESSION[$key]);
    }

    /**
     * Return all session
     * @return array
     */
    public static function all()
    {
        return $_SESSION;
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

    /**
     * Get Session Flash
     * @param $key
     * @return mixed|null
     */
    public static function flash($key)
    {
        $value = null;
        if (self::has($key)) {
            $value = self::get($key);
            self::remove($key);
        }

        return $value;
    }
}