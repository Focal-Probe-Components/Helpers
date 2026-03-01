<?php
use Probe\Attributes\FocalOnly;
use Probe\Attributes\Anywhere;


// PHP 8.5 ARRAY FUNCTIONS FOR PHP VERSIONS LOWER THAN 8.5
if (version_compare(PHP_VERSION, "8.5.0", "<")){

    function array_first(array $array):mixed{
        return array_values($array)[0];
    }
    function array_last(array $array):mixed{
        return array_values($array)[count($array) -  1];
    }
}


if (class_exists(Focal\Core\Kernel::class)){
    #[FocalOnly]
    /**
     * Helper function to access the App instance
     * * You can use this method: `Only in a Focal App`
     * 
     * @return Focal\Core\Kernel
     */
    function app(): Focal\Core\Kernel{
        return Focal\Core\Kernel::getInstance();
    }

    /**
     * @param string $name
     * @return string|false Returns the file path of a stub or false if it does not exist.
     */
    function stub(string $name){
        return match(true){
            app()->doesStubExist($name) => app()->getStubPath($name),
            default => false,
        };
    }
}

#[Anywhere]
/**
 * Helper function to fetch Environment variables
 * * Can be run: `Anywhere`
 * @param string $key
 * @throws Exception
 * @return int|bool|string
 */
function env(string $key): int|bool|string{
    // If the function is being called in a Focal App
    if (function_exists("app")){
        if (!(app()->booted())){
            throw new Exception("Cannot fetch environment variable value because the App is not booted/ bootstrapped." . 'Run app()->boot()');
        }
    }
    if (!array_key_exists(key: $key, array: $_ENV)){
        throw new Exception("$key is not a valid environment variable");
    }
    return $_ENV[$key];
}



/**
 * Returns the currently set value from config/app.php or any other config file in App/config/
 * * Can be run: `Only in a Focal App`
 * @param string $key
 * @param string $config The name of the config you want to fetch from, i.e `app` => `config/app.php`
 * @throws Exception
 * @return string|null
 */
#[FocalOnly]
function config(string $key, string $config = "app"): string|null{
    if (!function_exists("app")){
        throw new Exception(__METHOD__ . " Cannot be run independently, You need a valid Focal App");
    }
    if (!file_exists(app()->basePath() . "/config/{$config}.php")){
        throw new Exception("Config file config/{$config}.php does not exist.");
    }
    $config = require_once "../config/{$config}.php";
    return $config[$key] ?? NULL;
}


#[FocalOnly]
/**
 * Helper function to include a view file
 * @param string $view `partials.auth.index` = `views/partials/auth/index`
 * @return void
 */
function view(string $view): void{
    include_once app()->basePath() . "/views/" . str_replace(search: ".", replace: "/", subject: $view) . ".php";
}