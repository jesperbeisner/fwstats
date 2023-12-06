<?php

declare(strict_types=1);

namespace Fwstats\Models;

use DateTime;
use Fwstats\Enums\RaceEnum;
use Fwstats\Enums\WorldEnum;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property WorldEnum $world
 * @property string $name
 * @property int $xp
 * @property RaceEnum $race
 * @property ?int $clan_id
 * @property DateTime $created_at
 * @property DateTime $updated_at
 */
final class Player extends Model
{
    protected $casts = [
        'world' => WorldEnum::class,
        'race' => RaceEnum::class,
    ];
}
