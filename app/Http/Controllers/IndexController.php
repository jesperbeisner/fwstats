<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

/**
 * @see \Tests\Feature\Http\Controllers\IndexControllerTest
 */
final readonly class IndexController
{
    public function __invoke(): View
    {
        return view('index');
    }
}
