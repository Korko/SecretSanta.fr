{{-- A text written by someone: dashed letter (message, target's messages), plain panel (gift idea) --}}
@props(['date' => null, 'plain' => false])
<div class="ss-letter" style="background-color: {{ $plain ? '#f6ece3' : '#fbf4ec' }}; {{ $plain ? '' : 'border: 2px dashed #9c7a60;' }} border-radius: 16px; margin-top: {{ $plain ? 0 : 20 }}px; padding: 20px 24px;">
@if ($date)
    <p style="color: #66757d; margin: 0 0 8px; font-family: Aileron, 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 13px; line-height: 18px;">{{ $date }}</p>
@endif
    <p style="color: #34454f; margin: 0; font-family: Aileron, 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 17px; line-height: 28px;">{{ $slot }}</p>
</div>
