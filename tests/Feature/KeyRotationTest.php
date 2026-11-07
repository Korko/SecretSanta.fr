<?php

declare(strict_types=1);

use App\Facades\DrawCrypt;
use App\Models\Participant;
use Illuminate\Support\Facades\URL;

test('rotating APP_KEY keeps live draws working with APP_PREVIOUS_KEYS', function (): void {
    $draw = createServiceDraw(generateParticipants(3));
    $participant = $draw->participants->first();
    $draw->refuseEmail($participant->email);
    $panel = URL::signedRoute('organizerPanel.fetch', ['draw' => $draw]);
    $iv = DrawCrypt::getIV();

    config(['app.previous_keys' => [config('app.key')], 'app.key' => 'base64:'.base64_encode(random_bytes(32))]);
    app()->forgetInstance('draw-encrypter');
    DrawCrypt::clearResolvedInstance('draw-encrypter');
    DrawCrypt::setIV($iv);

    expect(Participant::query()->find($participant->id)->email)->toBe($participant->email)
        ->and($draw->fresh()->refusesEmail($participant->email))->toBeTrue();
    ajaxGet($panel)->assertSuccessful();

    // Cancelling the report clears the fingerprint made with the previous key
    $draw->fresh()->refuseEmail($participant->email, false);
    expect($draw->fresh()->refusesEmail($participant->email))->toBeFalse();
});
