<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Tests\Application;

use Jesperbeisner\Fwstats\Application;
use Jesperbeisner\Fwstats\Controller\LogoutController;
use Jesperbeisner\Fwstats\Interface\SessionInterface;
use Jesperbeisner\Fwstats\Stdlib\Request;
use Jesperbeisner\Fwstats\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(LogoutController::class)]
final class LogoutControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        $this->setUpContainer();
        $this->setUpDatabase();
    }

    public function test_get_request(): void
    {
        $request = new Request(['REQUEST_URI' => '/logout', 'REQUEST_METHOD' => 'GET'], [], [], [], []);
        $this->getContainer()->set(Request::class, $request);

        $response = (new Application($this->getContainer()))->handle($request);

        $this->assertSame(302, $response->statusCode);
        $this->assertSame('/', $response->location);
    }

    public function test_get_request_deletes_session(): void
    {
        $request = new Request(['REQUEST_URI' => '/logout', 'REQUEST_METHOD' => 'GET'], [], [], [], []);
        $this->getContainer()->set(Request::class, $request);

        $session = $this->getContainer()->get(SessionInterface::class);
        $session->set('test', 'test');

        $this->assertSame('test', $session->get('test'));

        $response = (new Application($this->getContainer()))->handle($request);

        $this->assertSame(302, $response->statusCode);
        $this->assertSame('/', $response->location);
        $this->assertNull($session->get('test'));
    }
}
