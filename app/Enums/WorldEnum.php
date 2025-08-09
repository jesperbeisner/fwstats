<?php

declare(strict_types=1);

namespace App\Enums;

enum WorldEnum: string
{
    case Welt1 = '1';
    case Welt2 = '2';
    case Welt3 = '3';
    case Welt4 = '4';
    case Welt5 = '5';
    case Welt6 = '6';
    case Welt7 = '7';
    case Welt8 = '8';
    case Welt9 = '9';
    case Welt10 = '10';
    case Welt11 = '11';
    case Welt12 = '12';
    case Welt13 = '13';
    case Welt14 = '14';
    case RolePlay = 'RP';
    case ActionFreewar = 'AF';
    case ChaosWorld = 'CW';

    public function url(): string
    {
        return match ($this) {
            self::Welt1 => 'welt1',
            self::Welt2 => 'welt2',
            self::Welt3 => 'welt3',
            self::Welt4 => 'welt4',
            self::Welt5 => 'welt5',
            self::Welt6 => 'welt6',
            self::Welt7 => 'welt7',
            self::Welt8 => 'welt8',
            self::Welt9 => 'welt9',
            self::Welt10 => 'welt10',
            self::Welt11 => 'welt11',
            self::Welt12 => 'welt12',
            self::Welt13 => 'welt13',
            self::Welt14 => 'welt14',
            self::RolePlay => 'rpsrv',
            self::ActionFreewar => 'afsrv',
            self::ChaosWorld => 'chaos',
        };
    }
}
