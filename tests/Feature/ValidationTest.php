<?php

namespace Tests\Feature;

class ValidationTest extends RequestCase
{
    public function assertArrayKeysEquals(array $keys, array $array)
    {
        $errors = array_get($array, 'errors', []);
        $arraykeys = array_keys($errors);

        foreach ($keys as $key) {
            $this->assertContains($key, $arraykeys);
        }

        // Use the whole array to keep the value in the error message
        // Use json_encode so that the value is displayed (if not, you'll only have "Array ()" as value)
        $this->assertEquals('[]', json_encode(array_diff_key($errors, array_flip($keys))), 'Unexpected keys');
    }

    // Nothing sent, recaptcha needed, at least a name
    public function testEmpty()
    {
        $content = $this->ajaxPost('/', ['a' => 'b'], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'participants'], $content);
    }

    // Just names, need recaptcha and contact informations
    public function testContactInfo()
    {
        // At least 2 names!
        $content = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto'],
        ]], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'participants', 'participants.0.email', 'participants.0.phone'], $content);

        // Ok for names but duplicates this time but no contact infos
        $content = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto'],
            ['name' => 'toto'],
        ]], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'participants', 'participants.0.name', 'participants.0.email', 'participants.0.phone', 'participants.1.name', 'participants.1.email', 'participants.1.phone'], $content);

        // Ok for names but not enough participants and no contact infos
        $content = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto'],
            ['name' => 'tata'],
        ]], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'participants', 'participants.0.email', 'participants.0.phone', 'participants.1.email', 'participants.1.phone'], $content);

        // Ok for names but no contact infos
        $content = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto'],
            ['name' => 'tata'],
            ['name' => 'tutu'],
        ]], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'participants.0.email', 'participants.0.phone', 'participants.1.email', 'participants.1.phone', 'participants.2.email', 'participants.2.phone'], $content);

        // Ok for names but partial contact infos (sms)
        $content = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto', 'phone' => '0612345678'],
            ['name' => 'tata', 'phone' => ''],
            ['name' => 'tutu', 'phone' => ''],
        ]], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'participants.1.email', 'participants.1.phone', 'participants.2.email', 'participants.2.phone', 'contentSMS'], $content);

        // Ok for names but partial contact infos (mail)
        $content = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto', 'email' => ''],
            ['name' => 'tata', 'email' => 'test@test.com'],
            ['name' => 'tutu', 'email' => ''],
        ]], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'participants.0.email', 'participants.0.phone', 'participants.2.email', 'participants.2.phone', 'title', 'contentMail'], $content);

        // Ok for names but partial contact infos (both)
        $content = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto', 'phone' => '', 'email' => 'test@test.com'],
            ['name' => 'tata', 'phone' => '0612345678', 'email' => ''],
            ['name' => 'tutu', 'phone' => '', 'email' => ''],
        ]], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'participants.2.email', 'participants.2.phone', 'title', 'contentMail', 'contentSMS'], $content);
    }

    // Names and contact infos but no mail body nor sms body
    public function testContactBodiesMail()
    {
        $content = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto', 'email' => 'invalid@invalidformat'],
            ['name' => 'tata', 'email' => 'test@test.com'],
            ['name' => 'tutu', 'email' => 'tata@test.com'],
        ]], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'participants.0.email', 'title', 'contentMail'], $content);

        $content = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto', 'email' => 'test@est.com'],
            ['name' => 'tata', 'email' => 'test@test.com'],
            ['name' => 'tutu', 'email' => 'test@test.com'],
        ]], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'title', 'contentMail'], $content);
    }

    // Names and contact infos but no mail body nor sms body
    public function testContactBodiesSms()
    {
        $content = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto', 'phone' => '0612345678'],
            ['name' => 'tata', 'phone' => '55213249jgh'],
            ['name' => 'tutu', 'phone' => '0709876543'],
        ]], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'participants.1.phone', 'contentSMS'], $content);

        $content = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto', 'phone' => '0612345678'],
            ['name' => 'tata', 'phone' => '612345678'],
            ['name' => 'tutu', 'phone' => '787654987'],
        ]], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'contentSMS'], $content);
    }

    // Names and contact infos but no mail body nor sms body
    public function testContactBodiesBoth()
    {
        $content = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto', 'phone' => '0612345678', 'email' => 'test@test.com'],
            ['name' => 'tata', 'phone' => '0612345678', 'email' => ''],
            ['name' => 'tutu', 'phone' => '', 'email' => 'test@test.com'],
        ]], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'title', 'contentMail', 'contentSMS'], $content);
    }

    // Names and exclusionss
    public function testExclusionss()
    {
        $content = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto', 'exclusions' => ['87']],
            ['name' => 'tata'],
            ['name' => 'tutu'],
        ]], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'participants.0.exclusions.0', 'participants.0.phone', 'participants.1.phone', 'participants.2.phone', 'participants.0.email', 'participants.1.email', 'participants.2.email'], $content);

        $content = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto', 'exclusions' => ['0']],
            ['name' => 'tata'],
            ['name' => 'tutu'],
        ]], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'participants.0.exclusions.0', 'participants.0.phone', 'participants.1.phone', 'participants.2.phone', 'participants.0.email', 'participants.1.email', 'participants.2.email'], $content);

        $content = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto', 'exclusions' => ['1']],
            ['name' => 'tata'],
            ['name' => 'tutu'],
        ]], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'participants.0.phone', 'participants.1.phone', 'participants.2.phone', 'participants.0.email', 'participants.1.email', 'participants.2.email'], $content);
    }

    // Dear Santa
    public function testDearSanta()
    {
        // dearsanta at false, no problem
        $content = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto', 'phone' => '0612345678'],
            ['name' => 'tata', 'phone' => '0612345678'],
            ['name' => 'tutu', 'phone' => '0612345678'],
        ], 'dearsanta' => '0'], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'contentSMS'], $content);

        // dearsanta invalid value
        $content = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto', 'phone' => '0612345678'],
            ['name' => 'tata', 'phone' => '0612345678'],
            ['name' => 'tutu', 'phone' => '0612345678'],
        ], 'dearsanta' => '2'], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'contentSMS', 'dearsanta'], $content);

        // dearsanta enabled, need emails and limit
        $content = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto', 'phone' => '0612345678'],
            ['name' => 'tata', 'phone' => '0612345678'],
            ['name' => 'tutu', 'phone' => '0612345678'],
        ], 'dearsanta' => '1'], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'contentSMS', 'participants.0.email', 'participants.1.email', 'participants.2.email', 'dearsanta-expiration'], $content);

        // dearsanta enabled, invalid limit (too soon)
        $content = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto', 'phone' => '0612345678'],
            ['name' => 'tata', 'phone' => '0612345678'],
            ['name' => 'tutu', 'phone' => '0612345678'],
        ], 'dearsanta' => '1', 'dearsanta-expiration' => date('Y-m-d')], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'contentSMS', 'participants.0.email', 'participants.1.email', 'participants.2.email', 'dearsanta-expiration'], $content);

        // dearsanta enabled, limit valid
        $content = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto', 'phone' => '0612345678'],
            ['name' => 'tata', 'phone' => '0612345678'],
            ['name' => 'tutu', 'phone' => '0612345678'],
        ], 'dearsanta' => '1', 'dearsanta-expiration' => date('Y-m-d', strtotime('+3 day'))], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'contentSMS', 'participants.0.email', 'participants.1.email', 'participants.2.email'], $content);
    }

    // Sms limit
    public function testSmsLimit()
    {
        config(['sms.max' => 1]);
        $content = $this->ajaxPost('/', [
            'participants' => [
                ['name' => 'toto', 'phone' => '0612345678'],
                ['name' => 'tata', 'phone' => '0612345678'],
                ['name' => 'tutu', 'phone' => '0612345678'],
            ],
            'contentSMS' => '{TARGET}'.implode('', array_fill(0, 161, 'a')), // 2 sms long
        ], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'contentSMS'], $content);
    }

    public function testOrganizerEmailRequired()
    {
        $content = $this->ajaxPost('/', [
            'participants' => [
                ['name' => 'toto', 'phone' => '0612345678', 'email' => ''],
                ['name' => 'tata', 'phone' => '0612345678', 'email' => 'test@test.com'],
                ['name' => 'tutu', 'phone' => '0612345678', 'email' => 'test2@test.com'],
            ],
        ], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'title', 'contentMail', 'contentSMS', 'participants.0.email'], $content);
    }
}
