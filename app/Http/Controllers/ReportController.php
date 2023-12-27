<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    public function view(Participant $participant): Response
    {
        return '';
    }

    public function handle(Participant $participant, Request $request): JsonResponse
    {


        return response()->json([
            'message' => trans('message.report_sent'),
        ]);
    }
}
