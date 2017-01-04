<?php

require_once('RequestCase.php');

class RequestTest extends RequestCase
{
    public function testOnly_two()
    {
        Recaptcha::shouldReceive('verify')->once()->andReturn(true);

        Mail::shouldReceive('raw')
            ->once()
            ->with('#test mail toto => tata#', Mockery::on(function ($closure) {
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

        Sms::shouldReceive('message')
            ->once()
            ->with('0612345678', '#test sms "tata\' => &toto#')
            ->andReturn(true);

        $content = $this->ajaxPost('/', [
            'g-recaptcha-response' => 'mocked',
            'name'                 => ['toto', 'tata'],
            'email'                => ['test@test.com', ''],
            'phone'                => ['', '0612345678'],
            'exclusions'           => [],
            'title'                => 'test mail title',
            'contentMail'          => 'test mail {SANTA} => {TARGET}',
            'contentSMS'           => 'test sms "{SANTA}\' => &{TARGET}',
        ], 200);
        $this->assertEquals(['Envoyé avec succès !'], $content);
    }

    public function testSeveral()
    {
        Recaptcha::shouldReceive('verify')->once()->andReturn(true);

        Mail::shouldReceive('raw')
            ->once()
            ->with('#test mail toto => tata#', Mockery::on(function ($closure) {
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

        Mail::shouldReceive('raw')
            ->once()
            ->with('#test mail tutu => toto#', Mockery::on(function ($closure) {
                $message = Mockery::mock('Illuminate\Mailer\Message');
                $message->shouldReceive('to')
                    ->with('test2@test.com', 'tutu')
                    ->andReturn(Mockery::self());
                $message->shouldReceive('subject')
                    ->with('test mail title')
                    ->andReturn(Mockery::self());
                $closure($message);

                return true;
            }))
            ->andReturn(true);

        Sms::shouldReceive('message')
            ->once()
            ->with('0612345678', '#test sms "tata\' => &tutu#')
            ->andReturn(true);

        Sms::shouldReceive('message')
            ->once()
            ->with('0712345678', '#test sms "tutu\' => &toto#')
            ->andReturn(true);

        $content = $this->ajaxPost('/', [
            'g-recaptcha-response' => 'mocked',
            'name'                 => ['toto', 'tata', 'tutu'],
            'email'                => ['test@test.com', '', 'test2@test.com'],
            'phone'                => ['', '0612345678', '712345678'],
            'exclusions'           => [['2'], ['0'], ['1']],
            'title'                => 'test mail title',
            'contentMail'          => 'test mail {SANTA} => {TARGET}',
            'contentSMS'           => 'test sms "{SANTA}\' => &{TARGET}',
        ], 200);
        $this->assertEquals(['Envoyé avec succès !'], $content);
    }
}
