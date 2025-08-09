<?php

declare(strict_types=1);

namespace App\Enums;

enum RaceEnum: string
{
    case DunklerMagier = 'dunkler Magier';
    case Keuroner = 'Keuroner';
    case MenschArbeiter = 'Mensch/Arbeiter';
    case MenschKaempfer = 'Mensch/Kämpfer';
    case MenschZauberer = 'Mensch/Zauberer';
    case NatlaHaendler = 'Natla-Händler';
    case Onlo = 'Onlo';
    case SerumGeist = 'Serum-Geist';
    case Taruner = 'Taruner';

    public static function fromDump(string $race): ?RaceEnum
    {
        return match ($race) {
            'dunkler Magier' => RaceEnum::DunklerMagier,
            'Keuroner' => RaceEnum::Keuroner,
            'Mensch / Arbeiter' => RaceEnum::MenschArbeiter,
            'Mensch / Kämpfer' => RaceEnum::MenschKaempfer,
            'Mensch / Zauberer' => RaceEnum::MenschZauberer,
            'Natla - Händler' => RaceEnum::NatlaHaendler,
            'Onlo' => RaceEnum::Onlo,
            'Serum-Geist' => RaceEnum::SerumGeist,
            'Taruner' => RaceEnum::Taruner,
            default => null,
        };
    }
}
