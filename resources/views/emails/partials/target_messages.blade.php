{{-- What the target wrote to their santa, and the others' gift ideas: within the target emails, or as the TargetMessages recap --}}
@if($targetMessages)
    <x-mail.h2>{{ __('emails.target_messages.messages') }}</x-mail.h2>
    <x-mail.p :top="4">{{ __('emails.target_messages.intro', ['name' => $targetName]) }}</x-mail.p>
    {{-- Not $message: the mail message of that name is a reserved view variable --}}
    @foreach ($targetMessages as $targetMessage)
        <x-mail.letter :date="$targetMessage['date']">{!! nl2br(e($targetMessage['content'])) !!}</x-mail.letter>
    @endforeach
@endif
@if($giftSuggestions)
    <x-mail.h2>{{ __('emails.target_messages.ideas') }}</x-mail.h2>
    <x-mail.p :top="4">{{ __('emails.target_messages.suggestions', ['name' => $targetName]) }}</x-mail.p>
    @foreach ($giftSuggestions as $giftSuggestion)
        {{-- Its author only when they signed it: "De : ?" otherwise --}}
        <x-mail.gift :label="__('emails.gift.from')" :name="($giftSuggestion['sender'] ?? null) ?: __('emails.gift.anonymous')" />
        <x-mail.letter plain :date="$giftSuggestion['date']">{!! nl2br(e($giftSuggestion['content'])) !!}</x-mail.letter>
    @endforeach
@endif
