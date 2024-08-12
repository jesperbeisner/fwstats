<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Tests\Application;

use DateTimeImmutable;
use Jesperbeisner\Fwstats\Application;
use Jesperbeisner\Fwstats\Controller\CronjobController;
use Jesperbeisner\Fwstats\Interface\CronjobInterface;
use Jesperbeisner\Fwstats\Model\User;
use Jesperbeisner\Fwstats\Repository\UserRepository;
use Jesperbeisner\Fwstats\Stdlib\Request;
use Jesperbeisner\Fwstats\Stdlib\Response;
use Jesperbeisner\Fwstats\Tests\AbstractTestCase;
use Jesperbeisner\Fwstats\Tests\Doubles\CronjobDummy;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(CronjobController::class)]
final class CronjobControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        $this->setUpContainer();
        $this->setUpDatabase();
    }

    public function test_get_request(): void
    {
        $request = new Request(['REQUEST_URI' => '/cronjob', 'REQUEST_METHOD' => 'GET'], [], [], [], []);
        $this->getContainer()->set(Request::class, $request);

        $this->getContainer()->set(CronjobInterface::class, new CronjobDummy());

        $response = (new Application($this->getContainer()))->handle($request);

        $this->assertSame(405, $response->statusCode);
        $this->assertSame(Response::CONTENT_TYPE_JSON, $response->contentType);
        $this->assertSame('{"Error":"Method not allowed."}', $response->content);
    }

    public function test_post_request_without_token(): void
    {
        $request = new Request(['REQUEST_URI' => '/cronjob', 'REQUEST_METHOD' => 'POST'], [], [], [], []);
        $this->getContainer()->set(Request::class, $request);

        $this->getContainer()->set(CronjobInterface::class, new CronjobDummy());

        $response = (new Application($this->getContainer()))->handle($request);

        $this->assertSame(401, $response->statusCode);
        $this->assertSame(Response::CONTENT_TYPE_JSON, $response->contentType);
        $this->assertSame('{"Error":"No token was specified or the token is not valid."}', $response->content);
    }

    public function test_post_request_with_non_valid_token(): void
    {
        $request = new Request(['REQUEST_URI' => '/cronjob', 'REQUEST_METHOD' => 'POST'], [], [], [], ['Authorization' => 'Bearer test']);
        $this->getContainer()->set(Request::class, $request);

        $this->getContainer()->set(CronjobInterface::class, new CronjobDummy());

        $response = (new Application($this->getContainer()))->handle($request);

        $this->assertSame(401, $response->statusCode);
        $this->assertSame(Response::CONTENT_TYPE_JSON, $response->contentType);
        $this->assertSame('{"Error":"No token was specified or the token is not valid."}', $response->content);
    }

    public function test_post_request_with_valid_token_but_cronjob_not_allowed_to_run(): void
    {
        $request = new Request(['REQUEST_URI' => '/cronjob', 'REQUEST_METHOD' => 'POST'], [], [], [], ['Authorization' => 'Bearer test']);
        $this->getContainer()->set(Request::class, $request);

        $this->getContainer()->set(CronjobInterface::class, new CronjobDummy(false));

        $user = new User(1, 'test', 'test', 'test', 'test', new DateTimeImmutable());
        $this->getContainer()->get(UserRepository::class)->insert($user);

        $response = (new Application($this->getContainer()))->handle($request);

        $this->assertSame(200, $response->statusCode);
        $this->assertSame(Response::CONTENT_TYPE_JSON, $response->contentType);
        $this->assertSame('{"Success":"Request received but cronjob is currently not allowed to run."}', $response->content);
    }

    public function test_post_request_with_valid_token(): void
    {
        $request = new Request(['REQUEST_URI' => '/cronjob', 'REQUEST_METHOD' => 'POST'], [], [], [], ['Authorization' => 'Bearer test']);
        $this->getContainer()->set(Request::class, $request);

        $this->getContainer()->set(CronjobInterface::class, new CronjobDummy());

        $user = new User(1, 'test', 'test', 'test', 'test', new DateTimeImmutable());
        $this->getContainer()->get(UserRepository::class)->insert($user);

        $response = (new Application($this->getContainer()))->handle($request);

        $this->assertSame(200, $response->statusCode);
        $this->assertSame(Response::CONTENT_TYPE_JSON, $response->contentType);
        $this->assertSame('{"Success":"Cronjob successfully executed."}', $response->content);
    }
}
