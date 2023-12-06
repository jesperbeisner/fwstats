<?php

declare(strict_types=1);

namespace Fwstats\Http\Controllers;

use Illuminate\Contracts\View\View;

final readonly class IndexController
{
    public function index(): View
    {
        return view('index');
    }
}
