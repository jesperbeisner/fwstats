<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Tests\Application;

use DateTimeImmutable;
use Jesperbeisner\Fwstats\Application;
use Jesperbeisner\Fwstats\Controller\RequestLogController;
use Jesperbeisner\Fwstats\Interface\SessionInterface;
use Jesperbeisner\Fwstats\Model\User;
use Jesperbeisner\Fwstats\Stdlib\Request;
use Jesperbeisner\Fwstats\Stdlib\Response;
use Jesperbeisner\Fwstats\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(RequestLogController::class)]
final class RequestLogControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        $this->setUpContainer();
        $this->setUpDatabase();
    }

    public function test_get_request_without_login(): void
    {
        $request = new Request(['REQUEST_URI' => '/admin/request-logs', 'REQUEST_METHOD' => 'GET'], [], [], [], []);
        $this->getContainer()->set(Request::class, $request);

        $response = (new Application($this->getContainer()))->handle($request);

        $this->assertSame(302, $response->statusCode);
        $this->assertSame('/login', $response->location);
    }

    public function test_get_request_with_login(): void
    {
        $request = new Request(['REQUEST_URI' => '/admin/request-logs', 'REQUEST_METHOD' => 'GET'], [], [], [], []);
        $this->getContainer()->set(Request::class, $request);

        $user = new User(1, 'test', 'test', 'test', 'test', new DateTimeImmutable());
        $this->getContainer()->get(SessionInterface::class)->setUser($user);

        $response = (new Application($this->getContainer()))->handle($request);

        $this->assertSame(200, $response->statusCode);
        $this->assertSame(Response::CONTENT_TYPE_HTML, $response->contentType);
        $this->assertSame('request-logs/request-logs.phtml', $response->template);
        $this->assertNotEmpty($response->vars);
    }
}
