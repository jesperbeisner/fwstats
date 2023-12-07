<?php

declare(strict_types=1);

namespace Fwstats\Models;

use DateTime;
use Fwstats\Enums\RemovedTypeEnum;
use Fwstats\Enums\WorldEnum;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $player_id
 * @property WorldEnum $player_world
 * @property RemovedTypeEnum $type
 * @property DateTime $created_at
 * @property ?DateTime $updated_at
 */
final class RemovedPlayer extends Model
{
    protected $casts = [
        'world' => WorldEnum::class,
        'type' => RemovedTypeEnum::class,
    ];
}
