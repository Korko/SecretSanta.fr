<?php

use App\Models\Mail as MailModel;

it('marks a bounced email as error', function () {
    assertReturnPath('bounceReturnPath', MailModel::ERROR);
});

it('marks a read confirmed email as received', function () {
    assertReturnPath('confirmReturnPath', MailModel::RECEIVED);
});