<?php

require_once('RequestCase.php');

class ValidationTest extends RequestCase
{
    public function assertArrayKeysEquals(array $keys, array $array)
    {
        $arraykeys = array_keys($array);

        foreach ($keys as $key) {
            $this->assertContains($key, $arraykeys);
        }

        // Use the whole array to keep the value in the error message
        // Use json_encode so that the value is displayed (if not, you'll only have "Array ()" as value)
        $this->assertEquals('[]', json_encode(array_diff_key($array, array_flip($keys))), 'Unexpected keys');
    }

    // Nothing sent, recaptcha needed, at least a name
    public function testEmpty()
    {
        $content = $this->ajaxPost('/', ['a' => 'b'], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'name'], $content);
    }

    // Just names, need recaptcha and contact informations
    public function testContactInfo()
    {
        // At least 2 names!
        $content = $this->ajaxPost('/', ['name' => ['toto']], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'name', 'email.0', 'phone.0'], $content);

        // Ok for names but duplicates this time but no contact infos
        $content = $this->ajaxPost('/', ['name' => ['toto', 'toto']], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'name', 'email.0', 'phone.0', 'email.1', 'phone.1'], $content);

        // Ok for names but no contact infos
        $content = $this->ajaxPost('/', ['name' => ['toto', 'tata']], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'email.0', 'phone.0', 'email.1', 'phone.1'], $content);

        // Ok for names but partial contact infos (sms)
        $content = $this->ajaxPost('/', ['name' => ['toto', 'tata'], 'phone' => ['0612345678', '']], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'email.1', 'phone.1', 'contentSMS'], $content);

        // Ok for names but partial contact infos (mail)
        $content = $this->ajaxPost('/', ['name' => ['toto', 'tata'], 'email' => ['', 'test@test.com']], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'email.0', 'phone.0', 'title', 'contentMail'], $content);
    }

    // Names and contact infos but no mail body nor sms body
    public function testContactBodiesMail()
    {
        $content = $this->ajaxPost('/', ['name' => ['toto', 'tata'], 'email' => ['invalid@invalidformat', 'test@test.com']], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'email.0', 'title', 'contentMail'], $content);

        $content = $this->ajaxPost('/', ['name' => ['toto', 'tata'], 'email' => ['test@test.com', 'test@test.com']], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'title', 'contentMail'], $content);
    }

    // Names and contact infos but no mail body nor sms body
    public function testContactBodiesSms()
    {
        $content = $this->ajaxPost('/', ['name' => ['toto', 'tata'], 'phone' => ['0612345678', '55213249jgh']], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'phone.1', 'contentSMS'], $content);

        $content = $this->ajaxPost('/', ['name' => ['toto', 'tata'], 'phone' => ['0612345678', '612345678']], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'contentSMS'], $content);
    }

    // Names and contact infos but no mail body nor sms body
    public function testContactBodiesBoth()
    {
        $content = $this->ajaxPost('/', [
            'name' => ['toto', 'tata', 'tutu'], 'email' => ['', 'test@test.com', 'test@test.com'], 'phone' => ['0612345678', '0612345678', ''], 'exclusions' => []
        ], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'title', 'contentMail', 'contentSMS'], $content);
    }

    // Names and exclusionss
    public function testExclusionss()
    {
        $content = $this->ajaxPost('/', ['name' => ['toto', 'tata'], 'exclusions' => [['87']]], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'exclusions.0.0', 'phone.0', 'phone.1', 'email.0', 'email.1'], $content);

        $content = $this->ajaxPost('/', ['name' => ['toto', 'tata'], 'exclusions' => [['87']]], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'exclusions.0.0', 'phone.0', 'phone.1', 'email.0', 'email.1'], $content);

        $content = $this->ajaxPost('/', ['name' => ['toto', 'tata'], 'exclusions' => [['0']]], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'exclusions.0.0', 'phone.0', 'phone.1', 'email.0', 'email.1'], $content);

        $content = $this->ajaxPost('/', ['name' => ['toto', 'tata'], 'exclusions' => [['1']]], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'phone.0', 'phone.1', 'email.0', 'email.1'], $content);
    }

    // Sms limit
    public function testSmsLimit()
    {
        config(['sms.max' => 1]);
        $content = $this->ajaxPost('/', [
            'name'                 => ['toto', 'tata'],
            'phone'                => ['0612345678', '0612345678'],
            'contentSMS'           => '{TARGET}'.implode('', array_fill(0, 161, 'a')), // 2 sms long
        ], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'contentSMS'], $content);
    }
}
