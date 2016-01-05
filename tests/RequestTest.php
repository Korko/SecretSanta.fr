<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Symfony\Component\HttpFoundation\JsonResponse;

class RequestTest extends TestCase
{
    // Ignore CSRF Validation
    use WithoutMiddleware;

    protected function ajaxPost($url, $postArgs, $code = 200)
    {
        $server = ['HTTP_X-Requested-With' => 'XMLHttpRequest'];
        $response = $this->call('POST', $url, $postArgs, [], [], $server);
        $this->assertEquals($code, $response->status());
        return json_decode($response->content(), true);
    }

    protected function assertArrayKeysEquals(array $keys, array $array)
    {
        $arraykeys = array_keys($array);

        foreach($keys as $key) {
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

        $content = $this->ajaxPost('/', ['name' => ['toto', 'tata'], 'phone' => ['0612345678', '0612345678']], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'contentSMS'], $content);
    }

    // Names and contact infos but no mail body nor sms body
    public function testContactBodiesBoth()
    {
        $content = $this->ajaxPost('/', ['name' => ['toto', 'tata', 'tutu'], 'email' => ['', 'test@test.com', 'test@test.com'], 'phone' => ['0612345678', '0612345678', ''], 'partners' => ['', '', '']], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'title', 'contentMail', 'contentSMS'], $content);
    }

    // Names and partners
    public function testPartners()
    {
        $content = $this->ajaxPost('/', ['name' => ['toto', 'tata'], 'partner' => [87]], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'partner.0', 'phone.0', 'phone.1', 'email.0', 'email.1'], $content);

        $content = $this->ajaxPost('/', ['name' => ['toto', 'tata'], 'partner' => [0]], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'partner.0', 'phone.0', 'phone.1', 'email.0', 'email.1'], $content);

        $content = $this->ajaxPost('/', ['name' => ['toto', 'tata'], 'partner' => [1]], 422);
        $this->assertArrayKeysEquals(['g-recaptcha-response', 'phone.0', 'phone.1', 'email.0', 'email.1'], $content);
    }

    public function testOk()
    {
        Recaptcha::shouldReceive('verify')->once()->andReturn(true);

        Mail::shouldReceive('raw')
            ->once()
            ->with('test mail toto => tata', Mockery::on(function($closure) {
                $message = Mockery::mock('Illuminate\Mailer\Message');
                $message->shouldReceive('to')
                    ->with('test@test.com', 'toto')
                    ->andReturn(Mockery::self());
                $message->shouldReceive('subject')
                    ->with('test mail title')
                    ->andReturn(Mockery::self());
                $closure($message);
                return true;
            }))
            ->andReturn(true);
        SmsWave::shouldReceive('send')
            ->once()
            ->with('0612345678', 'test sms tata => toto')
            ->andReturn(true);

        $content = $this->ajaxPost('/', [
            'g-recaptcha-response' => 'mocked',
            'name' => ['toto', 'tata'],
            'email' => ['test@test.com', ''],
            'phone' => ['', '0612345678'],
            'partners' => ['', ''],
            'title' => 'test mail title', 'contentMail' => 'test mail {SANTA} => {TARGET}',
            'contentSMS' => 'test sms {SANTA} => {TARGET}'
        ], 200);
        $this->assertEquals(['Envoyé avec succès !'], $content);
    }
}