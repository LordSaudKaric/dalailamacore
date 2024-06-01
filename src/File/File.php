<?php
namespace Dalailama\File;

class File
{
    /**
     * File constructor
     */
    private function __construct(){}

    public static function path($path)
    {
        $path = ROOT_PATH . DS . trim($path, '/');
        $path = str_replace(['/', '\\'], DS, $path);

        return $path;
    }
    public static function exist($path)
    {
        return file_exists(self::path($path));
    }

    public static function require($path)
    {
        return self::exist($path) ? require_once self::path($path) : null;
    }

    public static function include($path)
    {
        return self::exist($path) ? include_once self::path($path) : null;
    }

    public static function require_directory($path)
    {
        $files = array_diff(scandir(self::path($path)), ['.', '..']);

        foreach ($files as $file) {
            $file_path = $path . DS . $file;
            if (self::exist($file_path)) {
                self::require($file_path);
            }
        }
    }
}