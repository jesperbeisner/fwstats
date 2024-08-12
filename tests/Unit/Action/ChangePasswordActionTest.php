<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Tests\Unit\Action;

use DateTimeImmutable;
use Jesperbeisner\Fwstats\Action\ChangePasswordAction;
use Jesperbeisner\Fwstats\Exception\ActionException;
use Jesperbeisner\Fwstats\Exception\RuntimeException;
use Jesperbeisner\Fwstats\Model\User;
use Jesperbeisner\Fwstats\Repository\UserRepository;
use Jesperbeisner\Fwstats\Tests\Doubles\DatabaseDummy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ChangePasswordAction::class)]
final class ChangePasswordActionTest extends TestCase
{
    public function test_it_throws_a_RuntimeException_when_the_user_is_not_set(): void
    {
        $database = new DatabaseDummy();
        $userRepository = new UserRepository($database);
        $changePasswordAction = new ChangePasswordAction($userRepository);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No user and/or password set in "ChangePasswordAction::configure".');

        $changePasswordAction->configure(['user-not-set' => 'test', 'password' => 'test']);
    }

    public function test_it_throws_a_RuntimeException_when_the_password_is_not_set(): void
    {
        $database = new DatabaseDummy();
        $userRepository = new UserRepository($database);
        $changePasswordAction = new ChangePasswordAction($userRepository);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No user and/or password set in "ChangePasswordAction::configure".');

        $changePasswordAction->configure(['user' => 'test', 'password-not-set' => 'test']);
    }

    public function test_it_throws_a_RuntimeException_when_the_user_is_not_instance_of_User(): void
    {
        $database = new DatabaseDummy();
        $userRepository = new UserRepository($database);
        $changePasswordAction = new ChangePasswordAction($userRepository);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage(sprintf('The user set in the "CreateUserAction::configure" method is not an instance of "%s".', User::class));

        $changePasswordAction->configure(['user' => 'test', 'password' => 'test']);
    }

    public function test_it_throws_a_RuntimeException_when_the_password_is_no_string(): void
    {
        $user = new User(null, 'test', 'test', 'test', 'test', new DateTimeImmutable());

        $database = new DatabaseDummy();
        $userRepository = new UserRepository($database);
        $changePasswordAction = new ChangePasswordAction($userRepository);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The password set in the "CreateUserAction::configure" method is not a string.');

        $changePasswordAction->configure(['user' => $user, 'password' => 123]);
    }

    public function test_it_throws_a_ActionException_when_the_password_is_not_long_enough(): void
    {
        $user = new User(null, 'test', 'test', 'test', 'test', new DateTimeImmutable());

        $database = new DatabaseDummy();
        $userRepository = new UserRepository($database);
        $changePasswordAction = new ChangePasswordAction($userRepository);

        $this->expectException(ActionException::class);
        $this->expectExceptionMessage('text.password-not-long-enough');

        $changePasswordAction->configure(['user' => $user, 'password' => 'test']);
    }

    public function test_it_throws_a_ActionException_when_the_password_is_too_long(): void
    {
        $user = new User(null, 'test', 'test', 'test', 'test', new DateTimeImmutable());

        $database = new DatabaseDummy();
        $userRepository = new UserRepository($database);
        $changePasswordAction = new ChangePasswordAction($userRepository);

        $this->expectException(ActionException::class);
        $this->expectExceptionMessage('text.password-too-long');

        $changePasswordAction->configure(['user' => $user, 'password' => str_repeat('1234567890', 11)]);
    }

    public function test_it_throws_a_RuntimeException_when_configure_is_not_called_before_run(): void
    {
        $database = new DatabaseDummy();
        $userRepository = new UserRepository($database);
        $changePasswordAction = new ChangePasswordAction($userRepository);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('You need to call "configure" before you can call "run".');

        $changePasswordAction->run();
    }

    public function test_it_returns_a_success_ActionResult_when_everything_works(): void
    {
        $user = new User(1, 'test', 'test', 'test', 'test', new DateTimeImmutable());

        $database = new DatabaseDummy();
        $userRepository = new UserRepository($database);
        $changePasswordAction = new ChangePasswordAction($userRepository);

        $result = $changePasswordAction->configure(['user' => $user, 'password' => '1234567890'])->run();

        $this->assertTrue($result->isSuccess());
        $this->assertSame('text.password-changed-successfully', $result->getMessage());
    }
}
