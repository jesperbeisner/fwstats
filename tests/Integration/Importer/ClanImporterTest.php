<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Tests\Integration\Importer;

use DateTimeImmutable;
use Jesperbeisner\Fwstats\Enum\WorldEnum;
use Jesperbeisner\Fwstats\Importer\ClanImporter;
use Jesperbeisner\Fwstats\Interface\DatabaseInterface;
use Jesperbeisner\Fwstats\Interface\FreewarDumpServiceInterface;
use Jesperbeisner\Fwstats\Model\Clan;
use Jesperbeisner\Fwstats\Repository\ClanRepository;
use Jesperbeisner\Fwstats\Tests\AbstractTestCase;
use Jesperbeisner\Fwstats\Tests\Doubles\FreewarDumpServiceDummy;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ClanImporter::class)]
final class ClanImporterTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        $this->setUpContainer();
        $this->setUpDatabase();
    }

    public function test_initial_import_works_when_clans_table_is_empty(): void
    {
        $container = $this->getContainer();
        $database = $container->get(DatabaseInterface::class);

        $container->set(FreewarDumpServiceInterface::class, new FreewarDumpServiceDummy([], [
            1 => new Clan(null, WorldEnum::AFSRV, 1, 'test-1', 'Test-1', 1, 0, 0, 0, new DateTimeImmutable()),
            2 => new Clan(null, WorldEnum::AFSRV, 2, 'test-2', 'Test-2', 2, 0, 0, 0, new DateTimeImmutable()),
            3 => new Clan(null, WorldEnum::AFSRV, 3, 'test-3', 'Test-3', 3, 0, 0, 0, new DateTimeImmutable()),
        ], []));

        $this->assertCount(0, $database->select("SELECT * FROM clans"));

        $container->get(ClanImporter::class)->import(WorldEnum::AFSRV);

        $this->assertCount(3, $database->select("SELECT * FROM clans"));
    }

    public function test_clan_name_history_will_be_created_when_clan_name_or_clan_shortcut_change(): void
    {
        $container = $this->getContainer();
        $database = $container->get(DatabaseInterface::class);

        $container->set(FreewarDumpServiceInterface::class, new FreewarDumpServiceDummy([], [
            1 => new Clan(null, WorldEnum::AFSRV, 1, 'shortcut-change', 'Test-1', 1, 0, 0, 0, new DateTimeImmutable()),
            2 => new Clan(null, WorldEnum::AFSRV, 2, 'test-2', 'name-change', 2, 0, 0, 0, new DateTimeImmutable()),
            3 => new Clan(null, WorldEnum::AFSRV, 3, 'test-3', 'Test-3', 3, 0, 0, 0, new DateTimeImmutable()),
        ], []));

        $container->get(ClanRepository::class)->insertClans(WorldEnum::AFSRV, [
            new Clan(null, WorldEnum::AFSRV, 1, 'test-1', 'Test-1', 1, 0, 0, 0, new DateTimeImmutable()),
            new Clan(null, WorldEnum::AFSRV, 2, 'test-2', 'Test-2', 2, 0, 0, 0, new DateTimeImmutable()),
            new Clan(null, WorldEnum::AFSRV, 3, 'test-3', 'Test-3', 3, 0, 0, 0, new DateTimeImmutable()),
        ]);

        $this->assertCount(0, $database->select("SELECT * FROM clans_name_history"));

        $container->get(ClanImporter::class)->import(WorldEnum::AFSRV);

        $histories = $database->select("SELECT * FROM clans_name_history");

        $this->assertCount(2, $histories);

        $this->assertIsArray($histories[0]);
        $this->assertSame(1, $histories[0]['clan_id']);
        $this->assertSame('test-1', $histories[0]['old_shortcut']);
        $this->assertSame('shortcut-change', $histories[0]['new_shortcut']);

        $this->assertIsArray($histories[1]);
        $this->assertSame(2, $histories[1]['clan_id']);
        $this->assertSame('Test-2', $histories[1]['old_name']);
        $this->assertSame('name-change', $histories[1]['new_name']);
    }

    public function test_clan_deleted_history_will_be_when_clan_is_not_in_dump_anymore(): void
    {
        $container = $this->getContainer();
        $database = $container->get(DatabaseInterface::class);

        $container->set(FreewarDumpServiceInterface::class, new FreewarDumpServiceDummy([], [
            1 => new Clan(null, WorldEnum::AFSRV, 1, 'test-1', 'Test-1', 1, 0, 0, 0, new DateTimeImmutable()),
            2 => new Clan(null, WorldEnum::AFSRV, 2, 'test-2', 'Test-2', 2, 0, 0, 0, new DateTimeImmutable()),
        ], []));

        $container->get(ClanRepository::class)->insertClans(WorldEnum::AFSRV, [
            new Clan(null, WorldEnum::AFSRV, 1, 'test-1', 'Test-1', 1, 0, 0, 0, new DateTimeImmutable()),
            new Clan(null, WorldEnum::AFSRV, 2, 'test-2', 'Test-2', 2, 0, 0, 0, new DateTimeImmutable()),
            new Clan(null, WorldEnum::AFSRV, 3, 'test-3', 'Test-3', 3, 0, 0, 0, new DateTimeImmutable()),
        ]);

        $this->assertCount(0, $database->select("SELECT * FROM clans_deleted_history"));

        $container->get(ClanImporter::class)->import(WorldEnum::AFSRV);

        $histories = $database->select("SELECT * FROM clans_deleted_history");

        $this->assertCount(1, $histories);

        $this->assertIsArray($histories[0]);
        $this->assertSame(3, $histories[0]['clan_id']);
        $this->assertSame('test-3', $histories[0]['shortcut']);
        $this->assertSame('Test-3', $histories[0]['name']);
    }

    public function test_clan_created_history_will_be_when_clan_is_in_dump_but_not_database_anymore(): void
    {
        $container = $this->getContainer();
        $database = $container->get(DatabaseInterface::class);

        $container->set(FreewarDumpServiceInterface::class, new FreewarDumpServiceDummy([], [
            1 => new Clan(null, WorldEnum::AFSRV, 1, 'test-1', 'Test-1', 1, 0, 0, 0, new DateTimeImmutable()),
            2 => new Clan(null, WorldEnum::AFSRV, 2, 'test-2', 'Test-2', 2, 0, 0, 0, new DateTimeImmutable()),
            3 => new Clan(null, WorldEnum::AFSRV, 3, 'test-3', 'Test-3', 3, 0, 0, 0, new DateTimeImmutable()),
        ], []));

        $container->get(ClanRepository::class)->insertClans(WorldEnum::AFSRV, [
            new Clan(null, WorldEnum::AFSRV, 1, 'test-1', 'Test-1', 1, 0, 0, 0, new DateTimeImmutable()),
            new Clan(null, WorldEnum::AFSRV, 2, 'test-2', 'Test-2', 2, 0, 0, 0, new DateTimeImmutable()),
        ]);

        $this->assertCount(0, $database->select("SELECT * FROM clans_created_history"));

        $container->get(ClanImporter::class)->import(WorldEnum::AFSRV);

        $histories = $database->select("SELECT * FROM clans_created_history");

        $this->assertCount(1, $histories);

        $this->assertIsArray($histories[0]);
        $this->assertSame(3, $histories[0]['clan_id']);
        $this->assertSame('test-3', $histories[0]['shortcut']);
        $this->assertSame('Test-3', $histories[0]['name']);
    }
}
