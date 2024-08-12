<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Tests\Unit\Action;

use Jesperbeisner\Fwstats\Action\CreateUserAction;
use Jesperbeisner\Fwstats\Exception\ActionException;
use Jesperbeisner\Fwstats\Exception\RuntimeException;
use Jesperbeisner\Fwstats\Repository\UserRepository;
use Jesperbeisner\Fwstats\Tests\Doubles\DatabaseDummy;
use Jesperbeisner\Fwstats\Tests\Doubles\TranslatorDummy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CreateUserAction::class)]
final class CreateUserActionTest extends TestCase
{
    private CreateUserAction $createUserAction;

    public function setUp(): void
    {
        $this->createUserAction = new CreateUserAction(new UserRepository(new DatabaseDummy()), new TranslatorDummy());
    }

    public function test_will_throw_RuntimeException_when_username_is_not_set(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No username and/or password set in "CreateUserAction::configure".');

        $this->createUserAction->configure(['no-username' => 'test', 'password' => 'Password123']);
    }

    public function test_will_throw_RuntimeException_when_password_is_not_set(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No username and/or password set in "CreateUserAction::configure".');

        $this->createUserAction->configure(['username' => 'test', 'no-password' => 'Password123']);
    }

    public function test_will_throw_RuntimeException_when_username_is_not_a_string(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The username and/or password set in the "CreateUserAction::configure" method is not a string.');

        $this->createUserAction->configure(['username' => 123, 'password' => 'Password123']);
    }

    public function test_will_throw_RuntimeException_when_password_is_not_a_string(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The username and/or password set in the "CreateUserAction::configure" method is not a string.');

        $this->createUserAction->configure(['username' => 'test', 'password' => 123]);
    }

    public function test_will_throw_ActionException_when_username_is_not_valid(): void
    {
        $this->expectException(ActionException::class);
        $this->expectExceptionMessage('text.username-not-long-enough');

        $this->createUserAction->configure(['username' => 'a', 'password' => 'Password123']);
    }

    public function test_will_throw_ActionException_when_password_is_not_at_least_8_characters_long(): void
    {
        $this->expectException(ActionException::class);
        $this->expectExceptionMessage('text.password-not-long-enough');

        $this->createUserAction->configure(['username' => 'test', 'password' => 'test']);
    }

    public function test_will_throw_ActionException_when_user_with_this_username_already_exists(): void
    {
        $database = new DatabaseDummy();
        $database->setSelectOneReturn([
            'id' => 1,
            'uuid' => 'test',
            'username' => 'test',
            'password' => 'test',
            'token' => 'test',
            'created' => '2022-01-01',
        ]);

        $createUserAction = new CreateUserAction(new UserRepository($database), new TranslatorDummy());

        $createUserAction->configure(['username' => 'test', 'password' => 'Test1234567890']);

        $this->expectException(ActionException::class);
        $this->expectExceptionMessage('text.username-already-exists');

        $createUserAction->run();
    }

    public function test_will_create_a_new_user_and_returns_CreateUserActionResult_with_user(): void
    {
        $this->createUserAction->configure(['username' => 'test', 'password' => 'Test1234567890']);

        $createUserActionResult = $this->createUserAction->run();

        $this->assertTrue($createUserActionResult->isSuccess());
        $this->assertSame('text.user-created-successfully', $createUserActionResult->getMessage());
        $this->assertArrayHasKey('user', $createUserActionResult->getData());
    }
}
