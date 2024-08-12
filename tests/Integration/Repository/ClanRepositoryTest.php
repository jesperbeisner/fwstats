<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Tests\Integration\Repository;

use DateTimeImmutable;
use Jesperbeisner\Fwstats\Enum\WorldEnum;
use Jesperbeisner\Fwstats\Model\Clan;
use Jesperbeisner\Fwstats\Repository\ClanRepository;
use Jesperbeisner\Fwstats\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ClanRepository::class)]
final class ClanRepositoryTest extends AbstractTestCase
{
    private ClanRepository $clanRepository;

    protected function setUp(): void
    {
        $this->setUpContainer();
        $this->setUpDatabase();

        $this->clanRepository = $this->getContainer()->get(ClanRepository::class);
    }

    public function test_insert(): void
    {
        $clan = new Clan(null, WorldEnum::AFSRV, 1, 'o.O', 'test', 1, 1, 1, 1, new DateTimeImmutable());
        $newClan = $this->clanRepository->insert($clan);

        $this->assertSame(1, $newClan->id);
        $this->assertNotSame($clan, $newClan);
    }

    public function test_findAllByWorld_without_clans_available(): void
    {
        $clans = $this->clanRepository->findAllByWorld(WorldEnum::AFSRV);

        $this->assertSame([], $clans);
    }

    public function test_findAllByWorld_with_clans_available(): void
    {
        $clan = new Clan(null, WorldEnum::AFSRV, 100, 'o.O', 'test', 1, 1, 1, 1, new DateTimeImmutable());
        $this->clanRepository->insert($clan);

        $clans = $this->clanRepository->findAllByWorld(WorldEnum::AFSRV);

        $this->assertArrayHasKey(100, $clans);
    }
}
