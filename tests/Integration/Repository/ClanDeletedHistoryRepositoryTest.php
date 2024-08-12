<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Tests\Integration\Repository;

use DateTimeImmutable;
use Jesperbeisner\Fwstats\Enum\WorldEnum;
use Jesperbeisner\Fwstats\Model\ClanDeletedHistory;
use Jesperbeisner\Fwstats\Repository\ClanDeletedHistoryRepository;
use Jesperbeisner\Fwstats\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ClanDeletedHistoryRepository::class)]
final class ClanDeletedHistoryRepositoryTest extends AbstractTestCase
{
    private ClanDeletedHistoryRepository $clanDeletedHistoryRepository;

    protected function setUp(): void
    {
        $this->setUpContainer();
        $this->setUpDatabase();

        $this->clanDeletedHistoryRepository = $this->getContainer()->get(ClanDeletedHistoryRepository::class);
    }

    public function test_insert(): void
    {
        $clanDeletedHistory = new ClanDeletedHistory(null, WorldEnum::AFSRV, 1, 'o.O', 'test', 1, 1, 1, 1, new DateTimeImmutable());
        $newClanDeletedHistory = $this->clanDeletedHistoryRepository->insert($clanDeletedHistory);

        $this->assertSame(1, $newClanDeletedHistory->id);
        $this->assertNotSame($clanDeletedHistory, $newClanDeletedHistory);
    }
}
