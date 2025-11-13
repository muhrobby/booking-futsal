<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * Override the default database driver for tests.
     */
    protected function defineEnvironment($app)
    {
        $app['config']->set('database.default', 'testing');
    }
}
