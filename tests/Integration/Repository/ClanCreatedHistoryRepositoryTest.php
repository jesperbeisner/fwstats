<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Tests\Integration\Repository;

use DateTimeImmutable;
use Jesperbeisner\Fwstats\Enum\WorldEnum;
use Jesperbeisner\Fwstats\Model\ClanCreatedHistory;
use Jesperbeisner\Fwstats\Repository\ClanCreatedHistoryRepository;
use Jesperbeisner\Fwstats\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ClanCreatedHistoryRepository::class)]
final class ClanCreatedHistoryRepositoryTest extends AbstractTestCase
{
    private ClanCreatedHistoryRepository $clanCreatedHistoryRepository;

    protected function setUp(): void
    {
        $this->setUpContainer();
        $this->setUpDatabase();

        $this->clanCreatedHistoryRepository = $this->getContainer()->get(ClanCreatedHistoryRepository::class);
    }

    public function test_insert(): void
    {
        $clanCreatedHistory = new ClanCreatedHistory(null, WorldEnum::AFSRV, 1, 'o.O', 'test', 1, 1, 1, 1, new DateTimeImmutable());
        $newClanCreatedHistory = $this->clanCreatedHistoryRepository->insert($clanCreatedHistory);

        $this->assertSame(1, $newClanCreatedHistory->id);
        $this->assertNotSame($clanCreatedHistory, $newClanCreatedHistory);
    }
}
