<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use Illuminate\Support\Facades\URL;

class ReportController extends Controller
{
    public function view(Participant $participant)
    {
        return response()->inertia('ReportForm', [
            'participant' => $participant->hash,
            'routes' => [
                'report' => URL::signedRoute('report.report', ['participant' => $participant]),
            ],
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
            'message' => trans('message.report_sent'),
        ]);
    }
}
