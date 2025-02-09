<?php

declare(strict_types=1);

namespace App\Enums;

enum ProfessionEnum: string
{
    case Koch = 'Koch';
    case Sammler = 'Sammler';
    case Schatzmeister = 'Schatzmeister';
    case Maschinenbauer = 'Maschinenbauer';
    case Alchemist = 'Alchemist';
    case Schuetzer = 'Schützer';
    case Magieverlaengerer = 'Magieverlängerer';
    case Lebensgeist = 'Lebensgeist';
}
