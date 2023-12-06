<?php

declare(strict_types=1);

namespace Fwstats\Console;

use Illuminate\Foundation\Console\Kernel as BaseConsoleKernel;

final class Kernel extends BaseConsoleKernel
{
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
    }
}
