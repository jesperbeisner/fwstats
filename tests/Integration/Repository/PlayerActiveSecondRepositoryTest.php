<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Tests\Integration\Repository;

use DateTimeImmutable;
use Jesperbeisner\Fwstats\Enum\WorldEnum;
use Jesperbeisner\Fwstats\Model\Player;
use Jesperbeisner\Fwstats\Model\PlayerActiveSecond;
use Jesperbeisner\Fwstats\Repository\PlayerActiveSecondRepository;
use Jesperbeisner\Fwstats\Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(PlayerActiveSecondRepository::class)]
final class PlayerActiveSecondRepositoryTest extends AbstractTestCase
{
    private PlayerActiveSecondRepository $playerActiveSecondRepository;

    protected function setUp(): void
    {
        $this->setUpContainer();
        $this->setUpDatabase();

        $this->playerActiveSecondRepository = $this->getContainer()->get(PlayerActiveSecondRepository::class);
    }

    public function test_insert(): void
    {
        $playerActiveSecond = new PlayerActiveSecond(null, WorldEnum::AFSRV, 1, 1, new DateTimeImmutable());
        $newPlayerActiveSecond = $this->playerActiveSecondRepository->insert($playerActiveSecond);

        $this->assertSame(1, $newPlayerActiveSecond->id);
        $this->assertNotSame($playerActiveSecond, $newPlayerActiveSecond);

        $playerActiveSecond2 = new PlayerActiveSecond(null, WorldEnum::AFSRV, 1, 100, new DateTimeImmutable());
        $newPlayerActiveSecond2 = $this->playerActiveSecondRepository->insert($playerActiveSecond2);

        $this->assertSame(1, $newPlayerActiveSecond2->id);
        $this->assertNotSame($playerActiveSecond2, $newPlayerActiveSecond2);
        $this->assertSame(100, $newPlayerActiveSecond2->seconds);
    }

    public function test_getPlaytimesForPlayer(): void
    {
        $result = $this->playerActiveSecondRepository->getPlaytimesForPlayer(new Player(null, WorldEnum::AFSRV, 1, 'test', 'Onlo', 1, 1, 1, null, null, new DateTimeImmutable()), 3);

        $this->assertSame(['day_1' => null, 'day_2' => null, 'day_3' => null, 'day_4' => null] ,$result);
    }
}
