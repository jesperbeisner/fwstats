<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Tests\Unit\Stdlib;

use Jesperbeisner\Fwstats\Exception\RuntimeException;
use Jesperbeisner\Fwstats\Stdlib\Config;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Config::class)]
final class ConfigTest extends TestCase
{
    public function test_throws_a_RuntimeException_when_config_does_not_exist(): void
    {
        $config = new Config(['global' => [], 'routes' => [], 'startProcesses' => [], 'endProcesses' => [], 'commands' => [], 'factories' => []]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('There is no config value for the key "test".');

        $config->get('test');
    }

    public function test_throws_a_RuntimeException_when_config_is_not_a_string(): void
    {
        $config = new Config(['global' => ['test' => 1], 'routes' => [], 'startProcesses' => [], 'endProcesses' => [], 'commands' => [], 'factories' => []]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Config value for key "test" found but it is no string.');

        $config->getString('test');
    }

    public function test_throws_a_RuntimeException_when_config_is_not_a_integer(): void
    {
        $config = new Config(['global' => ['test' => 'test'], 'routes' => [], 'startProcesses' => [], 'endProcesses' => [], 'commands' => [], 'factories' => []]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Config value for key "test" found but it is no int.');

        $config->getInt('test');
    }

    public function test_throws_a_RuntimeException_when_config_is_not_a_float(): void
    {
        $config = new Config(['global' => ['test' => 'test'], 'routes' => [], 'startProcesses' => [], 'endProcesses' => [], 'commands' => [], 'factories' => []]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Config value for key "test" found but it is no float.');

        $config->getFloat('test');
    }

    public function test_throws_a_RuntimeException_when_config_is_not_a_bool(): void
    {
        $config = new Config(['global' => ['test' => 'test'], 'routes' => [], 'startProcesses' => [], 'endProcesses' => [], 'commands' => [], 'factories' => []]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Config value for key "test" found but it is no bool.');

        $config->getBool('test');
    }

    public function test_it_returns_the_right_app_env(): void
    {
        $config = new Config(['global' => ['app_env' => 'test'], 'routes' => [], 'startProcesses' => [], 'endProcesses' => [], 'commands' => [], 'factories' => []]);

        $this->assertSame('test', $config->getAppEnv());
    }

    public function test_it_returns_the_right_root_dir(): void
    {
        $config = new Config(['global' => ['root_directory' => __DIR__], 'routes' => [], 'startProcesses' => [], 'endProcesses' => [], 'commands' => [], 'factories' => []]);

        $this->assertSame(__DIR__, $config->getRootDir());
    }
}
