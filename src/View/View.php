<?php
namespace Dalailama\View;

use Dalailama\File\File;
use Dalailama\Session\Session;
use Jenssegers\Blade\Blade;

class View
{
    /**
     * View constructor
     */
    private function __construct(){}
    public static function render($path, $data = [], $type = 'blade')
    {
        $errors = Session::get('errors');
        $old = Session::get('old');
        $data = array_merge($data, ['errors' => $errors, 'old' => $old]);

        $render = $type . 'Render';

        return static::$render($path, $data);
    }
    public static function bladeRender($path, $data = [])
    {
        $blade = new Blade(File::path('/views'), File::path('/storage/cache'));
        return $blade->make($path, $data)->render();
    }
    public static function contentRender($path, $data = [])
    {
        $path = 'views' . DS . str_replace(['/', '\\', '.', '|', '#', '@'], DS, $path) . '.php';

        if (! File::exist($path)) {
            throw new \Exception(
                sprintf("The view file '%s' does not exists", $path)
            );
        }

        ob_start();
        extract($data);
        include File::path($path);
        $content = ob_get_contents();
        ob_get_clean();
        return $content;
    }
}