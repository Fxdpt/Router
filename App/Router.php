<?php

namespace App;

use Symfony\Component\Yaml\Yaml;
use App\Exception\InvalidRouteException;

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
        foreach($routes as $routeName => $routeParams){
            if ($routeParams['path'] === $url) {
                $this->currentRoute[$routeName] = $routeParams;
                return true;
            }
        }
        return false;
    }

    /**
     * Dispatch the request to the correct controller & method
     *
     * @param array $routeConfig
     * @return void
     */
    private function dispatch(array $routeConfig)
    {
        dump($routeConfig);
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