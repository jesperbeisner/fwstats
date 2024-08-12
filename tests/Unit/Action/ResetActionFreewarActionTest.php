<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Tests\Unit\Action;

use Jesperbeisner\Fwstats\Action\ResetActionFreewarAction;
use Jesperbeisner\Fwstats\Repository\AchievementRepository;
use Jesperbeisner\Fwstats\Repository\ClanCreatedHistoryRepository;
use Jesperbeisner\Fwstats\Repository\ClanDeletedHistoryRepository;
use Jesperbeisner\Fwstats\Repository\ClanNameHistoryRepository;
use Jesperbeisner\Fwstats\Repository\ClanRepository;
use Jesperbeisner\Fwstats\Repository\PlayerActiveSecondRepository;
use Jesperbeisner\Fwstats\Repository\PlayerClanHistoryRepository;
use Jesperbeisner\Fwstats\Repository\PlayerNameHistoryRepository;
use Jesperbeisner\Fwstats\Repository\PlayerProfessionHistoryRepository;
use Jesperbeisner\Fwstats\Repository\PlayerRaceHistoryRepository;
use Jesperbeisner\Fwstats\Repository\PlayerRepository;
use Jesperbeisner\Fwstats\Repository\PlayerStatusHistoryRepository;
use Jesperbeisner\Fwstats\Tests\Doubles\DatabaseDummy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ResetActionFreewarAction::class)]
final class ResetActionFreewarActionTest extends TestCase
{
    public function test_it_returns_a_success_ActionResult_when_everything_works(): void
    {
        $database = new DatabaseDummy();

        $achievementRepository = new AchievementRepository($database);
        $clanCreatedHistoryRepository = new ClanCreatedHistoryRepository($database);
        $clanDeletedHistoryRepository = new ClanDeletedHistoryRepository($database);
        $clanNameHistoryRepository = new ClanNameHistoryRepository($database);
        $clanRepository = new ClanRepository($database);
        $playerActiveSecondRepository = new PlayerActiveSecondRepository($database);
        $playerClanHistoryRepository = new PlayerClanHistoryRepository($database);
        $playerNameHistoryRepository = new PlayerNameHistoryRepository($database);
        $playerProfessionHistoryRepository = new PlayerProfessionHistoryRepository($database);
        $playerRaceHistoryRepository = new PlayerRaceHistoryRepository($database);
        $playerRepository = new PlayerRepository($database);
        $playerStatusHistoryRepository = new PlayerStatusHistoryRepository($database);

        $resetActionFreewarAction = new ResetActionFreewarAction(
            $achievementRepository,
            $clanCreatedHistoryRepository,
            $clanDeletedHistoryRepository,
            $clanNameHistoryRepository,
            $clanRepository,
            $playerActiveSecondRepository,
            $playerClanHistoryRepository,
            $playerNameHistoryRepository,
            $playerProfessionHistoryRepository,
            $playerRaceHistoryRepository,
            $playerRepository,
            $playerStatusHistoryRepository,
        );

        $result = $resetActionFreewarAction->configure([])->run();

        $this->assertTrue($result->isSuccess());
        $this->assertSame('text.reset-action-freewar-success', $result->getMessage());
    }
}
