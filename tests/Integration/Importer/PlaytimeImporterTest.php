<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Tests\Integration\Importer;

use DateTimeImmutable;
use Jesperbeisner\Fwstats\Enum\WorldEnum;
use Jesperbeisner\Fwstats\Importer\PlaytimeImporter;
use Jesperbeisner\Fwstats\Interface\DatabaseInterface;
use Jesperbeisner\Fwstats\Interface\FreewarDumpServiceInterface;
use Jesperbeisner\Fwstats\Model\Player;
use Jesperbeisner\Fwstats\Tests\AbstractTestCase;
use Jesperbeisner\Fwstats\Tests\Doubles\FreewarDumpServiceDummy;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PlaytimeImporter::class)]
final class PlaytimeImporterTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        $this->setUpContainer();
        $this->setUpDatabase();
    }

    public function test_it_imports_playtimes(): void
    {
        $container = $this->getContainer();
        $database = $container->get(DatabaseInterface::class);

        $container->set(FreewarDumpServiceInterface::class, new FreewarDumpServiceDummy([
            1 => new Player(null, WorldEnum::AFSRV, 1, 'Test-1', 'Onlo', 100, 0, 100, null, null, new DateTimeImmutable()),
            2 => new Player(null, WorldEnum::AFSRV, 2, 'Test-2', 'Taruner', 200, 0, 200, null, null, new DateTimeImmutable()),
            3 => new Player(null, WorldEnum::AFSRV, 3, 'Test-3', 'Keuroner', 300, 0, 300, null, null, new DateTimeImmutable()),
        ], [], [
            1 => [1019 => 1, 1022 => 1, 1021 => 1, 1047 => 1],
            2 => [1019 => 1, 1022 => 1, 1021 => 1, 1047 => 1],
            3 => [1019 => 1, 1022 => 1, 1021 => 1, 1047 => 1],
        ]));

        $this->assertCount(0, $database->select("SELECT * FROM players_active_seconds"));

        $container->get(PlaytimeImporter::class)->import(WorldEnum::AFSRV);

        $this->assertCount(3, $database->select("SELECT * FROM players_active_seconds"));
    }

    public function test_it_imports_nothing_when_players_dump_is_empty(): void
    {
        $container = $this->getContainer();
        $database = $container->get(DatabaseInterface::class);

        $container->set(FreewarDumpServiceInterface::class, new FreewarDumpServiceDummy([], [], [
            1 => [1019 => 1, 1022 => 1, 1021 => 1, 1047 => 1],
            2 => [1019 => 1, 1022 => 1, 1021 => 1, 1047 => 1],
            3 => [1019 => 1, 1022 => 1, 1021 => 1, 1047 => 1],
        ]));

        $this->assertCount(0, $database->select("SELECT * FROM players_active_seconds"));

        $container->get(PlaytimeImporter::class)->import(WorldEnum::AFSRV);

        $this->assertCount(0, $database->select("SELECT * FROM players_active_seconds"));
    }

    public function test_it_imports_nothing_when_achievements_dump_is_empty(): void
    {
        $container = $this->getContainer();
        $database = $container->get(DatabaseInterface::class);

        $container->set(FreewarDumpServiceInterface::class, new FreewarDumpServiceDummy([
            1 => new Player(null, WorldEnum::AFSRV, 1, 'Test-1', 'Onlo', 100, 0, 100, null, null, new DateTimeImmutable()),
            2 => new Player(null, WorldEnum::AFSRV, 2, 'Test-2', 'Taruner', 200, 0, 200, null, null, new DateTimeImmutable()),
            3 => new Player(null, WorldEnum::AFSRV, 3, 'Test-3', 'Keuroner', 300, 0, 300, null, null, new DateTimeImmutable()),
        ], [], []));

        $this->assertCount(0, $database->select("SELECT * FROM players_active_seconds"));

        $container->get(PlaytimeImporter::class)->import(WorldEnum::AFSRV);

        $this->assertCount(0, $database->select("SELECT * FROM players_active_seconds"));
    }
}
