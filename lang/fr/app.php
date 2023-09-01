<?php

use App\Enums\AppMode;
use App\Enums\EmailAddressStatus;
use App\Enums\PendingDrawStatus;
use App\Enums\QuestionToSanta;

return [
    'nojs' => 'Ce site internet a besoin que vous activiez le JavaScript dans votre navigateur pour fonctionner.',
    'santa' => [
        // Cannot use BackedEnum as key so cast to get the actual value
        (QuestionToSanta::PRESENTATION)->value => 'Votre père/mère noël secrêt(e) souhaiterait en savoir plus sur vous, pourriez-vous vous présenter ?',
        (QuestionToSanta::IDEAS)->value => 'Votre père/mère noël secrêt(e) souhaiterait que vous lui donniez quelques idées cadeau, les choses qui vous plaise, etc.',
    ],
    'modes' => [
        // Cannot use BackedEnum as key so cast to get the actual value
        (AppMode::FREE)->value => 'Publique',
        (AppMode::OPEN)->value => 'Mécène',
        (AppMode::UNLIMITED)->value => 'Professionnelle',
    ],
    'pendingDrawStatus' => [
        (PendingDrawStatus::CREATED)->value => '',
        (PendingDrawStatus::READY)->value => '',
        (PendingDrawStatus::DRAWING)->value => '',
        (PendingDrawStatus::STARTED)->value => '',
        (PendingDrawStatus::ERROR)->value => '',
    ],
    'emailAddressStatus' => [
        (EmailAddressStatus::CREATED)->value => 'Créée',
        (EmailAddressStatus::CONFIRMED)->value => 'Confirmée',
        (EmailAddressStatus::ERROR)->value => 'Erreur',
    ]
];
