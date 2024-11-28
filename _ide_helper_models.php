<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{use Eloquent;use Illuminate\Database\Eloquent\Builder;
/**
 * App\Models\DearSanta
 *
 * @property int $id
 * @property int $sender_id
 * @property array $mail_body
 * @property-read mixed $draw
 * @property-read mixed $hash
 * @property-read Mail|null $mail
 * @property-read Participant $sender
 * @method static Builder|DearSanta findByHashOrFail($hash)
 * @method static Builder|DearSanta newModelQuery()
 * @method static Builder|DearSanta newQuery()
 * @method static Builder|DearSanta query()
 * @method static Builder|DearSanta whereId($value)
 * @method static Builder|DearSanta whereMailBody($value)
 * @method static Builder|DearSanta whereSenderId($value)
 */
	class DearSanta extends Eloquent {}
}

namespace App\Models{use App\Collections\ParticipantsCollection;use Database\Factories\DrawFactory;use Eloquent;use Illuminate\Database\Eloquent\Builder;use Illuminate\Notifications\DatabaseNotification;use Illuminate\Notifications\DatabaseNotificationCollection;use Illuminate\Support\Carbon;
/**
 * App\Models\Draw
 *
 * @property int $id
 * @property array $mail_title
 * @property array $mail_body
 * @property Carbon $expires_at
 * @property bool $next_solvable
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read mixed $can_redraw
 * @property-read mixed $deleted_at
 * @property-read mixed $expired
 * @property-read mixed $hash
 * @property-read mixed $metric_id
 * @property-read mixed $organizer
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read ParticipantsCollection|Participant[] $participants
 * @property-read int|null $participants_count
 * @method static DrawFactory factory(...$parameters)
 * @method static Builder|Draw findByHashOrFail($hash)
 * @method static Builder|Draw newModelQuery()
 * @method static Builder|Draw newQuery()
 * @method static Builder|Draw query()
 * @method static Builder|Draw whereCreatedAt($value)
 * @method static Builder|Draw whereExpiresAt($value)
 * @method static Builder|Draw whereId($value)
 * @method static Builder|Draw whereMailBody($value)
 * @method static Builder|Draw whereMailTitle($value)
 * @method static Builder|Draw whereNextSolvable($value)
 * @method static Builder|Draw whereUpdatedAt($value)
 */
	class Draw extends Eloquent {}
}

namespace App\Models{use Eloquent;use Illuminate\Database\Eloquent\Builder;
/**
 * App\Models\Exclusion
 *
 * @property int $participant_id
 * @property int $exclusion_id
 * @method static Builder|Exclusion newModelQuery()
 * @method static Builder|Exclusion newQuery()
 * @method static Builder|Exclusion query()
 * @method static Builder|Exclusion whereExclusionId($value)
 * @method static Builder|Exclusion whereParticipantId($value)
 */
	class Exclusion extends Eloquent {}
}

namespace App\Models{use Database\Factories\MailFactory;use Eloquent;use Illuminate\Database\Eloquent\Builder;use Illuminate\Support\Carbon;
/**
 * App\Models\Mail
 *
 * @property int $id
 * @property string $notification
 * @property string $mailable_type
 * @property int $mailable_id
 * @property string $delivery_status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|Eloquent $mailable
 * @method static MailFactory factory(...$parameters)
 * @method static Builder|Mail newModelQuery()
 * @method static Builder|Mail newQuery()
 * @method static Builder|Mail query()
 * @method static Builder|Mail whereCreatedAt($value)
 * @method static Builder|Mail whereDeliveryStatus($value)
 * @method static Builder|Mail whereId($value)
 * @method static Builder|Mail whereMailableId($value)
 * @method static Builder|Mail whereMailableType($value)
 * @method static Builder|Mail whereNotification($value)
 * @method static Builder|Mail whereUpdatedAt($value)
 */
	class Mail extends Eloquent {}
}

namespace App\Models{use Eloquent;use Illuminate\Database\Eloquent\Builder;
/**
 * App\Models\Model
 *
 * @method static Builder|Model newModelQuery()
 * @method static Builder|Model newQuery()
 * @method static Builder|Model query()
 */
	class Model extends Eloquent {}
}

namespace App\Models{use App\Collections\ParticipantsCollection;use Database\Factories\ParticipantFactory;use Eloquent;use Illuminate\Database\Eloquent\Builder;use Illuminate\Database\Eloquent\Collection;use Illuminate\Notifications\DatabaseNotification;use Illuminate\Notifications\DatabaseNotificationCollection;use Illuminate\Support\Carbon;
/**
 * App\Models\Participant
 *
 * @property int $id
 * @property int $draw_id
 * @property array $name
 * @property array $email
 * @property int|null $target_id
 * @property int $redraw
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|DearSanta[] $dearSantas
 * @property-read int|null $dear_santas_count
 * @property-read Draw $draw
 * @property-read ParticipantsCollection|Participant[] $exclusions
 * @property-read int|null $exclusions_count
 * @property-read mixed $exclusions_names
 * @property-read mixed $hash
 * @property-read mixed $metric_id
 * @property-read mixed $sender
 * @property-read Mail|null $mail
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read Participant|null $santa
 * @property-read Participant|null $target
 * @method static ParticipantsCollection|static[] all($columns = ['*'])
 * @method static ParticipantFactory factory(...$parameters)
 * @method static Builder|Participant findByHashOrFail($hash)
 * @method static ParticipantsCollection|static[] get($columns = ['*'])
 * @method static Builder|Participant newModelQuery()
 * @method static Builder|Participant newQuery()
 * @method static Builder|Participant query()
 * @method static Builder|Participant whereCreatedAt($value)
 * @method static Builder|Participant whereDrawId($value)
 * @method static Builder|Participant whereEmail($value)
 * @method static Builder|Participant whereId($value)
 * @method static Builder|Participant whereName($value)
 * @method static Builder|Participant whereRedraw($value)
 * @method static Builder|Participant whereTargetId($value)
 * @method static Builder|Participant whereUpdatedAt($value)
 */
	class Participant extends Eloquent {}
}

