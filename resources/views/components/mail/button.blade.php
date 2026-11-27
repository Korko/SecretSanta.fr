{{-- The green tag with cut corners (or kraft): its ends are images, its body is text on a cell colour, so it stays clickable without images --}}
@props(['href', 'variant' => 'sapin'])
@php
    [$bg, $shadow, $color, $font] = match ($variant) {
        'sapin' => ['#1a7a43', '#0f5a30', '#fffffe', "font-family: 'Happy Kids', 'Trebuchet MS', Arial, sans-serif; font-size: 20px;"],
        default => ['#e4c8b3', '#c9a88f', '#34454f', "font-family: Aileron, 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 16px; font-weight: 700;"],
    };
@endphp
<table role="presentation" cellpadding="0" cellspacing="0" style="margin-top: 24px;">
    <tr>
        <td style="background-color: {{ $bg }}; width: 24px; vertical-align: top;"><img src="{{ asset('images/emails/pointe-'.$variant.'.png') }}" width="24" height="56" alt="" style="vertical-align: middle; max-width: 100%; display: block;"></td>
        <td class="tag-body" style="height: 52px; background-color: {{ $bg }}; border-color: {{ $shadow }}; border-style: solid; border-width: 0 0 4px;"><a href="{{ $href }}" class="tag-link" style="white-space: nowrap; padding-left: 8px; padding-right: 12px; {{ $font }} line-height: 52px; text-decoration: none; display: block; color: {{ $color }};">{{ $slot }}</a></td>
        <td style="background-color: {{ $bg }}; width: 12px; vertical-align: top;"><img src="{{ asset('images/emails/bout-'.$variant.'.png') }}" width="12" height="56" alt="" style="vertical-align: middle; max-width: 100%; display: block;"></td>
    </tr>
</table>
