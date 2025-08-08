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
}
