<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase {
        migrateFreshUsing as private migrateFreshUsingBase;
    }

    protected function migrateFreshUsing()
    {
        return array_merge(
            [
                // Specify the schema path as it's the same for mysql and testing connections
                '--schema-path' => 'database/schema/mysql-schema.sql'
            ],
            $this->migrateFreshUsingBase()
        );
    }
}
