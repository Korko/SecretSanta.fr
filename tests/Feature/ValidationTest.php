<?php

namespace Tests\Feature;

use Illuminate\Support\Arr;
use Validator;

class ValidationTest extends RequestCase
{
    public function testRuleSmsCount(): void
    {
        $validator = Validator::make([
            'content' => ''
        ], ['content' => 'max_sms_count:1']);
        $this->assertTrue($validator->passes());

        $validator = Validator::make([
            'content' => implode('', array_fill(0, 160, 'a'))
        ], ['content' => 'max_sms_count:1']);
        $this->assertTrue($validator->passes());

        $validator = Validator::make([
            'content' => implode('', array_fill(0, 161, 'a'))
        ], ['content' => 'max_sms_count:1']);
        $this->assertFalse($validator->passes());
    }

    public function testRuleRequiredWithAny(): void
    {
        $validator = Validator::make([
            'users' => []
        ], ['content' => 'required_with_any:users.*.name']);
        $this->assertTrue($validator->passes());

        $validator = Validator::make([
        ], ['content' => 'required_with_any:users.*.name']);
        $this->assertTrue($validator->passes());

        $validator = Validator::make([
            'users' => [['name' => 'Foo']],
        ], ['content' => 'required_with_any:users.*.name']);
        $this->assertFalse($validator->passes());
    }

    // Nothing sent, recaptcha needed, at least a name
    public function testEmpty(): void
    {
        $this->ajaxPost('/', ['a' => 'b'])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'g-recaptcha-response', 'participants', 'data-expiration', 'title', 'content-email'
            ]);
    }

    // Just names, need recaptcha and contact informations
    public function testContactInfo(): void
    {
        // At least 2 names!
        $response = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto'],
        ]]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'g-recaptcha-response', 'participants', 'participants.0.email', 'participants.0.phone', 'title', 'content-email', 'data-expiration'
            ]);

        // Ok for names but duplicates this time but no contact infos
        $response = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto'],
            ['name' => 'toto'],
        ]]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'g-recaptcha-response',
                'participants', 'participants.0.name', 'participants.0.email', 'participants.0.phone', 'participants.1.name', 'participants.1.email', 'participants.1.phone',
                'title', 'content-email', 'data-expiration'
            ]);

        // Ok for names but not enough participants and no contact infos
        $response = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto'],
            ['name' => 'tata'],
        ]]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'g-recaptcha-response',
                'participants', 'participants.0.email', 'participants.0.phone', 'participants.1.email', 'participants.1.phone',
                'title', 'content-email', 'data-expiration'
            ]);

        // Ok for names but no contact infos
        $response = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto'],
            ['name' => 'tata'],
            ['name' => 'tutu'],
        ]]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'g-recaptcha-response',
                'participants.0.email', 'participants.0.phone', 'participants.1.email', 'participants.1.phone', 'participants.2.email', 'participants.2.phone',
                'title', 'content-email', 'data-expiration'
            ]);

        // Ok for names but partial contact infos (sms)
        $response = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto', 'phone' => '0612345678'],
            ['name' => 'tata'],
            ['name' => 'tutu'],
        ]]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'g-recaptcha-response',
                'participants.0.email', 'participants.1.email', 'participants.1.phone', 'participants.2.email', 'participants.2.phone',
                'title', 'content-email', 'content-sms', 'data-expiration'
            ]);

        // Ok for names but partial contact infos (mail)
        $response = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto'],
            ['name' => 'tata', 'email' => 'test@test.com'],
            ['name' => 'tutu'],
        ]]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'g-recaptcha-response',
                'participants.0.email', 'participants.0.phone', 'participants.2.email', 'participants.2.phone',
                'title', 'content-email', 'data-expiration'
            ]);

        // Ok for names but partial contact infos (both)
        $response = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto', 'email' => 'test@test.com'],
            ['name' => 'tata', 'phone' => '0612345678'],
            ['name' => 'tutu'],
        ]]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'g-recaptcha-response',
                'participants.2.email', 'participants.2.phone',
                'title', 'content-email', 'content-sms', 'data-expiration'
            ]);
    }

    // Names and contact infos but no mail body nor sms body
    public function testContactBodiesMail(): void
    {
        $response = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto', 'email' => 'invalidformat'],
            ['name' => 'tata', 'email' => 'test@test.com'],
            ['name' => 'tutu', 'email' => 'tata@test.com'],
        ]]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'g-recaptcha-response',
                'participants.0.email',
                'title', 'content-email', 'data-expiration'
            ]);

        $response = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto', 'email' => 'test@est.com'],
            ['name' => 'tata', 'email' => 'test@test.com'],
            ['name' => 'tutu', 'email' => 'test@test.com'],
        ]]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'g-recaptcha-response', 'title', 'content-email', 'data-expiration'
            ]);
    }

    // Names and contact infos but no mail body nor sms body
    public function testContactBodiesSms(): void
    {
        $response = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto', 'phone' => '0612345678'],
            ['name' => 'tata', 'phone' => '55213249jgh'],
            ['name' => 'tutu', 'phone' => '0709876543'],
        ]]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'g-recaptcha-response', 'participants.0.email', 'participants.1.phone', 'content-sms', 'data-expiration'
            ]);

        $response = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto', 'phone' => '0612345678'],
            ['name' => 'tata', 'phone' => '612345678'],
            ['name' => 'tutu', 'phone' => '787654987'],
        ]]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'g-recaptcha-response', 'participants.0.email', 'content-sms', 'data-expiration'
            ]);
    }

    // Names and contact infos but no mail body nor sms body
    public function testContactBodiesBoth(): void
    {
        $response = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto', 'phone' => '0612345678', 'email' => 'test@test.com'],
            ['name' => 'tata', 'phone' => '0612345678'],
            ['name' => 'tutu', 'email' => 'test@test.com'],
        ]]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'g-recaptcha-response', 'title', 'content-email', 'content-sms', 'data-expiration'
            ]);
    }

    // Names and exclusionss
    public function testExclusionss(): void
    {
        $response = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto', 'exclusions' => ['87']],
            ['name' => 'tata'],
            ['name' => 'tutu'],
        ]]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'g-recaptcha-response',
                'participants.0.exclusions.0', 'participants.0.phone', 'participants.0.email',
                'participants.1.phone', 'participants.1.email',
                'participants.2.phone', 'participants.2.email',
            ]);

        $response = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto', 'exclusions' => ['1']],
            ['name' => 'tata'],
            ['name' => 'tutu'],
        ]]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'g-recaptcha-response',
                'participants.0.phone', 'participants.0.email',
                'participants.1.phone', 'participants.1.email',
                'participants.2.phone', 'participants.2.email',
                'title', 'content-email', 'data-expiration',
            ]);
    }

    // Dear Santa
    public function testDearSanta(): void
    {
        // dearsanta at false, no problem
        $response = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto', 'phone' => '0612345678'],
            ['name' => 'tata', 'phone' => '0612345678'],
            ['name' => 'tutu', 'phone' => '0612345678'],
        ], 'dearsanta' => '0']);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['g-recaptcha-response', 'content-sms', 'participants.0.email', 'data-expiration']);

        // dearsanta invalid value
        $response = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto', 'phone' => '0612345678'],
            ['name' => 'tata', 'phone' => '0612345678'],
            ['name' => 'tutu', 'phone' => '0612345678'],
        ], 'dearsanta' => '2']);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['g-recaptcha-response', 'content-sms', 'participants.0.email', 'dearsanta', 'data-expiration']);

        // dearsanta enabled, need emails
        $response = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto', 'phone' => '0612345678'],
            ['name' => 'tata', 'phone' => '0612345678'],
            ['name' => 'tutu', 'phone' => '0612345678'],
        ], 'dearsanta' => '1']);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['g-recaptcha-response', 'content-sms', 'participants.0.email', 'participants.1.email', 'participants.2.email', 'data-expiration']);
    }

    public function testDataLimit(): void
    {
        // invalid limit (too soon)
        $response = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto', 'phone' => '0612345678'],
            ['name' => 'tata', 'phone' => '0612345678'],
            ['name' => 'tutu', 'phone' => '0612345678'],
        ], 'data-expiration' => date('Y-m-d')]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['g-recaptcha-response', 'content-sms', 'participants.0.email', 'data-expiration']);

        $response = $this->ajaxPost('/', ['participants' => [
            ['name' => 'toto', 'phone' => '0612345678'],
            ['name' => 'tata', 'phone' => '0612345678'],
            ['name' => 'tutu', 'phone' => '0612345678'],
        ], 'data-expiration' => date('Y-m-d', strtotime('+3 day'))]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['g-recaptcha-response', 'content-sms', 'participants.0.email']);
    }

    // Sms limit
    public function testSmsLimit(): void
    {
        config(['sms.max' => 1]);
        $response = $this->ajaxPost('/', [
            'participants' => [
                ['name' => 'toto', 'phone' => '0612345678'],
                ['name' => 'tata', 'phone' => '0612345678'],
                ['name' => 'tutu', 'phone' => '0612345678'],
            ],
            'content-sms' => '{TARGET}'.implode('', array_fill(0, 161, 'a')), // 2 sms long
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['g-recaptcha-response', 'content-sms', 'participants.0.email', 'data-expiration']);
    }

    public function testOrganizerEmailRequired(): void
    {
        $response = $this->ajaxPost('/', [
            'participants' => [
                ['name' => 'toto', 'phone' => '0612345678'],
                ['name' => 'tata', 'phone' => '0612345678', 'email' => 'test@test.com'],
                ['name' => 'tutu', 'phone' => '0612345678', 'email' => 'test2@test.com'],
            ],
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['g-recaptcha-response', 'title', 'content-email', 'content-sms', 'participants.0.email', 'data-expiration']);
    }
}
