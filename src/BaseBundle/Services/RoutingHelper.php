<?php

namespace BaseBundle\Services;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Router;

/**
 * base.routing.helper.
 */
class RoutingHelper
{
    protected $router;
    protected $cache = [];

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function getCurrentRoute(Request $request)
    {
        $pathInfo = $request->getPathInfo();
        $hash     = sha1(var_export($pathInfo, true));
        if (in_array($hash, $this->cache)) {
            return $this->cache[$hash];
        }

        $routeParams = $this->router->match($pathInfo);
        $routeName   = $routeParams['_route'];
        if (substr($routeName, 0, 1) === '_') {
            return;
        }
        unset($routeParams['_route']);

        $data = [
            'name'   => $routeName,
            'params' => $routeParams,
        ];

        $this->cache[$hash] = $data;

        return $data;
    }
}
