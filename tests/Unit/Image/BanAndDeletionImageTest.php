<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Tests\Unit\Image;

use Generator;
use Jesperbeisner\Fwstats\Enum\WorldEnum;
use Jesperbeisner\Fwstats\Image\BanAndDeletionImage;
use Jesperbeisner\Fwstats\Repository\PlayerStatusHistoryRepository;
use Jesperbeisner\Fwstats\Tests\Doubles\DatabaseDummy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(BanAndDeletionImage::class)]
final class BanAndDeletionImageTest extends TestCase
{
    #[DataProvider('provideWorldEnumData')]
    public function test_it_creates_an_image_successfully(WorldEnum $worldEnum): void
    {
        $fileName = __DIR__ . sprintf('/../../../var/%s-bans-and-deletions-test.png', $worldEnum->value);

        $database = new DatabaseDummy();
        $database->setSelectReturn([
            ['id' => 1, 'world' => $worldEnum->value, 'player_id' => 1, 'name' => 'Test-1', 'status' => 'deleted', 'created' => date('Y-m-d H:i:s'), 'deleted' => null, 'updated' => date('Y-m-d H:i:s')],
            ['id' => 2, 'world' => $worldEnum->value, 'player_id' => 2, 'name' => 'Test-2', 'status' => 'deleted', 'created' => date('Y-m-d H:i:s'), 'deleted' => null, 'updated' => date('Y-m-d H:i:s')],
            ['id' => 3, 'world' => $worldEnum->value, 'player_id' => 3, 'name' => 'Test-3', 'status' => 'deleted', 'created' => date('Y-m-d H:i:s'), 'deleted' => date('Y-m-d H:i:s'), 'updated' => date('Y-m-d H:i:s')],
        ]);

        $playerStatusHistoryRepository = new PlayerStatusHistoryRepository($database);

        $banAndDeletionImage = new BanAndDeletionImage(
            __DIR__ . '/../../../var',
            __DIR__ . '/../../../data/Roboto-Light.ttf',
            'bans-and-deletions-test.png',
            $playerStatusHistoryRepository
        );

        $this->assertFileDoesNotExist($fileName);

        $banAndDeletionImage->create($worldEnum);

        $this->assertFileExists($fileName);
        $this->assertTrue(unlink($fileName));
    }

    /**
     * @return Generator<array<WorldEnum>>
     */
    public static function provideWorldEnumData(): Generator
    {
        yield 'ActionFreewar' => [WorldEnum::AFSRV];
        yield 'ChaosFreewar' => [WorldEnum::CHAOS];
    }
}
