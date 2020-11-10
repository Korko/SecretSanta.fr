<?php

namespace Tests\Feature;

use Validator;

class ValidationTest extends RequestCase
{
    public function testRuleRequiredWithAny(): void
    {
        // True, no users.*.name
        $validator = Validator::make([
            'users' => [],
        ], ['content' => 'required_with_any:users.*.name']);
        $this->assertTrue($validator->passes());

        // True, still no users.*.name
        $validator = Validator::make([
        ], ['content' => 'required_with_any:users.*.name']);
        $this->assertTrue($validator->passes());

        // False, users.*.name defined but no content
        $validator = Validator::make([
            'users' => [['name' => 'Foo']],
        ], ['content' => 'required_with_any:users.*.name']);
        $this->assertFalse($validator->passes());

        // False, users.list.*.name.txt defined but no content
        $validator = Validator::make([
            'users' => [
                'list' => [
                    ['name' => ['txt' => 'Foo']]
                ]
            ],
        ], ['content' => 'required_with_any:users.list.*.name.txt']);
        $this->assertFalse($validator->passes());

        // True, users.list.*.name.txt and content defined
        $validator = Validator::make([
            'users' => [
                'list' => [
                    ['name' => ['txt' => 'Foo']]
                ]
            ],
            'content' => 'foobar'
        ], ['content' => 'required_with_any:users.list.*.name.txt']);
        $this->assertTrue($validator->passes());
    }

    // Nothing sent, need at least a name
    public function testEmpty(): void
    {
        $this->ajaxPost('/', ['a' => 'b'])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'participants', 'data-expiration', 'title', 'content-email',
            ]);
    }

    // Just names, need contact informations
    public function testContactInfo(): void
    {
        // At least 2 names!
        $this->ajaxPost('/', ['participants' => [
                ['name' => 'toto'],
            ]])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'participants', 'participants.0.email', 'title', 'content-email', 'data-expiration',
            ]);

        // Ok for names but duplicates this time but no contact infos
        $this->ajaxPost('/', ['participants' => [
                ['name' => 'toto'],
                ['name' => 'toto'],
            ]])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'participants',
                'participants.0.name', 'participants.0.email',
                'participants.1.name', 'participants.1.email',
                'title', 'content-email', 'data-expiration',
            ]);

        // Ok for names but not enough participants and no contact infos
        $this->ajaxPost('/', ['participants' => [
                ['name' => 'toto'],
                ['name' => 'tata'],
            ]])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'participants', 'participants.0.email', 'participants.1.email',
                'title', 'content-email', 'data-expiration',
            ]);

        // Ok for names but no contact infos
        $this->ajaxPost('/', ['participants' => [
                ['name' => 'toto'],
                ['name' => 'tata'],
                ['name' => 'tutu'],
            ]])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'participants.0.email', 'participants.1.email', 'participants.2.email',
                'title', 'content-email', 'data-expiration',
            ]);

        // Ok for names but partial contact infos (mail)
        $this->ajaxPost('/', ['participants' => [
                ['name' => 'toto'],
                ['name' => 'tata', 'email' => 'test@test.com'],
                ['name' => 'tutu'],
            ]])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'participants.0.email', 'participants.2.email',
                'title', 'content-email', 'data-expiration',
            ]);
    }

    // Names and contact infos but no mail body
    public function testContactBodiesMail(): void
    {
        $this->ajaxPost('/', ['participants' => [
                ['name' => 'toto', 'email' => 'invalidformat'],
                ['name' => 'tata', 'email' => 'test@test.com'],
                ['name' => 'tutu', 'email' => 'tata@test.com'],
            ]])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'participants.0.email',
                'title', 'content-email', 'data-expiration',
            ]);

        $this->ajaxPost('/', ['participants' => [
                ['name' => 'toto', 'email' => 'test@est.com'],
                ['name' => 'tata', 'email' => 'test@test.com'],
                ['name' => 'tutu', 'email' => 'test@test.com'],
            ]])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'title', 'content-email', 'data-expiration',
            ]);
    }

    // Names and exclusionss
    public function testExclusionss(): void
    {
        $this->ajaxPost('/', ['participants' => [
                ['name' => 'toto', 'exclusions' => ['87']],
                ['name' => 'tata'],
                ['name' => 'tutu'],
            ]])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'participants.0.exclusions.0', 'participants.0.email',
                'participants.1.email',
                'participants.2.email',
            ]);

        $this->ajaxPost('/', ['participants' => [
                ['name' => 'toto', 'exclusions' => ['1']],
                ['name' => 'tata'],
                ['name' => 'tutu'],
            ]])
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'participants.0.email',
                'participants.1.email',
                'participants.2.email',
                'title', 'content-email', 'data-expiration',
            ]);
    }

    public function testDataLimit(): void
    {
        // invalid limit (too soon)
        $this->ajaxPost('/', ['participants' => [
                ['name' => 'toto', 'email' => 'test@test.com'],
                ['name' => 'tata', 'email' => 'test@test.com'],
                ['name' => 'tutu', 'email' => 'test@test.com'],
            ], 'data-expiration' => date('Y-m-d')])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'content-email', 'data-expiration']);

        $this->ajaxPost('/', ['participants' => [
                ['name' => 'toto', 'email' => 'test@test.com'],
                ['name' => 'tata', 'email' => 'test@test.com'],
                ['name' => 'tutu', 'email' => 'test@test.com'],
            ], 'data-expiration' => date('Y-m-d', strtotime('+3 day'))])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'content-email']);
    }
}
