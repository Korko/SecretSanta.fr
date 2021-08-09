@if (config('app.bmc'))
<a class="bmc-button" target="_blank" href="https://www.buymeacoffee.com/{{ config('app.bmc') }}" rel="noopener noreferrer">
    <picture>
        <source srcset="/images/BMC Logo - Black.webp" type="image/webp">
        <source srcset="/images/BMC Logo - Black.png" type="image/png">
        <img src="/images/BMC Logo - Black.png" alt="Offrez moi un café">
    </picture><span style="margin-left:5px">Offrez moi un café</span>
</a>
@endif
