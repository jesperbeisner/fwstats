<?php

declare(strict_types=1);

namespace Fwstats\Enums;

enum RaceEnum: string
{
    case ONLO = 'Onlo';
    case NATLA = 'Natla - Händler';
    case TARUNER = 'Taruner';
    case KEURONER = 'Keuroner';
    case DARK_MAGE = 'dunkler Magier';
    case SERUM_WRAITH = 'Serum-Geist';
    case HUMAN_WORKER = 'Mensch / Arbeiter';
    case HUMAN_WARRIOR = 'Mensch / Kämpfer';
    case HUMAN_SORCERER = 'Mensch / Zauberer';
}
