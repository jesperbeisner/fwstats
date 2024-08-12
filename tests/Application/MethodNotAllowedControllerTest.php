<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Tests\Application;

use Jesperbeisner\Fwstats\Controller\MethodNotAllowedController;
use Jesperbeisner\Fwstats\Stdlib\Request;
use Jesperbeisner\Fwstats\Stdlib\Response;
use Jesperbeisner\Fwstats\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(MethodNotAllowedController::class)]
final class MethodNotAllowedControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        $this->setUpContainer();
        $this->setUpDatabase();
    }

    public function test_get_request(): void
    {
        $request = new Request(['REQUEST_URI' => '/', 'REQUEST_METHOD' => 'DELETE'], [], [], [], []);
        $response = (new MethodNotAllowedController())->execute($request);

        $this->assertSame(405, $response->statusCode);
        $this->assertSame(Response::CONTENT_TYPE_JSON, $response->contentType);
        $this->assertSame('{"Error":"Method not allowed."}', $response->content);
    }
}
