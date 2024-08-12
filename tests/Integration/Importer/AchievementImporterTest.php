<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Tests\Integration\Importer;

use DateTimeImmutable;
use Jesperbeisner\Fwstats\Enum\WorldEnum;
use Jesperbeisner\Fwstats\Importer\AchievementImporter;
use Jesperbeisner\Fwstats\Interface\DatabaseInterface;
use Jesperbeisner\Fwstats\Interface\FreewarDumpServiceInterface;
use Jesperbeisner\Fwstats\Model\Player;
use Jesperbeisner\Fwstats\Repository\PlayerRepository;
use Jesperbeisner\Fwstats\Tests\AbstractTestCase;
use Jesperbeisner\Fwstats\Tests\Doubles\FreewarDumpServiceDummy;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(AchievementImporter::class)]
final class AchievementImporterTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        $this->setUpContainer();
        $this->setUpDatabase();
    }

    public function test_it_imports_nothing_when_no_players_are_found(): void
    {
        $container = $this->getContainer();
        $database = $container->get(DatabaseInterface::class);

        $container->set(FreewarDumpServiceInterface::class, new FreewarDumpServiceDummy([], [], [
            1 => [1019 => 1, 1022 => 1, 1021 => 1],
            2 => [1019 => 1, 1022 => 1, 1021 => 1],
            3 => [1019 => 1, 1022 => 1, 1021 => 1],
        ]));

        $this->assertCount(0, $database->select("SELECT * FROM achievements"));

        $container->get(AchievementImporter::class)->import(WorldEnum::AFSRV);

        $this->assertCount(0, $database->select("SELECT * FROM achievements"));
    }

    public function test_it_imports_for_each_player_id_that_is_found_in_the_achievements_dump(): void
    {
        $container = $this->getContainer();
        $database = $container->get(DatabaseInterface::class);

        $container->get(PlayerRepository::class)->insertPlayers(WorldEnum::AFSRV, [
            new Player(null, WorldEnum::AFSRV, 1, 'Test-1', 'Onlo', 100, 0, 100, null, null, new DateTimeImmutable()),
            new Player(null, WorldEnum::AFSRV, 2, 'Test-2', 'Taruner', 200, 0, 200, null, null, new DateTimeImmutable()),
        ]);

        $container->set(FreewarDumpServiceInterface::class, new FreewarDumpServiceDummy([], [], [
            1 => [1019 => 1, 1022 => 1, 1021 => 1],
            2 => [1019 => 1, 1022 => 1, 1021 => 1],
            3 => [1019 => 1, 1022 => 1, 1021 => 1],
        ]));

        $this->assertCount(0, $database->select("SELECT * FROM achievements"));

        $container->get(AchievementImporter::class)->import(WorldEnum::AFSRV);

        $this->assertCount(2, $database->select("SELECT * FROM achievements"));
    }
}
