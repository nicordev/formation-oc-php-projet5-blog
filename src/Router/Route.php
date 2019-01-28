<?php

namespace Application\Router;

class Route
{
    public $controller = '';
    public $method = '';
    public $params = [];

    /**
     * Route constructor.
     * @param string $controller
     * @param string $method
     * @param array $params
     */
    public function __construct(string $controller, string $method, array $params = [])
    {
        $this->controller = $controller;
        $this->method = $method;
        $this->params = $params;
    }
}