<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Tests\Unit\Result;

use DateTimeImmutable;
use Jesperbeisner\Fwstats\Enum\ResultEnum;
use Jesperbeisner\Fwstats\Exception\ActionResultException;
use Jesperbeisner\Fwstats\Model\User;
use Jesperbeisner\Fwstats\Result\CreateUserActionResult;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(CreateUserActionResult::class)]
class CreateUserActionResultTest extends TestCase
{
    public function test_will_return_right_boolean_result_when_calling_isSuccess(): void
    {
        $createUserActionResultTest = new CreateUserActionResult(ResultEnum::SUCCESS);
        $this->assertTrue($createUserActionResultTest->isSuccess());

        $createUserActionResultTest = new CreateUserActionResult(ResultEnum::FAILURE);
        $this->assertFalse($createUserActionResultTest->isSuccess());
    }

    public function test_will_throw_ActionResultException_when_user_is_not_set_in_data_array(): void
    {
        $createUserActionResultTest = new CreateUserActionResult(ResultEnum::SUCCESS);

        $this->expectException(ActionResultException::class);
        $this->expectExceptionMessage('No user in data array available.');

        $createUserActionResultTest->getUser();
    }

    public function test_will_return_user_when_user_is_set_in_data_array(): void
    {
        $user = new User(null, 'test', 'test@test.com', 'test', 'test', new DateTimeImmutable('2000-01-01'));

        $createUserActionResultTest = new CreateUserActionResult(ResultEnum::SUCCESS, 'test', ['user' => $user]);

        $resultUser = $createUserActionResultTest->getUser();

        $this->assertSame($user, $resultUser);
    }

    public function test_data_is_empty_when_not_set(): void
    {
        $createUserActionResultTest = new CreateUserActionResult(ResultEnum::SUCCESS);

        $this->assertSame([], $createUserActionResultTest->getData());
    }

    public function test_data_returns_the_same_array(): void
    {
        $array = ['test-1' => 'test', 'test-2' => 2];

        $createUserActionResultTest = new CreateUserActionResult(ResultEnum::SUCCESS, 'test', $array);

        $this->assertSame($array, $createUserActionResultTest->getData());
    }

    public function test_message_is_empty_when_not_set(): void
    {
        $createUserActionResultTest = new CreateUserActionResult(ResultEnum::SUCCESS);

        $this->assertSame('', $createUserActionResultTest->getMessage());
    }

    public function test_message_is_the_same_when_set(): void
    {
        $createUserActionResultTest = new CreateUserActionResult(ResultEnum::SUCCESS, 'test', []);

        $this->assertSame('test', $createUserActionResultTest->getMessage());
    }
}
