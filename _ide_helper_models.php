<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * App\Models\DearSanta
 *
 * @property int $id
 * @property int $sender_id
 * @property array $mail_body
 * @property-read mixed $draw
 * @property-read mixed $hash
 * @property-read \App\Models\Mail|null $mail
 * @property-read \App\Models\Participant $sender
 * @method static \Illuminate\Database\Eloquent\Builder|DearSanta findByHashOrFail($hash)
 * @method static \Illuminate\Database\Eloquent\Builder|DearSanta newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DearSanta newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DearSanta query()
 * @method static \Illuminate\Database\Eloquent\Builder|DearSanta whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DearSanta whereMailBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DearSanta whereSenderId($value)
 */
	class DearSanta extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Draw
 *
 * @property int $id
 * @property array $mail_title
 * @property array $mail_body
 * @property \Illuminate\Support\Carbon $expires_at
 * @property bool $next_solvable
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $can_redraw
 * @property-read mixed $deleted_at
 * @property-read mixed $expired
 * @property-read mixed $hash
 * @property-read mixed $metric_id
 * @property-read mixed $organizer
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \App\Collections\ParticipantsCollection|\App\Models\Participant[] $participants
 * @property-read int|null $participants_count
 * @method static \Database\Factories\DrawFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Draw findByHashOrFail($hash)
 * @method static \Illuminate\Database\Eloquent\Builder|Draw newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Draw newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Draw query()
 * @method static \Illuminate\Database\Eloquent\Builder|Draw whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Draw whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Draw whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Draw whereMailBody($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Draw whereMailTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Draw whereNextSolvable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Draw whereUpdatedAt($value)
 */
	class Draw extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Exclusion
 *
 * @property int $participant_id
 * @property int $exclusion_id
 * @method static \Illuminate\Database\Eloquent\Builder|Exclusion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Exclusion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Exclusion query()
 * @method static \Illuminate\Database\Eloquent\Builder|Exclusion whereExclusionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Exclusion whereParticipantId($value)
 */
	class Exclusion extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Mail
 *
 * @property int $id
 * @property string $notification
 * @property string $mailable_type
 * @property int $mailable_id
 * @property string $delivery_status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $mailable
 * @method static \Database\Factories\MailFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Mail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Mail query()
 * @method static \Illuminate\Database\Eloquent\Builder|Mail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mail whereDeliveryStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mail whereMailableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mail whereMailableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mail whereNotification($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Mail whereUpdatedAt($value)
 */
	class Mail extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Model
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Model newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Model newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Model query()
 */
	class Model extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Participant
 *
 * @property int $id
 * @property int $draw_id
 * @property array $name
 * @property array $email
 * @property int|null $target_id
 * @property int $redraw
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\DearSanta[] $dearSantas
 * @property-read int|null $dear_santas_count
 * @property-read \App\Models\Draw $draw
 * @property-read \App\Collections\ParticipantsCollection|Participant[] $exclusions
 * @property-read int|null $exclusions_count
 * @property-read mixed $exclusions_names
 * @property-read mixed $hash
 * @property-read mixed $metric_id
 * @property-read mixed $sender
 * @property-read \App\Models\Mail|null $mail
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read Participant|null $santa
 * @property-read Participant|null $target
 * @method static \App\Collections\ParticipantsCollection|static[] all($columns = ['*'])
 * @method static \Database\Factories\ParticipantFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Participant findByHashOrFail($hash)
 * @method static \App\Collections\ParticipantsCollection|static[] get($columns = ['*'])
 * @method static \Illuminate\Database\Eloquent\Builder|Participant newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Participant newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Participant query()
 * @method static \Illuminate\Database\Eloquent\Builder|Participant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Participant whereDrawId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Participant whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Participant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Participant whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Participant whereRedraw($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Participant whereTargetId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Participant whereUpdatedAt($value)
 */
	class Participant extends \Eloquent {}
}

