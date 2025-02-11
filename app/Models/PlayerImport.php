<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\ProfessionEnum;
use App\Enums\RaceEnum;
use App\Enums\WorldEnum;
use Illuminate\Database\Eloquent\Model;

/**
 * @property-read int $id
 * @property WorldEnum $world
 * @property int $player_id
 * @property string $name
 * @property ?RaceEnum $race
 * @property ?int $clan_id
 * @property ?ProfessionEnum $profession
 * @property int $xp
 * @property int $soul_xp
 * @property int $total_xp
 */
final class PlayerImport extends Model
{
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'world' => WorldEnum::class,
            'race' => RaceEnum::class,
            'profession' => ProfessionEnum::class,
        ];
    }
}
