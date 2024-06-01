<?php
namespace Dalailama\Router;

use Dalailama\Http\Request;
use Dalailama\Exception\BadFunctionCallException;
use Dalailama\Exception\BadMethodCallException;
use Dalailama\Exception\InvalidArgumentException;
use Dalailama\Exception\InvalidCallbackException;

class Route
{
    protected static $rourtes = [];
    protected static $prefix = '';
    protected static $middleware = '';

    private function __construct(){}

    private static function add($method, $uri, $callback)
    {
        $uri = rtrim(self::$prefix . '/' . trim($uri, '/'), '/');
        $uri = $uri?:'/';

        self::$rourtes[] = [
            'uri'        => $uri,
            'callback'   => $callback,
            'method'     => $method,
            'middleware' => static::$middleware
        ];
    }
    public static function get($uri, $callback)
    {
        self::add('GET', $uri, $callback);
    }

    public static function post($uri, $callback)
    {
        self::add('POST', $uri, $callback);
    }

    public static function put($uri, $callback)
    {
        self::add('PUT', $uri, $callback);
    }

    public static function delete($uri, $callback)
    {
        self::add('DELETE', $uri, $callback);
    }

    public static function prefix($prefix, $callback)
    {
        $parent_prefix = self::$prefix;
        self::$prefix .= '/' . trim($prefix, '/');

        if (is_callable($callback)) {
            call_user_func($callback);
        } else {
            throw new InvalidCallbackException('Please provide valida callback function');
        }

        self::$prefix = $parent_prefix;
    }

    public static function middleware($middleware, $callback)
    {
        $parent_middleware = self::$middleware;
        self::$middleware .= '|' . trim(ucfirst($middleware), '|');

        if (is_callable($callback)) {
            call_user_func($callback);
        } else {
            throw new InvalidCallbackException('Please provide valida callback function');
        }

        self::$middleware = $parent_middleware;
    }

    public static function handle()
    {
        $uri    = Request::url();
        $method = Request::post('__METHOD') ?? Request::method();

        foreach (self::$rourtes as $route) {
            $matched = true;
            $route['uri'] = preg_replace('/\/{(.*?)}/', '/(.*?)', $route['uri']);
            $route['uri'] = '#^' . $route['uri'] . '$#';

            if (preg_match($route['uri'], $uri, $matches)) {
                array_shift($matches);
                $params = array_values($matches);

                foreach ($params as $param) {
                    if (strpos($param, '/')) {
                        $matched = false;
                    }
                }

                if ($route['method'] !== $method) {
                    $matched = false;
                }

                if ($matched === true) {
                    return self::invoke($route, $params);
                }
            }
        }

        return static::error();
    }

    public static function error($code = '404')
    {
        $controller = 'App\\Controllers\\ErrorController';
        $action     = '_' . $code;
        http_response_code($code);
        return call_user_func_array([new $controller, $action], []);
    }

    private static function invoke($route, $params = [])
    {
        self::executeMiddleware($route);

        $callback = $route['callback'];

        if (is_callable($callback)) {
            return call_user_func_array($callback, $params);
        } elseif(str_contains($callback, '@')) {
            list($controller, $action) = explode('@', $callback);

            $controller = 'App\\Controllers\\' . ucfirst($controller) . 'Controller';

            if (class_exists($controller)) {
                $obj = new $controller();

                if (method_exists($obj, $action)) {
                    return call_user_func_array([$obj, $action], $params);
                } else {
                    throw new BadMethodCallException(
                        sprintf("The method: '%s' does not exists at class: '%s'",
                            $action, $controller)
                    );
                }
            } else {
                throw new BadFunctionCallException(
                    sprintf("Class: '%s' does not exists", $controller)
                );
            }
        } else {
            throw new InvalidArgumentException("Please provide valid callback function!");
        }
    }

    private static function executeMiddleware($route)
    {
        foreach (explode('|', $route['middleware']) as $middleware) {
            if ($middleware != '') {

                $middleware = "App\\Middlewares\\" . ucfirst($middleware) . 'Middleware';

                if (class_exists($middleware)) {
                    $obj = new $middleware();
                    call_user_func_array([$obj, 'handle'], []);
                } else {
                    throw new BadFunctionCallException(
                        sprintf("Class: '%s' does not exists", $middleware)
                    );
                }
            }
        }
    }
}