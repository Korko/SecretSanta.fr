{{-- Coloured dots, red / green / pink: each item is HTML already escaped by the caller --}}
@props(['items'])
<table role="presentation" cellpadding="0" cellspacing="0" style="margin-top: 8px; width: 100%;">
@foreach ($items as $item)
    <tr>
        <td style="vertical-align: top; width: 20px; padding-top: 9px;"><div style="height: 8px; width: 8px; border-radius: 9999px; background-color: {{ ['#d03133', '#1a7a43', '#f9afaa'][$loop->index % 3] }};"></div></td>
        <td style="vertical-align: top; color: #3f4e56; padding-top: 4px; padding-bottom: 8px; font-family: Aileron, 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 26px;">{!! $item !!}</td>
    </tr>
@endforeach
</table>
