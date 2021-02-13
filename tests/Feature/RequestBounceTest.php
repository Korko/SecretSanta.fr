<?php

namespace Tests\Feature;

use App\Mail\TargetDrawn;
use App\Models\Draw;
use App\Models\Mail as MailModel;
use App\Models\Participant;
use App\Jobs\ParseBounces;
use App\Services\EmailClient;
use Event;
use Mail;
use Webklex\PHPIMAP\Message as EmailMessage;

class RequestBounceTest extends RequestCase
{
    use \Illuminate\Foundation\Testing\DatabaseMigrations;
    use \Illuminate\Foundation\Testing\DatabaseTransactions;

    protected function assertReturnPath($prop, $status)
    {
        Event::fake();
        Mail::fake();
        Mail::assertNothingSent();

        $participants = $this->createAjaxDraw(3);

        // Fake emails as bounces
        $emailClient = $this->mock(EmailClient::class, function ($mock) use ($participants, $prop) {
            // Get all the return path defined by the app
            $links = [];
            Mail::assertSent(function (TargetDrawn $mail) use (&$links, $prop) {
                $links[] = $mail->$prop;

                return true;
            });
            $this->assertEquals(count($participants), count($links));

            // Fake the list of "unseen mails"
            $messages = [];
            foreach($links as $link) {
                $messages[] = $this->mock(EmailMessage::class, function ($mock) use ($link) {
                    $mock
                        ->shouldReceive('getTo')
                        ->once()
                        ->andReturn([
                            (object) ['mailbox' => $link]
                        ]);

                    $mock->shouldReceive('move')->once();
                });
            }

            $mock
                ->shouldReceive('getUnseenMails')
                ->once()
                ->andReturn($messages);
        });

        // Ensure all participants are marked as bounced
        // Database is cleared, only 1 Draw available
        $draw = Draw::findOrFail(1);
        foreach ($draw->participants as $participant) {
            $this->assertEquals(MailModel::CREATED, $participant->mail->delivery_status);
        }

        // Parse bounces and mark them as bounced
        $job = new ParseBounces();
        $job->handle($emailClient);

        // Ensure all participants are marked as bounced
        foreach ($draw->participants as $participant) {
            // Get the last delivery_status from DB via the "fresh" method
            $this->assertEquals($status, $participant->mail->fresh()->delivery_status);
        }
    }

    public function testBounce(): void
    {
        $this->assertReturnPath('bounceReturnPath', MailModel::ERROR);
    }

    public function testConfirm(): void
    {
        $this->assertReturnPath('confirmReturnPath', MailModel::RECEIVED);
    }
}
