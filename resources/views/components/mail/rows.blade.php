{{-- Label / value lines, separated by a thin line: [label => value], a null value hides its line --}}
@props(['rows', 'panel' => true])
@php($rows = array_filter($rows, fn ($value) => $value !== null && $value !== ''))
<div style="{{ $panel ? 'background-color: #f6ece3; border-radius: 16px; padding: 20px 24px; margin-top: 16px;' : 'margin-top: 12px;' }}">
    <table role="presentation" cellpadding="0" cellspacing="0" style="width: 100%;">
@foreach ($rows as $label => $value)
@php($border = $loop->first ? '' : ' border-color: #e4c8b3; border-style: solid; border-width: 1px 0 0;')
        <tr>
            <td style="padding: 8px 12px 8px 0; vertical-align: top; color: #66757d; font-family: Aileron, 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 20px;{{ $border }}">{{ $label }}</td>
            <td style="text-align: right; vertical-align: top; color: #34454f; padding: 8px 0; font-family: Aileron, 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 14px; font-weight: 700; line-height: 20px;{{ $border }}">{{ $value }}</td>
        </tr>
@endforeach
    </table>
</div>
