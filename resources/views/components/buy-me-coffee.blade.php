@pushOnce('styles')
    @vite('resources/sass/buy-me-coffee.scss')
@endPushOnce

<a class="bmc-button" target="_blank" href="https://www.buymeacoffee.com/{{ $login }}" rel="noopener noreferrer">
    @lang('bmc.button')
</a>
