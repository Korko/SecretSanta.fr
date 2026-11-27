{{-- "Pour : …" / "De : …": a gift tag, an information and never a link --}}
@props(['label', 'name', 'top' => 20])
<table role="presentation" cellpadding="0" cellspacing="0" style="margin-top: {{ $top }}px;">
    <tr>
        <td style="width: 40px; vertical-align: top;"><img src="{{ asset('images/emails/pointe-cadeau.png') }}" width="40" height="48" alt="" style="vertical-align: middle; max-width: 100%; display: block;"></td>
        <td style="white-space: nowrap; color: #34454f; background-color: #fffffe; border-color: #d8b69c; border-style: solid; border-width: 2px 0; height: 44px; padding-left: 4px; padding-right: 8px; font-family: 'Happy Kids', 'Trebuchet MS', Arial, sans-serif; font-size: 21px; line-height: 44px;">{{ $label }}&nbsp;:&nbsp;<span style="color: #a62729;">{{ $name }}</span></td>
        <td style="width: 10px; vertical-align: top;"><img src="{{ asset('images/emails/bout-cadeau.png') }}" width="10" height="48" alt="" style="vertical-align: middle; max-width: 100%; display: block;"></td>
    </tr>
</table>
