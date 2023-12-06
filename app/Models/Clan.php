<?php

declare(strict_types=1);

namespace Fwstats\Models;

use DateTime;
use Fwstats\Enums\WorldEnum;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property WorldEnum $world
 * @property string $name
 * @property ?string $shortcut
 * @property ?int $leader_id
 * @property ?int $co_leader_id
 * @property DateTime $created_at
 * @property DateTime $updated_at
 */
final class Clan extends Model
{
    protected $casts = [
        'world' => WorldEnum::class,
    ];
}
