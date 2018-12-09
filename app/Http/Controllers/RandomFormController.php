<?php

namespace App\Http\Controllers;

use App\Draw;
use App\Exceptions\SolverException;
use App\Http\Requests\RandomFormRequest;
use App\Mail\TargetDrawn;
use App\Participant;
use App\Services\AsymmetricalPublicEncrypter as AsymmetricalEncrypter;
use App\Services\SymmetricalEncrypter;
use Facades\App\Services\HatSolver as Solver;
use Facades\App\Services\SmsTools as SmsTools;
use Hashids;
use Illuminate\Http\Request;
use Mail;
use Metrics;
use Sms;

class RandomFormController extends Controller
{
    private $symKey;
    private $encrypter;

    private $asymKeys;
    private $aEncrypter;

    public function __construct()
    {
        $this->symKey = SymmetricalEncrypter::generateKey();
        $this->encrypter = new SymmetricalEncrypter($this->symKey);

        $this->asymKeys = AsymmetricalEncrypter::generateKeys();
        $this->aEncrypter = new AsymmetricalEncrypter($this->asymKeys['public']);
    }

    public function handle(RandomFormRequest $request)
    {
        try {
            $this->draw($request);

            $message = trans('message.sent');

            return $request->ajax() ?
                response()->json(['message' => $message]) :
                redirect('/')->with('message', $message);
        } catch (SolverException $e) {
            $error = trans('error.solution');

            return $request->ajax() ?
                response()->json(['error' => $error], 500) :
                redirect('/')->with('error', $error);
        }
    }

    protected function draw(Request $request)
    {
        $participants = $this->getParticipants($request);

        $hat = Solver::one($participants, array_column($participants, 'exclusions'));

        Metrics::increment('draws');
        Metrics::increment('participants', count($participants));

        if ($request->input('contentMail')) {
            $this->sendMails($request, $participants, $hat);
        }

        if ($request->input('contentSMS')) {
            $this->sendSMSs($request, $participants, $hat);
        }
    }

    protected function getParticipants(Request $request)
    {
        $names = $request->input('name');
        $emails = $request->input('email');
        $phones = $request->input('phone');
        $exclusions = $request->input('exclusions', []);

        $participants = [];
        for ($i = 0; $i < count($names); $i++) {
            if ($phones[$i] && substr($phones[$i], 0, 1) === '0') {
                $phones[$i] = substr($phones[$i], 1);
            }
            $participants[$i] = [
                'name'       => $names[$i],
                'email'      => $emails[$i],
                'phone'      => $phones[$i] ? '+33'.$phones[$i] : $phones[$i],
                'exclusions' => (isset($exclusions[$i])) ? array_map('intval', $exclusions[$i]) : [],
            ];
        }

        return $participants;
    }

    protected function sendMails(Request $request, array $participants, array $hat)
    {
        $title = $request->input('title');
        $body = $request->input('contentMail');
        $dearSantaExpiration = $request->input('dearsanta-expiration');

        $organizer = $participants[0];

        // Use symmetrical encryption, we don't need to hide it from each other
        $this->draw = new Draw();
        $this->draw->expiration = $dearSantaExpiration;
        $this->draw->title = $this->encrypter->encrypt($title);
        $this->draw->body = $this->encrypter->encrypt($body);
        $this->draw->organizer_name = $this->encrypter->encrypt($organizer['name']);
        $this->draw->organizer_email = $this->encrypter->encrypt($organizer['email']);
        $this->draw->save();

        $personalizations = [];

        foreach ($hat as $santaIdx => $targetIdx) {
            $santa = $participants[$santaIdx];
            $target = $participants[$targetIdx];

            if (!empty($santa['email'])) {
                // Santa of that santa
                $superSanta = $participants[array_search($santaIdx, $hat)];

                $dearSantaLink = null;
                if ($request->input('dearsanta')) {
                    $dearSantaLink = $this->getDearSantaLink($superSanta, $dearSantaExpiration);
                }

                $personalizations[] = [
                    'to' => [
                        'email' => $santa['email'],
                        'name'  => $santa['name'],
                    ],
                    'substitutions' => [
                        '{SANTA}'  => $santa['name'],
                        '{TARGET}' => $target['name'],
                        ':link'    => $dearSantaLink,
                    ],
                ];
            }
        }

        if (!empty($personalizations)) {
            Metrics::increment('email', count($personalizations));

            Mail::to($organizer['email'])
                ->send(new TargetDrawn($title, $body, $personalizations));
        }
    }

    protected function sendSMSs(Request $request, array $participants, array $hat)
    {
        foreach ($hat as $santaIdx => $targetIdx) {
            $santa = $participants[$santaIdx];
            $target = $participants[$targetIdx];

            if (!empty($santa['phone'])) {
                Metrics::increment('phone');
                Metrics::increment('sms', SmsTools::count($request->input('contentSMS')));

                $contentSms = str_replace(['{SANTA}', '{TARGET}'], [$santa['name'], $target['name']], $request->input('contentSMS'));
                Sms::message($santa['phone'], $contentSms);
            }
        }
    }

    protected function getDearSantaLink(array $santa, $expirationDate)
    {
        $participant = $this->addParticipant($santa, $expirationDate);

        return route('dearsanta', ['santa' => Hashids::encode($participant->id)]).'#'.bin2hex($this->asymKeys['private']);
    }

    private function addParticipant(array $santa, $expirationDate)
    {
        // Use Asymmetrical encrypter, only the reciptient should be able to decrypt!
        $participant = new Participant();
        $participant->draw_id = $this->draw->id;
        $participant->santa_name = $this->aEncrypter->encrypt($santa['name']);
        $participant->santa_email = $this->aEncrypter->encrypt($santa['email']);
        $participant->challenge = $this->aEncrypter->encrypt(Participant::CHALLENGE, false); // tested by JS so no serializing
        $participant->public_key = $this->asymKeys['public'];

        $participant->save();

        return $participant;
    }
}
