
<?php

class SmsTest extends TestCase
{
    public function testCount()
    {
        $this->assertEquals(0, SmsTools::count(''));
        $this->assertEquals(1, SmsTools::count('e'));

        $this->assertEquals(1, SmsTools::count(implode('', array_fill(0, 160, 'a'))));
        $this->assertEquals(2, SmsTools::count(implode('', array_fill(0, 161, 'a'))));
    }
}
