<?php

namespace App;

use Symfony\Component\Yaml\Yaml;
use App\Exception\InvalidRouteException;
use App\Exception\InvalidActionException;
use Symfony\Component\Dotenv\Dotenv;

class Router
{

    /**
     * All the routes listed in the .yaml file
     *
     * @var array
     */
    private $routesConfig = [];

    /**
     * The current route parameters is the parsing is true
     *
     * @var array
     */
    private $currentRoute = [];

    public function __construct()
    {
        $this->routesConfig =  Yaml::parseFile(__DIR__ . '/config/routes.yaml');
        $dotenv = new Dotenv();
        $dotenv->loadEnv(__DIR__ . '/../.env');
    }

    /**
     * parse the URL as string
     *
     * @return string
     */
    private function parseUrl(): string
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * Check if the url is declared in the configuration file
     *
     * @param string $url
     * @return boolean
     */
    private function isInConfig(string $url) : bool
    {
        $routes = $this->routesConfig['routes'];
        foreach ($routes as $routeParams) {
            if ($routeParams['path'] === $url) {
                $this->currentRoute = $routeParams;
                return true;
            }
        }
        return false;
    }

    private function paramResolver(array $currentRoute, string $url)
    {
        //Check if the parameters match with the corresponding type in .yaml

    }

    /**
     * Dispatch the request to the correct controller & method
     *
     * @param array $routeConfig
     * @return void
     */
    public function dispatch(array $routeConfig)
    {
        $action = explode('::',$routeConfig['action']);
        try {
            if (count($action) !== 2 || $action === false) {
                throw new InvalidActionException('Your action must be on format ControllerName::methodName');
            }
            else {
                $controllerName = $_ENV['CONTROLLER_NAMESPACE'] . '\\' . $action[0];
                $method = $action[1];

                $controller = new $controllerName();
                $controller->$method();
            }
        } catch (InvalidActionException $e) {
            echo $e->getMessage();
            exit;
        }
    }

    /**
     * Method to call the Router in your app.
     *
     * @return void
     */
    public function run()
    {
        $url = $this->parseUrl();

        try{
            if ($this->isInConfig($url)) {
                $this->dispatch($this->currentRoute);
            } else {
                throw new InvalidRouteException('This route doesn\t exist, please check your configuration in routes.yaml');
            }
        }catch(InvalidRouteException $e) {
           echo $e->getMessage();
           exit;
        }
    }
}
