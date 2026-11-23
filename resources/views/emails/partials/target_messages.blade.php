@if($targetMessages)
    <p style="padding-bottom:10px !important">{{ __('emails.target_messages.intro', ['name' => $targetName]) }}</p>

    {{-- Not $message: the layout embeds its images through the mail message of that name --}}
    @foreach ($targetMessages as $targetMessage)
        <p style="padding-bottom:5px !important"><em>{{ $targetMessage['date'] }}</em></p>
        <blockquote>{!! nl2br(e($targetMessage['content'])) !!}</blockquote>
    @endforeach
@endif
@if($giftSuggestions)

    <p style="padding-bottom:10px !important">{{ __('emails.target_messages.suggestions', ['name' => $targetName]) }}</p>

    @foreach ($giftSuggestions as $giftSuggestion)
        <p style="padding-bottom:5px !important"><em>{{ $giftSuggestion['sender'] ? __('emails.target_messages.suggestion_from', ['date' => $giftSuggestion['date'], 'sender' => $giftSuggestion['sender']]) : $giftSuggestion['date'] }}</em></p>
        <blockquote>{!! nl2br(e($giftSuggestion['content'])) !!}</blockquote>
    @endforeach

    <p style="padding-bottom:10px !important"><small>{{ __('emails.target_messages.suggestions_secret', ['name' => $targetName]) }}</small></p>
@endif
