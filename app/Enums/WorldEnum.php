<?php

declare(strict_types=1);

namespace App\Enums;

enum WorldEnum: string
{
    case World1 = 'welt1';
    case World2 = 'welt2';
    case World3 = 'welt3';
    case World4 = 'welt4';
    case World5 = 'welt5';
    case World6 = 'welt6';
    case World7 = 'welt7';
    case World8 = 'welt8';
    case World9 = 'welt9';
    case World10 = 'welt10';
    case World11 = 'welt11';
    case World12 = 'welt12';
    case World13 = 'welt13';
    case World14 = 'welt14';
    case RolePlayWorld = 'rpsrv';
    case ActionFreewar = 'afsrv';
    case ChaosWorld = 'chaos';

    public function short(): string
    {
        return match ($this) {
            self::World1 => 'W1',
            self::World2 => 'W2',
            self::World3 => 'W3',
            self::World4 => 'W4',
            self::World5 => 'W5',
            self::World6 => 'W6',
            self::World7 => 'W7',
            self::World8 => 'W8',
            self::World9 => 'W9',
            self::World10 => 'W10',
            self::World11 => 'W11',
            self::World12 => 'W12',
            self::World13 => 'W13',
            self::World14 => 'W14',
            self::RolePlayWorld => 'RP',
            self::ActionFreewar => 'AF',
            self::ChaosWorld => 'CW',
        };
    }
}
