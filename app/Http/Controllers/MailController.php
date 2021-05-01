<?php

namespace App\Http\Controllers;

use App\Models\Mail as MailModel;
use Illuminate\Http\Request;

class MailController extends Controller
{
    public function updateStatus(MailModel $mail, Request $request)
    {
        $mail->markAsReceived();

        return '';
    }
}
