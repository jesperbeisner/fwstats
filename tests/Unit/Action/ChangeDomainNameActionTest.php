<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Tests\Unit\Action;

use Jesperbeisner\Fwstats\Action\ChangeDomainNameAction;
use Jesperbeisner\Fwstats\Exception\ActionException;
use Jesperbeisner\Fwstats\Exception\RuntimeException;
use Jesperbeisner\Fwstats\Repository\ConfigRepository;
use Jesperbeisner\Fwstats\Tests\Doubles\DatabaseDummy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ChangeDomainNameAction::class)]
final class ChangeDomainNameActionTest extends TestCase
{
    public function test_it_throws_RuntimeException_when_domainName_is_not_set(): void
    {
        $database = new DatabaseDummy();
        $configRepository = new ConfigRepository($database);
        $changeDomainNameAction = new ChangeDomainNameAction($configRepository);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No domain name set in "ChangeDomainNameAction::configure".');

        $changeDomainNameAction->configure([]);
    }

    public function test_it_throws_RuntimeException_when_domainName_is_not_a_string(): void
    {
        $database = new DatabaseDummy();
        $configRepository = new ConfigRepository($database);
        $changeDomainNameAction = new ChangeDomainNameAction($configRepository);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The domain name set in the "ChangeDomainNameAction::configure" method is not a string.');

        $changeDomainNameAction->configure(['domainName' => 123]);
    }

    public function test_it_throws_RuntimeException_when_domainName_does_not_start_with_https(): void
    {
        $database = new DatabaseDummy();
        $configRepository = new ConfigRepository($database);
        $changeDomainNameAction = new ChangeDomainNameAction($configRepository);

        $this->expectException(ActionException::class);
        $this->expectExceptionMessage('text.domain-name-wrong-start');

        $changeDomainNameAction->configure(['domainName' => 'http://example.com']);
    }

    public function test_it_throws_a_RuntimeException_when_configure_is_not_called_before_run(): void
    {
        $database = new DatabaseDummy();
        $configRepository = new ConfigRepository($database);
        $changeDomainNameAction = new ChangeDomainNameAction($configRepository);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('You need to call "configure" before you can call "run".');

        $changeDomainNameAction->run();
    }

    public function test_it_returns_a_success_ActionResult_when_everything_works(): void
    {
        $database = new DatabaseDummy();
        $configRepository = new ConfigRepository($database);
        $changeDomainNameAction = new ChangeDomainNameAction($configRepository);

        $result = $changeDomainNameAction->configure(['domainName' => 'https://example.com'])->run();

        $this->assertTrue($result->isSuccess());
        $this->assertSame('text.domain-name-changed-successfully', $result->getMessage());
    }
}
