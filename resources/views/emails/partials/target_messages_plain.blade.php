@if($targetMessages)
    {!! __('emails.target_messages.intro', ['name' => $targetName]) !!}
@foreach ($targetMessages as $targetMessage)

    {!! $targetMessage['date'] !!}
    > {!! $targetMessage['content'] !!}
@endforeach
@endif
@if($giftSuggestions)

    {!! __('emails.target_messages.suggestions', ['name' => $targetName]) !!}
@foreach ($giftSuggestions as $giftSuggestion)

    {!! $giftSuggestion['sender'] ? __('emails.target_messages.suggestion_from', ['date' => $giftSuggestion['date'], 'sender' => $giftSuggestion['sender']]) : $giftSuggestion['date'] !!}
    > {!! $giftSuggestion['content'] !!}
@endforeach

    {!! __('emails.target_messages.suggestions_secret', ['name' => $targetName]) !!}
@endif
