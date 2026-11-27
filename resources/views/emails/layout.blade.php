{{--
    Design « cadeaux » (maquettes Maizzle, thème cadeaux) : une colonne de 600 px, styles dans les balises.
    Rien d'indispensable en image : images bloquées, l'en-tête reste crème avec « SecretSanta.fr » en texte, les boutons restent cliquables.
    Polices servies par le site ; Gmail et Outlook prennent les polices de repli.
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="utf-8">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no, date=no, address=no, email=no, url=no">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>@yield('title')</title>
    <!--[if mso]><xml><o:OfficeDocumentSettings><o:AllowPNG/><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml><![endif]-->
    <style>
        @font-face { font-family: "Happy Kids"; font-weight: 400; src: url("{{ asset('fonts/happy-kids.woff2') }}") format("woff2"); }
        @font-face { font-family: Aileron; font-weight: 400; src: url("{{ asset('fonts/aileron-400.woff2') }}") format("woff2"); }
        @font-face { font-family: Aileron; font-weight: 700; src: url("{{ asset('fonts/aileron-700.woff2') }}") format("woff2"); }
        @media (max-width: 600px) {
            .sm-w-full { width: 100% !important; }
            .sm-pt-0 { padding-top: 0 !important; }
            .ss-card { padding: 28px 20px 32px !important; }
            .ss-h1 { font-size: 28px !important; line-height: 34px !important; }
            .ss-letter { padding-left: 18px !important; padding-right: 18px !important; }
            .tag-link { padding-right: 6px !important; font-size: 17px !important; }
            .ss-logo { width: 180px !important; height: auto !important; }
        }
    </style>
</head>
<body style="margin: 0; width: 100%; background-color: #fbf4ec; padding: 0; word-break: break-word;">
    @hasSection('preheader')
        <div style="display: none; max-height: 0; overflow: hidden; mso-hide: all;" aria-hidden="true">@yield('preheader')&#8199;&#65279;&#847;&#8199;&#65279;&#847;&#8199;&#65279;&#847;&#8199;&#65279;&#847;&#8199;&#65279;&#847;</div>
    @endif
    <div role="article" aria-roledescription="email" aria-label="@yield('title')" lang="{{ app()->getLocale() }}" style="background-color: #fbf4ec; font-family: Aileron, 'Helvetica Neue', Helvetica, Arial, sans-serif;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #fbf4ec; font-family: Aileron, 'Helvetica Neue', Helvetica, Arial, sans-serif;">
            <tr>
                <td align="center" class="sm-pt-0" style="padding: 24px 0 0;">
                    <table role="presentation" width="600" cellpadding="0" cellspacing="0" class="sm-w-full" style="width: 600px;">
                        <tr>
                            <td style="text-align: center; background-color: #fff8ef; padding-top: 24px;">
                                <a href="{{ config('app.url') }}" style="color: #3b2418; text-decoration: none;"><img class="ss-logo" src="{{ asset('images/emails/logo-lockup-cadeaux.png') }}" width="220" height="81" alt="{{ config('app.name') }}" style="vertical-align: middle; max-width: 100%; font-family: 'Happy Kids', 'Trebuchet MS', Arial, sans-serif; font-size: 28px; line-height: 40px; color: #3b2418;"></a>
                                <img src="{{ asset('images/emails/ruban-noeud.png') }}" width="600" alt="" style="vertical-align: middle; max-width: 100%; margin-top: 20px; display: block; height: auto; width: 100%;">
                            </td>
                        </tr>
                        <tr>
                            <td class="ss-card" style="background-color: #fffffe; padding: 40px 44px 44px;">
                                @hasSection('eyebrow')
                                    <p style="letter-spacing: .06em; color: #a62729; text-transform: uppercase; margin: 0 0 8px; font-family: Aileron, 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: 700; line-height: 18px;">@yield('eyebrow')</p>
                                @endif
                                <h1 class="ss-h1" style="color: #34454f; margin: 0; font-family: 'Happy Kids', 'Trebuchet MS', Arial, sans-serif; font-size: 32px; font-weight: 400; line-height: 40px;">@yield('title')</h1>

                                @yield('content')

                                <p style="color: #3f4e56; margin: 32px 0 0; font-family: Aileron, 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px;">{{ __('emails.layout.signature') }}</p>
                                <p style="color: #a62729; margin: 0; font-family: 'Happy Kids', 'Trebuchet MS', Arial, sans-serif; font-size: 22px; line-height: 28px;">SecretSanta<span style="font-family: Aileron, 'Helvetica Neue', Helvetica, Arial, sans-serif; font-weight: 700;">.</span>fr</p>
                            </td>
                        </tr>
                        <tr>
                            <td style="text-align: center; padding: 24px 24px 40px;">
                                <p style="color: #66757d; margin: 0; font-family: Aileron, 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; line-height: 19px;">{{ __('emails.layout.automatic') }}</p>
                                <p style="color: #66757d; margin: 0; font-family: Aileron, 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; line-height: 19px;"><a href="{{ config('app.url') }}" style="color: #66757d; text-decoration: underline;">{{ config('app.name') }}</a> &middot; {{ __('emails.layout.tagline') }}</p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
