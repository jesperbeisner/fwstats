<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Tests\Application;

use Jesperbeisner\Fwstats\Application;
use Jesperbeisner\Fwstats\Stdlib\Request;
use Jesperbeisner\Fwstats\Stdlib\Response;

/**
 * @covers \Jesperbeisner\Fwstats\Controller\NameChangeImageDisplayController
 */
final class NameChangeImageDisplayControllerTest extends AbstractTestCase
{
    public function test_get_request_with_non_existing_world(): void
    {
        $request = new Request(['REQUEST_URI' => '/images/test-name-changes.png', 'REQUEST_METHOD' => 'GET'], [], [], [], []);

        $this->getContainer()->set(Request::class, $request);

        $response = (new Application($this->getContainer()))->handle($request);

        self::assertSame(404, $response->statusCode);
        self::assertSame('error/error.phtml', $response->template);
        self::assertNotEmpty($response->vars);
    }

    public function test_get_request_with_afsrv_world(): void
    {
        $request = new Request(['REQUEST_URI' => '/images/afsrv-name-changes.png', 'REQUEST_METHOD' => 'GET'], [], [], [], []);

        $this->getContainer()->set(Request::class, $request);

        $response = (new Application($this->getContainer()))->handle($request);

        self::assertSame(200, $response->statusCode);
        self::assertSame(Response::CONTENT_TYPE_IMAGE, $response->contentType);
    }

    public function test_get_request_with_chaos_world(): void
    {
        $request = new Request(['REQUEST_URI' => '/images/chaos-name-changes.png', 'REQUEST_METHOD' => 'GET'], [], [], [], []);

        $this->getContainer()->set(Request::class, $request);

        $response = (new Application($this->getContainer()))->handle($request);

        self::assertSame(200, $response->statusCode);
        self::assertSame(Response::CONTENT_TYPE_IMAGE, $response->contentType);
    }
}