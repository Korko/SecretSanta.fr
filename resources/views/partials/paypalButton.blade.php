@if (config('paypal.me'))
<a href="https://www.paypal.me/{{ config('paypal.me') }}">
    <img src="/images/paypal.gif">
</a>
@endif
