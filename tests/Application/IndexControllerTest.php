<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Tests\Application;

use Jesperbeisner\Fwstats\Application;
use Jesperbeisner\Fwstats\Controller\IndexController;
use Jesperbeisner\Fwstats\Stdlib\Request;
use Jesperbeisner\Fwstats\Stdlib\Response;
use Jesperbeisner\Fwstats\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(IndexController::class)]
final class IndexControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        $this->setUpContainer();
        $this->setUpDatabase();
    }

    public function test_get_request(): void
    {
        $request = new Request(['REQUEST_URI' => '/', 'REQUEST_METHOD' => 'GET'], [], [], [], []);
        $this->getContainer()->set(Request::class, $request);

        $response = (new Application($this->getContainer()))->handle($request);

        $this->assertSame(200, $response->statusCode);
        $this->assertSame(Response::CONTENT_TYPE_HTML, $response->contentType);
        $this->assertSame('index/index.phtml', $response->template);
        $this->assertNotEmpty($response->vars);
    }

    public function test_get_request_with_available_page(): void
    {
        $request = new Request(['REQUEST_URI' => '/', 'REQUEST_METHOD' => 'GET'], ['page' => '1'], [], [], []);
        $this->getContainer()->set(Request::class, $request);

        $response = (new Application($this->getContainer()))->handle($request);

        $this->assertSame(200, $response->statusCode);
        $this->assertSame(Response::CONTENT_TYPE_HTML, $response->contentType);
        $this->assertSame('index/index.phtml', $response->template);
        $this->assertNotEmpty($response->vars);
    }

    public function test_get_request_with_not_available_page(): void
    {
        $request = new Request(['REQUEST_URI' => '/', 'REQUEST_METHOD' => 'GET'], ['page' => '999'], [], [], []);
        $this->getContainer()->set(Request::class, $request);

        $response = (new Application($this->getContainer()))->handle($request);

        $this->assertSame(404, $response->statusCode);
        $this->assertSame(Response::CONTENT_TYPE_HTML, $response->contentType);
        $this->assertSame('error/error.phtml', $response->template);
        $this->assertNotEmpty($response->vars);
    }

    public function test_get_request_with_negative_page(): void
    {
        $request = new Request(['REQUEST_URI' => '/', 'REQUEST_METHOD' => 'GET'], ['page' => '-999'], [], [], []);
        $this->getContainer()->set(Request::class, $request);

        $response = (new Application($this->getContainer()))->handle($request);

        $this->assertSame(404, $response->statusCode);
        $this->assertSame(Response::CONTENT_TYPE_HTML, $response->contentType);
        $this->assertSame('error/error.phtml', $response->template);
        $this->assertNotEmpty($response->vars);
    }
}
