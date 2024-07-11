<?php declare(strict_types=1);
namespace Dalailama\Bootstrap;

use Dalailama\Exception\Ignition;
use Dalailama\File\File;
use Dalailama\Http\Request;
use Dalailama\Http\Response;
use Dalailama\Router\Route;
use Dalailama\Session\Session;

class Application
{
    /**
     * Application constructor
     */
    private function __construct(){}

    /**
     * Run the Application
     * @return void
     */
    public static function run(): void
    {
        // Ignition is a beautiful and customizable
        // error page for PHP applications
        Ignition::handle();
        // Start the session
        Session::start();
        // Handle the request
        Request::handle();
        // Require all routes directory
        File::require_directory('routes');
        // Handle the routes
        $data = Route::handle();
        // Output response
        Response::output($data);
    }
}