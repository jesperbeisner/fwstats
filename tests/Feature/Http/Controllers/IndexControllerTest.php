<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers;

use App\Http\Controllers\IndexController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

#[CoversClass(IndexController::class)]
final class IndexControllerTest extends AbstractTestCase
{
    #[Test]
    public function smokeTest(): void
    {
        $this
            ->get(route('index'))
            ->assertOk();
    }
}
