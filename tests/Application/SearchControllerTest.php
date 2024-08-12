<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Tests\Application;

use DateTimeImmutable;
use Jesperbeisner\Fwstats\Application;
use Jesperbeisner\Fwstats\Controller\SearchController;
use Jesperbeisner\Fwstats\Enum\WorldEnum;
use Jesperbeisner\Fwstats\Model\Player;
use Jesperbeisner\Fwstats\Repository\PlayerRepository;
use Jesperbeisner\Fwstats\Stdlib\Request;
use Jesperbeisner\Fwstats\Stdlib\Response;
use Jesperbeisner\Fwstats\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(SearchController::class)]
final class SearchControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        $this->setUpContainer();
        $this->setUpDatabase();
    }

    public function test_get_request(): void
    {
        $request = new Request(['REQUEST_URI' => '/search', 'REQUEST_METHOD' => 'GET'], [], [], [], []);
        $this->getContainer()->set(Request::class, $request);

        $response = (new Application($this->getContainer()))->handle($request);

        $this->assertSame(200, $response->statusCode);
        $this->assertSame(Response::CONTENT_TYPE_HTML, $response->contentType);
        $this->assertSame('search/search.phtml', $response->template);
        $this->assertEmpty($response->vars['players']);
    }

    public function test_get_request_with_query_and_non_existing_player(): void
    {
        $request = new Request(['REQUEST_URI' => '/search', 'REQUEST_METHOD' => 'GET'], ['query' => 'test'], [], [], []);
        $this->getContainer()->set(Request::class, $request);

        $response = (new Application($this->getContainer()))->handle($request);

        $this->assertSame(200, $response->statusCode);
        $this->assertSame(Response::CONTENT_TYPE_HTML, $response->contentType);
        $this->assertSame('search/search.phtml', $response->template);
        $this->assertEmpty($response->vars['players']);
    }

    public function test_get_request_with_query_and_existing_player(): void
    {
        $request = new Request(['REQUEST_URI' => '/search', 'REQUEST_METHOD' => 'GET'], ['query' => 'test'], [], [], []);
        $this->getContainer()->set(Request::class, $request);

        $player = new Player(null, WorldEnum::AFSRV, 1, 'test', 'Onlo', 1, 0, 1, null, null, new DateTimeImmutable());
        $this->getContainer()->get(PlayerRepository::class)->insert($player);

        $response = (new Application($this->getContainer()))->handle($request);

        $this->assertSame(200, $response->statusCode);
        $this->assertSame(Response::CONTENT_TYPE_HTML, $response->contentType);
        $this->assertSame('search/search.phtml', $response->template);
        $this->assertNotEmpty($response->vars['players']);
    }
}
