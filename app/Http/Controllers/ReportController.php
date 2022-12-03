<?php

namespace App\Http\Controllers;

use App\Http\Requests\DearSantaRequest;
use App\Models\DearSanta;
use App\Models\Mail as MailModel;
use App\Models\Participant;
use App\Notifications\DearSanta as DearSantaNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\URL;
use Lang;
use Str;

class ReportController extends Controller
{
    public function view(Participant $participant)
    {
        return response()->inertia('ReportForm', [
            'participant' => $participant->hash,
            'routes' => [
                'report' => URL::signedRoute('report.report', ['participant' => $participant]),
            ]
        ]);
    }

    public function handle(Participant $participant, \Illuminate\Http\Request $request)
    {
        //if($request->)

        // Send report to admin
        // => Link to remove draw and ban organizer ip
        // => Link to write to organizer

        // Withdraw participant

        return response()->json([
            'message' => trans('message.report_sent')
        ]);
    }
}
