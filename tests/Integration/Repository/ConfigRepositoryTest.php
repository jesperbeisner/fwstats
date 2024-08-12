<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Tests\Integration\Repository;

use DateTimeImmutable;
use Jesperbeisner\Fwstats\Model\Config;
use Jesperbeisner\Fwstats\Repository\ConfigRepository;
use Jesperbeisner\Fwstats\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ConfigRepository::class)]
final class ConfigRepositoryTest extends AbstractTestCase
{
    private ConfigRepository $configRepository;

    protected function setUp(): void
    {
        $this->setUpContainer();
        $this->setUpDatabase();

        $this->configRepository = $this->getContainer()->get(ConfigRepository::class);
    }

    public function test_insert(): void
    {
        $config = new Config(null, 'test', 'test', new DateTimeImmutable());
        $newConfig = $this->configRepository->insert($config);

        $this->assertSame(1, $newConfig->id);
        $this->assertNotSame($config, $newConfig);
    }

    public function test_findByKey_returns_null_when_key_is_not_available(): void
    {
        $result = $this->configRepository->findByKey('test');

        $this->assertNull($result);
    }

    public function test_findByKey_returns_a_config_model_when_key_is_available(): void
    {
        $this->configRepository->insert(new Config(null, 'test-key', 'test-value', new DateTimeImmutable()));

        $result = $this->configRepository->findByKey('test-key');

        $this->assertInstanceOf(Config::class, $result);
        $this->assertSame('test-key', $result->key);
        $this->assertSame('test-value', $result->value);
    }

    public function test_changeDomainName_works_as_expected(): void
    {
        $result = $this->configRepository->findByKey('domain-name');

        $this->assertNull($result);

        $this->configRepository->insert(new Config(null, 'domain-name', 'https://example.com', new DateTimeImmutable()));

        $result = $this->configRepository->findByKey('domain-name');

        $this->assertInstanceOf(Config::class, $result);
        $this->assertSame('domain-name', $result->key);
        $this->assertSame('https://example.com', $result->value);

        $this->configRepository->changeDomainName('https://example-test.com');

        $result = $this->configRepository->findByKey('domain-name');

        $this->assertInstanceOf(Config::class, $result);
        $this->assertSame('domain-name', $result->key);
        $this->assertSame('https://example-test.com', $result->value);
    }
}
