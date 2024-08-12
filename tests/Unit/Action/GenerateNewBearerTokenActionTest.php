<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Tests\Unit\Action;

use DateTimeImmutable;
use Jesperbeisner\Fwstats\Action\GenerateNewBearerTokenAction;
use Jesperbeisner\Fwstats\Exception\RuntimeException;
use Jesperbeisner\Fwstats\Model\User;
use Jesperbeisner\Fwstats\Repository\UserRepository;
use Jesperbeisner\Fwstats\Tests\Doubles\DatabaseDummy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(GenerateNewBearerTokenAction::class)]
final class GenerateNewBearerTokenActionTest extends TestCase
{
    public function test_it_throws_a_RuntimeException_when_no_user_is_set(): void
    {
        $database = new DatabaseDummy();
        $userRepository = new UserRepository($database);
        $generateNewBearerTokenAction = new GenerateNewBearerTokenAction($userRepository);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No user and/or token set in "GenerateNewBearerTokenAction::configure".');

        $generateNewBearerTokenAction->configure(['user-not-set' => 'test', 'token' => 'test']);
    }

    public function test_it_throws_a_RuntimeException_when_no_token_is_set(): void
    {
        $database = new DatabaseDummy();
        $userRepository = new UserRepository($database);
        $generateNewBearerTokenAction = new GenerateNewBearerTokenAction($userRepository);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No user and/or token set in "GenerateNewBearerTokenAction::configure".');

        $generateNewBearerTokenAction->configure(['user' => 'test', 'token-not-set' => 'test']);
    }

    public function test_it_throws_a_RuntimeException_when_the_user_is_not_an_instance_of_User(): void
    {
        $database = new DatabaseDummy();
        $userRepository = new UserRepository($database);
        $generateNewBearerTokenAction = new GenerateNewBearerTokenAction($userRepository);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(sprintf('The user set in the "GenerateNewBearerTokenAction::configure" method is not an instance of "%s".', User::class));

        $generateNewBearerTokenAction->configure(['user' => 'test', 'token' => 'test']);
    }

    public function test_it_throws_a_RuntimeException_when_the_token_is_not_a_string(): void
    {
        $user = new User(null, 'test', 'test', 'test', 'test', new DateTimeImmutable());

        $database = new DatabaseDummy();
        $userRepository = new UserRepository($database);
        $generateNewBearerTokenAction = new GenerateNewBearerTokenAction($userRepository);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The token set in the "GenerateNewBearerTokenAction::configure" method is not a string.');

        $generateNewBearerTokenAction->configure(['user' => $user, 'token' => 123]);
    }

    public function test_it_throws_a_RuntimeException_when_the_token_is_not_32_characters_long(): void
    {
        $user = new User(null, 'test', 'test', 'test', 'test', new DateTimeImmutable());

        $database = new DatabaseDummy();
        $userRepository = new UserRepository($database);
        $generateNewBearerTokenAction = new GenerateNewBearerTokenAction($userRepository);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The token set in the "GenerateNewBearerTokenAction::configure" method is not exactly 32 characters long.');

        $generateNewBearerTokenAction->configure(['user' => $user, 'token' => 'test']);
    }

    public function test_it_throws_a_RuntimeException_when_configure_is_not_called_before_run(): void
    {
        $database = new DatabaseDummy();
        $userRepository = new UserRepository($database);
        $generateNewBearerTokenAction = new GenerateNewBearerTokenAction($userRepository);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('You need to call "configure" before you can call "run".');

        $generateNewBearerTokenAction->run();
    }

    public function test_it_returns_a_success_ActionResult_when_everything_works(): void
    {
        $user = new User(1, 'test', 'test', 'test', 'test', new DateTimeImmutable());

        $database = new DatabaseDummy();
        $userRepository = new UserRepository($database);
        $generateNewBearerTokenAction = new GenerateNewBearerTokenAction($userRepository);

        $result = $generateNewBearerTokenAction->configure(['user' => $user, 'token' => str_repeat('0', 32)])->run();

        $this->assertTrue($result->isSuccess());
        $this->assertSame('text.new-token-generated-successfully', $result->getMessage());
    }
}
