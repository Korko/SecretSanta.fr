@if($targetMessages)
{!! __('emails.target_messages.messages') !!}

{!! __('emails.target_messages.intro', ['name' => $targetName]) !!}

@foreach ($targetMessages as $targetMessage)
{!! $targetMessage['date'] !!}
> {!! $targetMessage['content'] !!}

@endforeach
@endif
@if($giftSuggestions)
{!! __('emails.target_messages.ideas') !!}

{!! __('emails.target_messages.suggestions', ['name' => $targetName]) !!}

@foreach ($giftSuggestions as $giftSuggestion)
{!! __('emails.gift.from') !!} : {!! ($giftSuggestion['sender'] ?? null) ?: __('emails.gift.anonymous') !!} - {!! $giftSuggestion['date'] !!}
> {!! $giftSuggestion['content'] !!}

@endforeach
@endif
