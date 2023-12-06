<?php

declare(strict_types=1);

namespace Fwstats\Tests;

use Fwstats\Tests\Traits\CreateApplicationTrait;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class AbstractTestCase extends BaseTestCase
{
    use CreateApplicationTrait;
}
