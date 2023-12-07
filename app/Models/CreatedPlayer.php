<?php

declare(strict_types=1);

namespace Fwstats\Models;

use DateTime;
use Fwstats\Enums\WorldEnum;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $player_id
 * @property WorldEnum $player_world
 * @property DateTime $created_at
 */
final class CreatedPlayer extends Model
{
    protected $casts = [
        'world' => WorldEnum::class,
    ];
}
