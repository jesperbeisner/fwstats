<?php

declare(strict_types=1);

namespace Jesperbeisner\Fwstats\Tests\Unit\Image;

use Generator;
use Jesperbeisner\Fwstats\Enum\WorldEnum;
use Jesperbeisner\Fwstats\Image\NameChangeImage;
use Jesperbeisner\Fwstats\Repository\PlayerNameHistoryRepository;
use Jesperbeisner\Fwstats\Tests\Doubles\DatabaseDummy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversClass(NameChangeImage::class)]
final class NameChangeImageTest extends TestCase
{
    #[DataProvider('provideWorldEnumData')]
    public function test_it_creates_an_image_successfully(WorldEnum $worldEnum): void
    {
        $fileName = __DIR__ . sprintf('/../../../var/%s-name-changes-test.png', $worldEnum->value);

        $database = new DatabaseDummy();
        $database->setSelectReturn([
            ['id' => 1, 'world' => $worldEnum->value, 'player_id' => 1, 'old_name' => 'Test-1', 'new_name' => 'Test-New-1', 'created' => date('Y-m-d H:i:s')],
            ['id' => 2, 'world' => $worldEnum->value, 'player_id' => 2, 'old_name' => 'Test-2', 'new_name' => 'Test-New-2', 'created' => date('Y-m-d H:i:s')],
            ['id' => 3, 'world' => $worldEnum->value, 'player_id' => 3, 'old_name' => 'Test-3', 'new_name' => 'Test-New-3', 'created' => date('Y-m-d H:i:s')],
        ]);

        $playerNameHistoryRepository = new PlayerNameHistoryRepository($database);

        $nameChangeImage = new NameChangeImage(
            __DIR__ . '/../../../var',
            __DIR__ . '/../../../data/Roboto-Light.ttf',
            'name-changes-test.png',
            $playerNameHistoryRepository
        );

        $this->assertFileDoesNotExist($fileName);

        $nameChangeImage->create($worldEnum);

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
