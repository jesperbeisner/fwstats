<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Tests\Integration\Repository;

use DateTimeImmutable;
use Jesperbeisner\Fwstats\Enum\WorldEnum;
use Jesperbeisner\Fwstats\Model\ClanNameHistory;
use Jesperbeisner\Fwstats\Repository\ClanNameHistoryRepository;
use Jesperbeisner\Fwstats\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ClanNameHistoryRepository::class)]
final class ClanNameHistoryRepositoryTest extends AbstractTestCase
{
    private ClanNameHistoryRepository $clanNameHistoryRepository;

    protected function setUp(): void
    {
        $this->setUpContainer();
        $this->setUpDatabase();

        $this->clanNameHistoryRepository = $this->getContainer()->get(ClanNameHistoryRepository::class);
    }

    public function test_insert(): void
    {
        $clanNameHistory = new ClanNameHistory(null, WorldEnum::AFSRV, 1, 'o.O', 'o.O', 'test1', 'test2', new DateTimeImmutable());
        $newClanNameHistory = $this->clanNameHistoryRepository->insert($clanNameHistory);

        $this->assertSame(1, $newClanNameHistory->id);
        $this->assertNotSame($clanNameHistory, $newClanNameHistory);
    }
}
