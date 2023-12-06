<?php

declare(strict_types=1);

namespace Fwstats\Tests\Feature;

use Fwstats\Tests\AbstractTestCase;

final class ExampleAbstractTest extends AbstractTestCase
{
    public function test_the_application_returns_a_successful_response(): void
    {
        $this
            ->get('/')
            ->assertOk();
    }
}
