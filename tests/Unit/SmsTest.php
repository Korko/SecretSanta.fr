<?php

namespace Tests\Unit;

use Facades\App\Services\SmsTools as SmsTools;
use Tests\TestCase;

class SmsTest extends TestCase
{
    public function testUnicode()
    {
        $this->assertFalse(SmsTools::isUnicode('a'));
        $this->assertTrue(SmsTools::isUnicode('ñ'));
    }

    public function testCount()
    {
        $this->assertEquals(0, SmsTools::count(''));
        $this->assertEquals(1, SmsTools::count('e'));

        $this->assertEquals(1, SmsTools::count(implode('', array_fill(0, 160, 'a'))));
        $this->assertEquals(2, SmsTools::count(implode('', array_fill(0, 161, 'a'))));
        $this->assertEquals(2, SmsTools::count('ñ'.implode('', array_fill(0, 70, 'a')))); // Unicode
    }
}
