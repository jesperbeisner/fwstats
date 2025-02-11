<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\ProfessionEnum;
use App\Enums\RaceEnum;

final readonly class Player
{
    public function __construct(
        public int $playerId,
        public string $name,
        public ?RaceEnum $race,
        public ?int $clanId,
        public ?ProfessionEnum $profession,
        public int $xp,
        public int $soulXp,
        public int $totalXp,
    ) {
    }
}
