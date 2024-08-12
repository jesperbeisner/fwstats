<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Tests\Unit\Result;

use Jesperbeisner\Fwstats\Enum\ResultEnum;
use Jesperbeisner\Fwstats\Result\ActionResult;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ActionResult::class)]
final class ActionResultTest extends TestCase
{
    public function test_it_works(): void
    {
        $result = new ActionResult(ResultEnum::SUCCESS, 'test', ['test' => 'test']);

        $this->assertTrue($result->isSuccess());
        $this->assertSame('test', $result->getMessage());
        $this->assertSame(['test' => 'test'], $result->getData());
    }
}
