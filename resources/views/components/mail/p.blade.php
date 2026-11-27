@props(['muted' => false, 'small' => false, 'top' => 16])
<p style="color: {{ $muted ? '#66757d' : '#3f4e56' }}; margin: {{ $top }}px 0 0; font-family: Aileron, 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: {{ $small ? 13 : 16 }}px; line-height: {{ $small ? 20 : 26 }}px;">{{ $slot }}</p>
