<?php
namespace Dalailama\Exception;

class Ignition
{
    /**
     * Error Handler: Ignition constructor
     */
    private function __construct(){}

    /**
     * Error handler
     * @return void
     */
    public static function handle()
    {
        if (DEBUG) {
            switch (ENV) {
                case 'local':
                case 'testing':
                case 'development':
                    \Spatie\Ignition\Ignition::make()
                        ->useDarkMode()
                        ->register();
                    break;
                case 'production':
                    // Turn off all error reporting
                    error_reporting(0);
                    echo "This should not happen!";
                    break;
            }
        } else {
            // Turn off all error reporting
            error_reporting(0);
            echo "This should not happen!";
            exit();
        }
    }
}