<?php

namespace Packaged\Tests\Routing;

use Packaged\Context\Context;
use Packaged\Http\Request;
use Packaged\Routing\Handler\FuncHandler;
use Packaged\Routing\RequestCondition;
use Packaged\Routing\Route;
use PHPUnit\Framework\TestCase;

class RouteTest extends TestCase
{
  public function testRoutePart()
  {
    [$route, $ctx] = $this->_getRoute();

    $route->add(RequestCondition::i()->path('route'));
    $this->assertTrue($route->match($ctx));
  }

  public function testRouteRootRoute()
  {
    [$route, $ctx] = $this->_getRoute();

    $route->add(RequestCondition::i()->path('/route'));
    $this->assertTrue($route->match($ctx));
  }

  public function testRouteRoot()
  {
    [$route, $ctx] = $this->_getRoute();

    $route->add(RequestCondition::i()->path('/'));
    $this->assertTrue($route->match($ctx));
  }

  public function testRoutePort()
  {
    [$route, $ctx] = $this->_getRoute();
    $route->add(RequestCondition::i()->port('8484'));
    $this->assertFalse($route->match($ctx));
  }

  public function testRouteExtra()
  {
    $route = new Route();
    $handler = new FuncHandler(function () { });
    $route->setHandler($handler);

    $ctx = new Context(Request::create('/route_extra'));
    $route->add(RequestCondition::i()->path('/route'));
    $this->assertFalse($route->match($ctx));
  }

  public function testComplete()
  {
    $ctx = new Context(Request::create('/route_extra'));
    $route = Route::i()->add(RequestCondition::i()->path('{pathname}'));
    $route->match($ctx);
    $route->complete($ctx);
    $this->assertEquals('route_extra', $ctx->routeData()->get('pathname'));
  }

  /**
   * @return array
   */
  private function _getRoute(): array
  {
    $route = Route::i();
    $handler = new FuncHandler(function () { });
    $route->setHandler($handler);
    $this->assertSame($handler, $route->getHandler());

    $ctx = new Context(Request::create('/route'));
    $this->assertTrue($route->match($ctx));
    return [$route, $ctx];
  }
}
