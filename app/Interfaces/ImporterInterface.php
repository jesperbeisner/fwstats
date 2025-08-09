<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Enums\WorldEnum;

interface ImporterInterface
{
    public function import(WorldEnum $world): void;
}
