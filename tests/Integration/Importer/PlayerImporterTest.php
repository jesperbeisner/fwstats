<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Tests\Integration\Importer;

use DateTimeImmutable;
use Jesperbeisner\Fwstats\Enum\PlayerStatusEnum;
use Jesperbeisner\Fwstats\Enum\WorldEnum;
use Jesperbeisner\Fwstats\Importer\PlayerImporter;
use Jesperbeisner\Fwstats\Interface\DatabaseInterface;
use Jesperbeisner\Fwstats\Interface\FreewarDumpServiceInterface;
use Jesperbeisner\Fwstats\Interface\PlayerStatusServiceInterface;
use Jesperbeisner\Fwstats\Model\Clan;
use Jesperbeisner\Fwstats\Model\Player;
use Jesperbeisner\Fwstats\Model\PlayerStatusHistory;
use Jesperbeisner\Fwstats\Repository\ClanRepository;
use Jesperbeisner\Fwstats\Repository\PlayerRepository;
use Jesperbeisner\Fwstats\Repository\PlayerStatusHistoryRepository;
use Jesperbeisner\Fwstats\Tests\AbstractTestCase;
use Jesperbeisner\Fwstats\Tests\Doubles\FreewarDumpServiceDummy;
use Jesperbeisner\Fwstats\Tests\Doubles\PlayerStatusServiceDummy;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PlayerImporter::class)]
final class PlayerImporterTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        $this->setUpContainer();
        $this->setUpDatabase();
    }

    public function test_it_will_import_players_dump_successfully(): void
    {
        $container = $this->getContainer();
        $database = $container->get(DatabaseInterface::class);

        $container->set(PlayerStatusServiceInterface::class, new PlayerStatusServiceDummy(PlayerStatusEnum::BANNED));

        $freewarDumpService = new FreewarDumpServiceDummy([
            1 => new Player(null, WorldEnum::AFSRV, 1, 'Test-1', 'Onlo', 100, 0, 100, null, null, new DateTimeImmutable()),
            2 => new Player(null, WorldEnum::AFSRV, 2, 'Test-2', 'Taruner', 200, 0, 200, null, null, new DateTimeImmutable()),
            3 => new Player(null, WorldEnum::AFSRV, 3, 'Test-3', 'Keuroner', 300, 0, 300, null, null, new DateTimeImmutable()),
        ]);

        $container->set(FreewarDumpServiceInterface::class, $freewarDumpService);

        $this->assertCount(0, $database->select("SELECT * FROM players"));

        $container->get(PlayerImporter::class)->import(WorldEnum::AFSRV);

        $this->assertCount(3, $database->select("SELECT * FROM players"));
    }

    public function test_it_will_update_daily_xp_changes_successfully(): void
    {
        $container = $this->getContainer();
        $database = $container->get(DatabaseInterface::class);

        $container->set(PlayerStatusServiceInterface::class, new PlayerStatusServiceDummy(PlayerStatusEnum::BANNED));

        $freewarDumpService = new FreewarDumpServiceDummy([
            1 => new Player(null, WorldEnum::AFSRV, 1, 'Test-1', 'Onlo', 100, 0, 100, null, null, new DateTimeImmutable()),
            2 => new Player(null, WorldEnum::AFSRV, 2, 'Test-2', 'Taruner', 200, 0, 200, null, null, new DateTimeImmutable()),
            3 => new Player(null, WorldEnum::AFSRV, 3, 'Test-3', 'Keuroner', 300, 0, 300, null, null, new DateTimeImmutable()),
        ]);

        $container->set(FreewarDumpServiceInterface::class, $freewarDumpService);

        $this->assertCount(0, $database->select("SELECT * FROM players"));
        $this->assertCount(0, $database->select("SELECT * FROM players_xp_history"));

        $container->get(PlayerImporter::class)->import(WorldEnum::AFSRV);

        $this->assertCount(3, $database->select("SELECT * FROM players"));
        $this->assertCount(3, $database->select("SELECT * FROM players_xp_history"));
        $this->assertSame([
            ['id' => 1, 'world' => 'afsrv', 'player_id' => 1, 'start_xp' => 100, 'end_xp' => 100, 'day' => (new DateTimeImmutable())->setTime(0, 0, 0)->format('Y-m-d H:i:s')],
            ['id' => 2, 'world' => 'afsrv', 'player_id' => 2, 'start_xp' => 200, 'end_xp' => 200, 'day' => (new DateTimeImmutable())->setTime(0, 0, 0)->format('Y-m-d H:i:s')],
            ['id' => 3, 'world' => 'afsrv', 'player_id' => 3, 'start_xp' => 300, 'end_xp' => 300, 'day' => (new DateTimeImmutable())->setTime(0, 0, 0)->format('Y-m-d H:i:s')],
        ], $database->select("SELECT * FROM players_xp_history"));

        // We need to set up the container once again so the PlayerImporter gets the new FreewarDumpService
        $this->setUpContainer();
        $container = $this->getContainer();

        $freewarDumpService = new FreewarDumpServiceDummy([
            1 => new Player(null, WorldEnum::AFSRV, 1, 'Test-1', 'Onlo', 500, 0, 500, null, null, new DateTimeImmutable()),
            2 => new Player(null, WorldEnum::AFSRV, 2, 'Test-2', 'Taruner', 600, 0, 600, null, null, new DateTimeImmutable()),
            3 => new Player(null, WorldEnum::AFSRV, 3, 'Test-3', 'Keuroner', 700, 0, 700, null, null, new DateTimeImmutable()),
        ]);

        $container->set(FreewarDumpServiceInterface::class, $freewarDumpService);

        $container->get(PlayerImporter::class)->import(WorldEnum::AFSRV);

        $this->assertSame([
            ['id' => 1, 'world' => 'afsrv', 'player_id' => 1, 'start_xp' => 100, 'end_xp' => 500, 'day' => (new DateTimeImmutable())->setTime(0, 0, 0)->format('Y-m-d H:i:s')],
            ['id' => 2, 'world' => 'afsrv', 'player_id' => 2, 'start_xp' => 200, 'end_xp' => 600, 'day' => (new DateTimeImmutable())->setTime(0, 0, 0)->format('Y-m-d H:i:s')],
            ['id' => 3, 'world' => 'afsrv', 'player_id' => 3, 'start_xp' => 300, 'end_xp' => 700, 'day' => (new DateTimeImmutable())->setTime(0, 0, 0)->format('Y-m-d H:i:s')],
        ], $database->select("SELECT * FROM players_xp_history"));
    }

    public function test_it_will_create_a_new_player_created_entry_when_dump_has_a_new_player(): void
    {
        $container = $this->getContainer();
        $database = $container->get(DatabaseInterface::class);

        $freewarDumpService = new FreewarDumpServiceDummy([
            1 => new Player(null, WorldEnum::AFSRV, 1, 'Test-1', 'Onlo', 100, 0, 100, null, null, new DateTimeImmutable()),
            2 => new Player(null, WorldEnum::AFSRV, 2, 'Test-2', 'Taruner', 200, 0, 200, null, null, new DateTimeImmutable()),
            3 => new Player(null, WorldEnum::AFSRV, 3, 'Test-3', 'Keuroner', 300, 0, 300, null, null, new DateTimeImmutable()),
        ]);

        $container->set(FreewarDumpServiceInterface::class, $freewarDumpService);

        $container->get(PlayerRepository::class)->insertPlayers(WorldEnum::AFSRV, [
            new Player(null, WorldEnum::AFSRV, 1, 'Test-1', 'Onlo', 100, 0, 100, null, null, new DateTimeImmutable()),
            new Player(null, WorldEnum::AFSRV, 2, 'Test-2', 'Taruner', 200, 0, 200, null, null, new DateTimeImmutable()),
        ]);

        $this->assertCount(2, $database->select("SELECT * FROM players"));
        $this->assertCount(0, $database->select("SELECT * FROM players_created_history"));

        $container->get(PlayerImporter::class)->import(WorldEnum::AFSRV);

        $this->assertCount(3, $database->select("SELECT * FROM players"));
        $this->assertCount(1, $database->select("SELECT * FROM players_created_history"));
    }

    public function test_it_will_create_player_name_change_history(): void
    {
        $container = $this->getContainer();
        $database = $container->get(DatabaseInterface::class);

        $freewarDumpService = new FreewarDumpServiceDummy([
            1 => new Player(null, WorldEnum::AFSRV, 1, 'Test-1', 'Onlo', 100, 0, 100, null, null, new DateTimeImmutable()),
            2 => new Player(null, WorldEnum::AFSRV, 2, 'Test-2', 'Taruner', 200, 0, 200, null, null, new DateTimeImmutable()),
            3 => new Player(null, WorldEnum::AFSRV, 3, 'name-change', 'Keuroner', 300, 0, 300, null, null, new DateTimeImmutable()),
        ]);

        $container->set(FreewarDumpServiceInterface::class, $freewarDumpService);

        $container->get(PlayerRepository::class)->insertPlayers(WorldEnum::AFSRV, [
            new Player(null, WorldEnum::AFSRV, 1, 'Test-1', 'Onlo', 100, 0, 100, null, null, new DateTimeImmutable()),
            new Player(null, WorldEnum::AFSRV, 2, 'Test-2', 'Taruner', 200, 0, 200, null, null, new DateTimeImmutable()),
            new Player(null, WorldEnum::AFSRV, 3, 'Test-3', 'Keuroner', 300, 0, 300, null, null, new DateTimeImmutable()),
        ]);

        $this->assertCount(0, $database->select("SELECT * FROM players_name_history"));

        $container->get(PlayerImporter::class)->import(WorldEnum::AFSRV);

        $histories = $database->select("SELECT * FROM players_name_history");

        $this->assertCount(1, $histories);

        $this->assertIsArray($histories[0]);
        $this->assertSame(3, $histories[0]['player_id']);
        $this->assertSame('Test-3', $histories[0]['old_name']);
        $this->assertSame('name-change', $histories[0]['new_name']);
    }

    public function test_it_will_create_player_race_change_history(): void
    {
        $container = $this->getContainer();
        $database = $container->get(DatabaseInterface::class);

        $freewarDumpService = new FreewarDumpServiceDummy([
            1 => new Player(null, WorldEnum::AFSRV, 1, 'Test-1', 'Onlo', 100, 0, 100, null, null, new DateTimeImmutable()),
            2 => new Player(null, WorldEnum::AFSRV, 2, 'Test-2', 'Taruner', 200, 0, 200, null, null, new DateTimeImmutable()),
            3 => new Player(null, WorldEnum::AFSRV, 3, 'Test-3', 'Serum-Geist', 300, 0, 300, null, null, new DateTimeImmutable()),
        ]);

        $container->set(FreewarDumpServiceInterface::class, $freewarDumpService);

        $container->get(PlayerRepository::class)->insertPlayers(WorldEnum::AFSRV, [
            new Player(null, WorldEnum::AFSRV, 1, 'Test-1', 'Onlo', 100, 0, 100, null, null, new DateTimeImmutable()),
            new Player(null, WorldEnum::AFSRV, 2, 'Test-2', 'Taruner', 200, 0, 200, null, null, new DateTimeImmutable()),
            new Player(null, WorldEnum::AFSRV, 3, 'Test-3', 'Keuroner', 300, 0, 300, null, null, new DateTimeImmutable()),
        ]);

        $this->assertCount(0, $database->select("SELECT * FROM players_race_history"));

        $container->get(PlayerImporter::class)->import(WorldEnum::AFSRV);

        $histories = $database->select("SELECT * FROM players_race_history");

        $this->assertCount(1, $histories);

        $this->assertIsArray($histories[0]);
        $this->assertSame(3, $histories[0]['player_id']);
        $this->assertSame('Keuroner', $histories[0]['old_race']);
        $this->assertSame('Serum-Geist', $histories[0]['new_race']);
    }

    public function test_it_will_create_player_profession_change_history(): void
    {
        $container = $this->getContainer();
        $database = $container->get(DatabaseInterface::class);

        $freewarDumpService = new FreewarDumpServiceDummy([
            1 => new Player(null, WorldEnum::AFSRV, 1, 'Test-1', 'Onlo', 100, 0, 100, null, 'Magieverlängerer', new DateTimeImmutable()),
            2 => new Player(null, WorldEnum::AFSRV, 2, 'Test-2', 'Taruner', 200, 0, 200, null, null, new DateTimeImmutable()),
            3 => new Player(null, WorldEnum::AFSRV, 3, 'Test-3', 'Serum-Geist', 300, 0, 300, null, 'Sammler', new DateTimeImmutable()),
        ]);

        $container->set(FreewarDumpServiceInterface::class, $freewarDumpService);

        $container->get(PlayerRepository::class)->insertPlayers(WorldEnum::AFSRV, [
            new Player(null, WorldEnum::AFSRV, 1, 'Test-1', 'Onlo', 100, 0, 100, null, null, new DateTimeImmutable()),
            new Player(null, WorldEnum::AFSRV, 2, 'Test-2', 'Taruner', 200, 0, 200, null, 'Maschinenbauer', new DateTimeImmutable()),
            new Player(null, WorldEnum::AFSRV, 3, 'Test-3', 'Keuroner', 300, 0, 300, null, 'Schützer', new DateTimeImmutable()),
        ]);

        $this->assertCount(0, $database->select("SELECT * FROM players_profession_history"));

        $container->get(PlayerImporter::class)->import(WorldEnum::AFSRV);

        $histories = $database->select("SELECT * FROM players_profession_history");

        $this->assertCount(3, $histories);

        $this->assertIsArray($histories[0]);
        $this->assertSame(1, $histories[0]['player_id']);
        $this->assertNull($histories[0]['old_profession']);
        $this->assertSame('Magieverlängerer', $histories[0]['new_profession']);

        $this->assertIsArray($histories[1]);
        $this->assertSame(2, $histories[1]['player_id']);
        $this->assertSame('Maschinenbauer', $histories[1]['old_profession']);
        $this->assertNull($histories[1]['new_profession']);

        $this->assertIsArray($histories[2]);
        $this->assertSame(3, $histories[2]['player_id']);
        $this->assertSame('Schützer', $histories[2]['old_profession']);
        $this->assertSame('Sammler', $histories[2]['new_profession']);
    }

    public function test_it_will_create_player_clan_change_history_and_fills_it_with_nulls_when_no_clan_is_available(): void
    {
        $container = $this->getContainer();
        $database = $container->get(DatabaseInterface::class);

        $freewarDumpService = new FreewarDumpServiceDummy([
            1 => new Player(null, WorldEnum::AFSRV, 1, 'Test-1', 'Onlo', 100, 0, 100, 1, 'Magieverlängerer', new DateTimeImmutable()),
            2 => new Player(null, WorldEnum::AFSRV, 2, 'Test-2', 'Taruner', 200, 0, 200, null, null, new DateTimeImmutable()),
            3 => new Player(null, WorldEnum::AFSRV, 3, 'Test-3', 'Serum-Geist', 300, 0, 300, null, 'Sammler', new DateTimeImmutable()),
        ]);

        $container->set(FreewarDumpServiceInterface::class, $freewarDumpService);

        $container->get(PlayerRepository::class)->insertPlayers(WorldEnum::AFSRV, [
            new Player(null, WorldEnum::AFSRV, 1, 'Test-1', 'Onlo', 100, 0, 100, null, null, new DateTimeImmutable()),
            new Player(null, WorldEnum::AFSRV, 2, 'Test-2', 'Taruner', 200, 0, 200, null, 'Maschinenbauer', new DateTimeImmutable()),
            new Player(null, WorldEnum::AFSRV, 3, 'Test-3', 'Keuroner', 300, 0, 300, null, 'Schützer', new DateTimeImmutable()),
        ]);

        $this->assertCount(0, $database->select("SELECT * FROM players_clan_history"));

        $container->get(PlayerImporter::class)->import(WorldEnum::AFSRV);

        $histories = $database->select("SELECT * FROM players_clan_history");

        $this->assertCount(1, $histories);

        $this->assertIsArray($histories[0]);
        $this->assertSame(1, $histories[0]['player_id']);
        $this->assertNull($histories[0]['old_clan_id']);
        $this->assertNull($histories[0]['new_clan_id']);
        $this->assertNull($histories[0]['old_shortcut']);
        $this->assertNull($histories[0]['new_shortcut']);
        $this->assertNull($histories[0]['old_name']);
        $this->assertNull($histories[0]['new_name']);
    }

    public function test_it_will_create_player_clan_change_history(): void
    {
        $container = $this->getContainer();
        $database = $container->get(DatabaseInterface::class);

        $container->get(ClanRepository::class)->insertClans(WorldEnum::AFSRV, [
            new Clan(null, WorldEnum::AFSRV, 1, 'test-1', 'Test-1', 100, 0, 0, 0, new DateTimeImmutable()),
            new Clan(null, WorldEnum::AFSRV, 2, 'test-2', 'Test-2', 200, 0, 0, 0, new DateTimeImmutable()),
            new Clan(null, WorldEnum::AFSRV, 3, 'test-3', 'Test-3', 300, 0, 0, 0, new DateTimeImmutable()),
        ]);

        $freewarDumpService = new FreewarDumpServiceDummy([
            1 => new Player(null, WorldEnum::AFSRV, 1, 'Test-1', 'Onlo', 100, 0, 100, 1, null, new DateTimeImmutable()),
            2 => new Player(null, WorldEnum::AFSRV, 2, 'Test-2', 'Taruner', 200, 0, 200, 2, null, new DateTimeImmutable()),
            3 => new Player(null, WorldEnum::AFSRV, 3, 'Test-3', 'Serum-Geist', 300, 0, 300, null, null, new DateTimeImmutable()),
        ]);

        $container->set(FreewarDumpServiceInterface::class, $freewarDumpService);

        $container->get(PlayerRepository::class)->insertPlayers(WorldEnum::AFSRV, [
            new Player(null, WorldEnum::AFSRV, 1, 'Test-1', 'Onlo', 100, 0, 100, null, null, new DateTimeImmutable()),
            new Player(null, WorldEnum::AFSRV, 2, 'Test-2', 'Taruner', 200, 0, 200, 1, null, new DateTimeImmutable()),
            new Player(null, WorldEnum::AFSRV, 3, 'Test-3', 'Serum-Geist', 300, 0, 300, 3, null, new DateTimeImmutable()),
        ]);

        $this->assertCount(0, $database->select("SELECT * FROM players_clan_history"));

        $container->get(PlayerImporter::class)->import(WorldEnum::AFSRV);

        $histories = $database->select("SELECT * FROM players_clan_history");

        $this->assertCount(3, $histories);

        $this->assertIsArray($histories[0]);
        $this->assertSame(1, $histories[0]['player_id']);
        $this->assertNull($histories[0]['old_clan_id']);
        $this->assertSame(1, $histories[0]['new_clan_id']);
        $this->assertNull($histories[0]['old_shortcut']);
        $this->assertSame('test-1', $histories[0]['new_shortcut']);
        $this->assertNull($histories[0]['old_name']);
        $this->assertSame('Test-1', $histories[0]['new_name']);

        $this->assertIsArray($histories[1]);
        $this->assertSame(2, $histories[1]['player_id']);
        $this->assertSame(1, $histories[1]['old_clan_id']);
        $this->assertSame(2, $histories[1]['new_clan_id']);
        $this->assertSame('test-1', $histories[1]['old_shortcut']);
        $this->assertSame('test-2', $histories[1]['new_shortcut']);
        $this->assertSame('Test-1', $histories[1]['old_name']);
        $this->assertSame('Test-2', $histories[1]['new_name']);

        $this->assertIsArray($histories[2]);
        $this->assertSame(3, $histories[2]['player_id']);
        $this->assertSame(3, $histories[2]['old_clan_id']);
        $this->assertNull($histories[2]['new_clan_id']);
        $this->assertSame('test-3', $histories[2]['old_shortcut']);
        $this->assertNull($histories[2]['new_shortcut']);
        $this->assertSame('Test-3', $histories[2]['old_name']);
        $this->assertNull($histories[2]['new_name']);
    }

    public function test_it_will_create_a_new_banned_player_status_history_entry_when_player_is_not_found_in_dump_anymore(): void
    {
        $container = $this->getContainer();
        $database = $container->get(DatabaseInterface::class);

        $container->set(PlayerStatusServiceInterface::class, new PlayerStatusServiceDummy(PlayerStatusEnum::BANNED));

        $freewarDumpService = new FreewarDumpServiceDummy([
            1 => new Player(null, WorldEnum::AFSRV, 1, 'Test-1', 'Onlo', 100, 0, 100, null, null, new DateTimeImmutable()),
            2 => new Player(null, WorldEnum::AFSRV, 2, 'Test-2', 'Taruner', 200, 0, 200, null, null, new DateTimeImmutable()),
        ]);

        $container->set(FreewarDumpServiceInterface::class, $freewarDumpService);

        $container->get(PlayerRepository::class)->insertPlayers(WorldEnum::AFSRV, [
            new Player(null, WorldEnum::AFSRV, 1, 'Test-1', 'Onlo', 100, 0, 100, null, null, new DateTimeImmutable()),
            new Player(null, WorldEnum::AFSRV, 2, 'Test-2', 'Taruner', 200, 0, 200, null, null, new DateTimeImmutable()),
            new Player(null, WorldEnum::AFSRV, 3, 'Test-3', 'Serum-Geist', 300, 0, 300, null, null, new DateTimeImmutable()),
        ]);

        $this->assertCount(0, $database->select("SELECT * FROM players_status_history"));

        $container->get(PlayerImporter::class)->import(WorldEnum::AFSRV);

        $histories = $database->select("SELECT * FROM players_status_history");

        $this->assertCount(1, $histories);
        $this->assertIsArray($histories[0]);
        $this->assertSame(3, $histories[0]['player_id']);
        $this->assertSame('Test-3', $histories[0]['name']);
        $this->assertSame('banned', $histories[0]['status']);
        $this->assertNull($histories[0]['deleted']);
    }

    public function test_it_will_create_a_new_deleted_player_status_history_entry_when_player_is_not_found_in_dump_anymore(): void
    {
        $container = $this->getContainer();
        $database = $container->get(DatabaseInterface::class);

        $container->set(PlayerStatusServiceInterface::class, new PlayerStatusServiceDummy(PlayerStatusEnum::DELETED));

        $freewarDumpService = new FreewarDumpServiceDummy([
            1 => new Player(null, WorldEnum::AFSRV, 1, 'Test-1', 'Onlo', 100, 0, 100, null, null, new DateTimeImmutable()),
            2 => new Player(null, WorldEnum::AFSRV, 2, 'Test-2', 'Taruner', 200, 0, 200, null, null, new DateTimeImmutable()),
        ]);

        $container->set(FreewarDumpServiceInterface::class, $freewarDumpService);

        $container->get(PlayerRepository::class)->insertPlayers(WorldEnum::AFSRV, [
            new Player(null, WorldEnum::AFSRV, 1, 'Test-1', 'Onlo', 100, 0, 100, null, null, new DateTimeImmutable()),
            new Player(null, WorldEnum::AFSRV, 2, 'Test-2', 'Taruner', 200, 0, 200, null, null, new DateTimeImmutable()),
            new Player(null, WorldEnum::AFSRV, 3, 'Test-3', 'Serum-Geist', 300, 0, 300, null, null, new DateTimeImmutable()),
        ]);

        $this->assertCount(0, $database->select("SELECT * FROM players_status_history"));

        $container->get(PlayerImporter::class)->import(WorldEnum::AFSRV);

        $histories = $database->select("SELECT * FROM players_status_history");

        $this->assertCount(1, $histories);
        $this->assertIsArray($histories[0]);
        $this->assertSame(3, $histories[0]['player_id']);
        $this->assertSame('Test-3', $histories[0]['name']);
        $this->assertSame('deleted', $histories[0]['status']);
        $this->assertNull($histories[0]['deleted']);
    }

    public function test_it_will_not_create_a_player_status_history_entry_when_status_is_unknown(): void
    {
        $container = $this->getContainer();
        $database = $container->get(DatabaseInterface::class);

        $container->set(PlayerStatusServiceInterface::class, new PlayerStatusServiceDummy(PlayerStatusEnum::UNKNOWN));

        $freewarDumpService = new FreewarDumpServiceDummy([
            1 => new Player(null, WorldEnum::AFSRV, 1, 'Test-1', 'Onlo', 100, 0, 100, null, null, new DateTimeImmutable()),
            2 => new Player(null, WorldEnum::AFSRV, 2, 'Test-2', 'Taruner', 200, 0, 200, null, null, new DateTimeImmutable()),
        ]);

        $container->set(FreewarDumpServiceInterface::class, $freewarDumpService);

        $container->get(PlayerRepository::class)->insertPlayers(WorldEnum::AFSRV, [
            new Player(null, WorldEnum::AFSRV, 1, 'Test-1', 'Onlo', 100, 0, 100, null, null, new DateTimeImmutable()),
            new Player(null, WorldEnum::AFSRV, 2, 'Test-2', 'Taruner', 200, 0, 200, null, null, new DateTimeImmutable()),
            new Player(null, WorldEnum::AFSRV, 3, 'Test-3', 'Serum-Geist', 300, 0, 300, null, null, new DateTimeImmutable()),
        ]);

        $this->assertCount(0, $database->select("SELECT * FROM players_status_history"));

        $container->get(PlayerImporter::class)->import(WorldEnum::AFSRV);

        $this->assertCount(0, $database->select("SELECT * FROM players_status_history"));
    }

    public function test_it_when_player_dump_has_a_new_player_which_has_open_status_history_it_will_be_updated(): void
    {
        $container = $this->getContainer();
        $database = $container->get(DatabaseInterface::class);

        $container->get(PlayerStatusHistoryRepository::class)->insert(new PlayerStatusHistory(null, WorldEnum::AFSRV, 4, 'Test-4', PlayerStatusEnum::BANNED, new DateTimeImmutable(), null, new DateTimeImmutable()));

        $container->set(FreewarDumpServiceInterface::class, new FreewarDumpServiceDummy([
            1 => new Player(null, WorldEnum::AFSRV, 1, 'Test-1', 'Onlo', 100, 0, 100, null, null, new DateTimeImmutable()),
            2 => new Player(null, WorldEnum::AFSRV, 2, 'Test-2', 'Taruner', 200, 0, 200, null, null, new DateTimeImmutable()),
            3 => new Player(null, WorldEnum::AFSRV, 3, 'Test-3', 'Serum-Geist', 300, 0, 300, null, null, new DateTimeImmutable()),
            4 => new Player(null, WorldEnum::AFSRV, 4, 'Test-4', 'Keuroner', 400, 0, 400, null, null, new DateTimeImmutable()),
        ]));

        $container->get(PlayerRepository::class)->insertPlayers(WorldEnum::AFSRV, [
            new Player(null, WorldEnum::AFSRV, 1, 'Test-1', 'Onlo', 100, 0, 100, null, null, new DateTimeImmutable()),
            new Player(null, WorldEnum::AFSRV, 2, 'Test-2', 'Taruner', 200, 0, 200, null, null, new DateTimeImmutable()),
            new Player(null, WorldEnum::AFSRV, 3, 'Test-3', 'Serum-Geist', 300, 0, 300, null, null, new DateTimeImmutable()),
        ]);

        $histories = $database->select("SELECT * FROM players_status_history");

        $this->assertCount(1, $histories);
        $this->assertIsArray($histories[0]);
        $this->assertSame(1, $histories[0]['id']);
        $this->assertNull($histories[0]['deleted']);

        $container->get(PlayerImporter::class)->import(WorldEnum::AFSRV);

        $histories = $database->select("SELECT * FROM players_status_history");

        $this->assertCount(1, $histories);
        $this->assertIsArray($histories[0]);
        $this->assertSame(1, $histories[0]['id']);
        $this->assertNotNull($histories[0]['deleted']);

        $this->assertCount(4, $database->select("SELECT * FROM players"));
    }
}
