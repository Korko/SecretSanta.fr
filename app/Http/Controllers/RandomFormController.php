<?php

namespace Korko\SecretSanta\Http\Controllers;

use Illuminate\Http\Request;
use Korko\SecretSanta\Http\Requests\RandomFormRequest;
use Korko\SecretSanta\Libs\Resolver;
use Mail;
use Sms;
use Statsd;

class RandomFormController extends Controller
{
    public function view()
    {
        return view('randomForm');
    }

    public function handle(RandomFormRequest $request)
    {
        $participants = $this->getParticipants($request);

        $hat = Resolver::resolve($participants);

        $this->sendMessages($request, $participants, $hat);

        $message = 'Envoyé avec succès !';

        return $request->ajax() ? [$message] : redirect('/')->with('message', $message);
    }

    protected function getParticipants(Request $request)
    {
        $names = $request->input('name');
        $emails = $request->input('email');
        $phones = $request->input('phone');
        $exclusions = $request->input('exclusions', []);

        $participants = [];
        for ($i = 0; $i < count($names); $i++) {
            $participants[$i] = [
                'name'       => $names[$i],
                'email'      => $emails[$i],
                'phone'      => $phones[$i],
                'exclusions' => (isset($exclusions[$i])) ? array_map('intval', $exclusions[$i]) : [],
            ];
        }

        Statsd::gauge('draws', '+1');
        Statsd::gauge('participants', '+'.count($participants));

        return $participants;
    }

    protected function sendMessages(Request $request, array $participants, array $hat)
    {
        foreach ($hat as $santaIdx => $targetName) {
            $santa = $participants[$santaIdx];

            $this->sendMessage($request, $santa, $targetName);
        }
    }

    protected function sendMessage(Request $request, array $santa, $targetName)
    {
        if (!empty($santa['email'])) {
            Statsd::gauge('email', '+1');
            $this->sendMail($santa, $targetName, $request->input('title'), $request->input('contentMail'));
        }

        if (!empty($santa['phone'])) {
            Statsd::gauge('phone', '+1');
            $this->sendSms($santa, $targetName, $request->input('contentSMS'));
        }
    }

    protected function sendMail($santa, $targetName, $title, $content)
    {
        $contentMail = str_replace(['{SANTA}', '{TARGET}'], [$santa['name'], $targetName], $content);
        $contentMail .= PHP_EOL.trans('form.mail.post');
        Mail::raw($contentMail, function ($m) use ($santa, $title) {
            $m->to($santa['email'], $santa['name'])->subject($title);
        });
    }

    protected function sendSms($santa, $targetName, $content)
    {
        $contentSms = str_replace(['{SANTA}', '{TARGET}'], [$santa['name'], $targetName], $content);
        $contentSms .= PHP_EOL.trans('form.sms.post');
        Sms::message($santa['phone'], $contentSms);
    }
}
