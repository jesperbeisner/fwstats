<?php

declare(strict_types=1);

namespace Fwstats\Enums;

enum WorldEnum: string
{
    case WORLD_1 = 'welt1';
    case WORLD_2 = 'welt2';
    case WORLD_3 = 'welt3';
    case WORLD_4 = 'welt4';
    case WORLD_5 = 'welt5';
    case WORLD_6 = 'welt6';
    case WORLD_7 = 'welt7';
    case WORLD_8 = 'welt8';
    case WORLD_9 = 'welt9';
    case WORLD_10 = 'welt10';
    case WORLD_11 = 'welt11';
    case WORLD_12 = 'welt12';
    case WORLD_13 = 'welt13';
    case WORLD_RP = 'rpsrv';
    case WORLD_AF = 'afsrv';
    case WORLD_CHAOS = 'chaos';

    public function getText(): string
    {
        return match ($this) {
            WorldEnum::WORLD_1 => 'Welt 1',
            WorldEnum::WORLD_2 => 'Welt 2',
            WorldEnum::WORLD_3 => 'Welt 3',
            WorldEnum::WORLD_4 => 'Welt 4',
            WorldEnum::WORLD_5 => 'Welt 5',
            WorldEnum::WORLD_6 => 'Welt 6',
            WorldEnum::WORLD_7 => 'Welt 7',
            WorldEnum::WORLD_8 => 'Welt 8',
            WorldEnum::WORLD_9 => 'Welt 9',
            WorldEnum::WORLD_10 => 'Welt 10',
            WorldEnum::WORLD_11 => 'Welt 11',
            WorldEnum::WORLD_12 => 'Welt 12',
            WorldEnum::WORLD_13 => 'Welt 13',
            WorldEnum::WORLD_RP => 'Rollenspiel-Welt',
            WorldEnum::WORLD_AF => 'Action-Freewar',
            WorldEnum::WORLD_CHAOS => 'Chaos-Welt',
        };
    }
}
