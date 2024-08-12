<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Tests\Unit\Service;

use DateTimeImmutable;
use Jesperbeisner\Fwstats\Enum\WorldEnum;
use Jesperbeisner\Fwstats\Model\Player;
use Jesperbeisner\Fwstats\Model\PlayerXpHistory;
use Jesperbeisner\Fwstats\Repository\PlayerRepository;
use Jesperbeisner\Fwstats\Repository\PlayerXpHistoryRepository;
use Jesperbeisner\Fwstats\Service\XpService;
use Jesperbeisner\Fwstats\Tests\Doubles\DatabaseDummy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(XpService::class)]
final class XpServiceTest extends TestCase
{
    public function test_it_returns_an_array_with_the_last_7_days_as_keys_and_null_as_values_when_no_xp_histories_are_found(): void
    {
        $database = new DatabaseDummy();
        $database->setSelectReturn([]);

        $playerXpHistoryRepository = new PlayerXpHistoryRepository($database);
        $playerRepository = new PlayerRepository($database);
        $xpService = new XpService($playerXpHistoryRepository, $playerRepository);

        $xpChanges = $xpService->getXpChangesForPlayer(new Player(null, WorldEnum::AFSRV, 1, 'test', 'Onlo', 1, 1, 2, null, null, new DateTimeImmutable()), 7);

        $this->assertCount(7, $xpChanges);
        $this->assertSame([
            (new DateTimeImmutable())->format('Y-m-d') => null,
            (new DateTimeImmutable('-1 days'))->format('Y-m-d') => null,
            (new DateTimeImmutable('-2 days'))->format('Y-m-d') => null,
            (new DateTimeImmutable('-3 days'))->format('Y-m-d') => null,
            (new DateTimeImmutable('-4 days'))->format('Y-m-d') => null,
            (new DateTimeImmutable('-5 days'))->format('Y-m-d') => null,
            (new DateTimeImmutable('-6 days'))->format('Y-m-d') => null,
        ], $xpChanges);
    }

    public function test_it_returns_an_array_with_the_last_7_days_as_keys_and_some_non_null_values_when_histories_are_found(): void
    {
        $database = new DatabaseDummy();
        $database->setSelectReturn([
            ['id' => 1, 'world' => 'afsrv', 'player_id' => 1, 'start_xp' => 0, 'end_xp' => 100, 'day' => (new DateTimeImmutable('-3 days'))->setTime(0, 0, 0)->format('Y-m-d H:i:s')],
            ['id' => 2, 'world' => 'afsrv', 'player_id' => 1, 'start_xp' => 100, 'end_xp' => 200, 'day' => (new DateTimeImmutable('-4 days'))->setTime(0, 0, 0)->format('Y-m-d H:i:s')],
        ]);

        $playerXpHistoryRepository = new PlayerXpHistoryRepository($database);
        $playerRepository = new PlayerRepository($database);
        $xpService = new XpService($playerXpHistoryRepository, $playerRepository);

        $xpChanges = $xpService->getXpChangesForPlayer(new Player(null, WorldEnum::AFSRV, 1, 'test', 'Onlo', 1, 1, 2, null, null, new DateTimeImmutable()), 7);

        $this->assertCount(7, $xpChanges);
        $this->assertArrayHasKey((new DateTimeImmutable('-3 days'))->format('Y-m-d'), $xpChanges);
        $this->assertInstanceOf(PlayerXpHistory::class, $xpChanges[(new DateTimeImmutable('-3 days'))->format('Y-m-d')]);
        $this->assertArrayHasKey((new DateTimeImmutable('-4 days'))->format('Y-m-d'), $xpChanges);
        $this->assertInstanceOf(PlayerXpHistory::class, $xpChanges[(new DateTimeImmutable('-4 days'))->format('Y-m-d')]);
        $this->assertArrayHasKey((new DateTimeImmutable())->format('Y-m-d'), $xpChanges);
        $this->assertNull($xpChanges[(new DateTimeImmutable())->format('Y-m-d')]);
        $this->assertArrayHasKey((new DateTimeImmutable('-5 days'))->format('Y-m-d'), $xpChanges);
        $this->assertNull($xpChanges[(new DateTimeImmutable('-5 days'))->format('Y-m-d')]);
    }

    public function test_it_returns_an_array_with_the_length_of_days(): void
    {
        $database = new DatabaseDummy();
        $database->setSelectReturn([]);

        $playerXpHistoryRepository = new PlayerXpHistoryRepository($database);
        $playerRepository = new PlayerRepository($database);
        $xpService = new XpService($playerXpHistoryRepository, $playerRepository);

        $xpChanges = $xpService->getXpChangesForPlayer(new Player(null, WorldEnum::AFSRV, 1, 'test', 'Onlo', 1, 1, 2, null, null, new DateTimeImmutable()), 100);

        $this->assertCount(100, $xpChanges);
    }
}
