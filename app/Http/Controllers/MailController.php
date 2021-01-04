<?php

namespace App\Http\Controllers;

use App\Models\Mail as MailModel;
use App\Traits\UpdatesMailDelivery;
use Illuminate\Http\Request;

class MailController extends Controller
{
    use UpdatesMailDelivery;

    public function updateStatus(MailModel $mail, Request $request)
    {
        $this->updateDelivery($mail, MailModel::RECEIVED, $request->route('version'));

        return '';
    }
}
