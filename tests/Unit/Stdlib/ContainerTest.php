<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Tests\Unit\Stdlib;

use Jesperbeisner\Fwstats\Exception\ContainerException;
use Jesperbeisner\Fwstats\Interface\ContainerInterface;
use Jesperbeisner\Fwstats\Interface\FactoryInterface;
use Jesperbeisner\Fwstats\Stdlib\Container;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use stdClass;

#[CoversClass(Container::class)]
final class ContainerTest extends TestCase
{
    public function test_get_throws_a_ContainerException_when_the_service_does_not_exist(): void
    {
        $container = new Container([]);

        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage('Service with key "stdClass" does not exist in the container. Did you forget to register it in the "config.php" file?');

        $container->get(stdClass::class);
    }

    public function test_get_throws_a_ContainerException_when_the_service_is_not_an_instance_of_key(): void
    {
        $container = new Container([]);
        $container->set(stdClass::class, new Container([]));

        $this->expectException(ContainerException::class);
        $this->expectExceptionMessage('Returned service is not an instance of "stdClass".');

        $container->get(stdClass::class);
    }

    public function test_has_returns_true_when_the_service_exists_in_factory_array(): void
    {
        $container = new Container([stdClass::class => FactoryDummy::class]);

        $this->assertTrue($container->has(stdClass::class));
    }

    public function test_has_returns_false_when_the_service_does_not_exists_in_factory_array(): void
    {
        $container = new Container([stdClass::class => FactoryDummy::class]);

        $this->assertFalse($container->has(Container::class));
    }
}

final readonly class FactoryDummy implements FactoryInterface
{
    public function build(ContainerInterface $container, string $serviceId): object
    {
        return new $serviceId();
    }
}
