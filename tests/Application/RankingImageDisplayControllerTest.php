<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Tests\Application;

use Jesperbeisner\Fwstats\Application;
use Jesperbeisner\Fwstats\Controller\RankingImageDisplayController;
use Jesperbeisner\Fwstats\Stdlib\Request;
use Jesperbeisner\Fwstats\Stdlib\Response;
use Jesperbeisner\Fwstats\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(RankingImageDisplayController::class)]
final class RankingImageDisplayControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        $this->setUpContainer();
        $this->setUpDatabase();
    }

    public function test_get_request_with_non_existing_world(): void
    {
        $request = new Request(['REQUEST_URI' => '/images/test-ranking.png', 'REQUEST_METHOD' => 'GET'], [], [], [], []);
        $this->getContainer()->set(Request::class, $request);

        $response = (new Application($this->getContainer()))->handle($request);

        $this->assertSame(404, $response->statusCode);
        $this->assertSame('error/error.phtml', $response->template);
        $this->assertNotEmpty($response->vars);
    }

    public function test_get_request_with_afsrv_world(): void
    {
        $request = new Request(['REQUEST_URI' => '/images/afsrv-ranking.png', 'REQUEST_METHOD' => 'GET'], [], [], [], []);
        $this->getContainer()->set(Request::class, $request);

        $response = (new Application($this->getContainer()))->handle($request);

        $this->assertSame(200, $response->statusCode);
        $this->assertSame(Response::CONTENT_TYPE_IMAGE, $response->contentType);
    }

    public function test_get_request_with_chaos_world(): void
    {
        $request = new Request(['REQUEST_URI' => '/images/chaos-ranking.png', 'REQUEST_METHOD' => 'GET'], [], [], [], []);
        $this->getContainer()->set(Request::class, $request);

        $response = (new Application($this->getContainer()))->handle($request);

        $this->assertSame(200, $response->statusCode);
        $this->assertSame(Response::CONTENT_TYPE_IMAGE, $response->contentType);
    }
}
