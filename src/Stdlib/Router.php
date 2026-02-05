<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Stdlib;

use FastRoute\Dispatcher as DispatcherInterface;
use Jesperbeisner\Fwstats\Controller\MethodNotAllowedController;
use Jesperbeisner\Fwstats\Controller\NotFoundController;
use Jesperbeisner\Fwstats\Interface\ControllerInterface;
use Jesperbeisner\Fwstats\Interface\RouterInterface;

final readonly class Router implements RouterInterface
{
    public function __construct(
        private DispatcherInterface $dispatcher,
    ) {
    }

    public function route(Request $request): void
    {
        /** @var array{int, class-string<ControllerInterface>, array<string, string>} $routeInfo */
        $routeInfo = $this->dispatcher->dispatch($request->getHttpMethod(), $request->getUri());

        if ($routeInfo[0] === DispatcherInterface::NOT_FOUND) {
            $request->setController(NotFoundController::class);
            return;
        }

        if ($routeInfo[0] === DispatcherInterface::METHOD_NOT_ALLOWED) {
            $request->setController(MethodNotAllowedController::class);
            // TODO: Display allowed methods
            // $allowedMethods = $routeInfo[1];

            return;
        }

        $request->setController($routeInfo[1]);
        $request->setRouteParameters($routeInfo[2]);
    }
}
