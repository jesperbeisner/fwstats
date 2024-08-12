<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Tests\Application;

use Jesperbeisner\Fwstats\Controller\UnauthorizedController;
use Jesperbeisner\Fwstats\Stdlib\Request;
use Jesperbeisner\Fwstats\Stdlib\Response;
use Jesperbeisner\Fwstats\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(UnauthorizedController::class)]
final class UnauthorizedControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        $this->setUpContainer();
        $this->setUpDatabase();
    }

    public function test_get_request(): void
    {
        $request = new Request(['REQUEST_URI' => '/test', 'REQUEST_METHOD' => 'GET'], [], [], [], []);

        $response = (new UnauthorizedController())->execute($request);

        $this->assertSame(401, $response->statusCode);
        $this->assertSame(Response::CONTENT_TYPE_JSON, $response->contentType);
        $this->assertSame('{"Error":"No token was specified or the token is not valid."}', $response->content);
    }
}
