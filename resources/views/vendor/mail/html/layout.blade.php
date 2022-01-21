<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link href="{{ mix('/css/webfonts.css') }}" rel="stylesheet">
</head>
<body>
    <style>
        html { background: #ffffff; color: #000; font-family:'Kreon', Georgia, serif; }
        body { background: #ffffff; color: #000; font-family:'Kreon', Georgia, serif; padding:0 !important; margin:0 !important; display:block !important; -webkit-text-size-adjust:none; background:#ffffff }
        a { color:#e85853; text-decoration:none }
        p { padding:0 !important; margin:0 !important }
        img { -ms-interpolation-mode: bicubic; /* Allow smoother rendering of resized image in Internet Explorer */ }
        .mcnPreviewText { display: none !important; }
        blockquote { font-family:'Caveat', cursive; }

        /* Mobile styles */
        @media only screen and (max-device-width: 480px), only screen and (max-width: 480px) {
            .mobile-shell { width: 100% !important; min-width: 100% !important; }

            .text-header,
            .m-center { text-align: center !important; }
            .holder { padding: 0 10px !important; }
            .ribbon { font-size: 18px !important; }
            .center { margin: 0 auto !important; }
            .td { width: 100% !important; min-width: 100% !important; }

            .text-header .link-white { text-shadow: 0 3px 4px rgba(0,0,0,09) !important; }

            .m-br-15 { height: 15px !important; }
            .bg { height: auto !important; }
            .img-title img { width: 30px !important; height: auto !important; }

            .m-td,
            .m-hide { display: none !important; width: 0 !important; height: 0 !important; font-size: 0 !important; line-height: 0 !important; min-height: 0 !important; }
            .m-block { display: block !important; }

            .p30-15 { padding: 30px 15px !important; }
            .p15-15 { padding: 15px 15px !important; }
            .p30-0 { padding: 30px 0px !important; }
            .p0-0-30 { padding: 0px 0px 30px 0px !important; }
            .p0-15-30 { padding: 0px 15px 30px 15px !important; }
            .p0-15 { padding: 0px 15px 0px 15px !important; }
            .mp0 { padding: 0px !important; }
            .mp20-0-0 { padding: 20px 0px 0px 0px !important }
            .mp30 { padding-bottom: 30px !important; }
            .container { padding: 20px 0px !important; }
            .outer { padding: 0px !important }
            .h0 { height: 0px !important; }
            .brr0 { border-radius: 0px !important; }

            .fluid-img img { width: 100% !important; max-width: 100% !important; height: auto !important; }

            .column,
            .column-top,
            .column-dir,
            .column-empty,
            .column-empty2,
            .column-empty3,
            .column-bottom,
            .column-dir-top,
            .column-dir-bottom { float: left !important; width: 100% !important; display: block !important; }

            .column-empty { padding-bottom: 10px !important; }
            .column-empty2 { padding-bottom: 25px !important; }
            .column-empty3 { padding-bottom: 45px !important; }

            .content-spacing { width: 15px !important; }
            .content-spacing2 { width: 25px !important; }
        }
    </style>

    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="background: #ffffff;" align="center">
        <tr>
            <td align="center" valign="top" style="background: rgba(33,45,57,.8) url({{ mix('/images/email_bg1.png') }} ); background-repeat: repeat-x; background-position: 0 0;">
                <table width="110" border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td class="td" style="width:86px; min-width:86px; height:86px; min-height:86px; font-size:0pt; line-height:0pt; padding:0; margin:0; font-weight:normal; background:#000;border-radius: 0 0 100% 100%;padding:12px;">
                            <img src="{{ mix('/images/logo.png') }}" width="86" height="86" border="0" alt="" />
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td align="center" valign="top" style="background: #ffffff;">
                <table width="650" border="0" cellspacing="0" cellpadding="0" class="mobile-shell">
                    <tr>
                        <td class="td" style="width:650px; min-width:650px; padding:0; margin:0; font-weight:normal;">
                            {{ $header ?? '' }}

                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td>
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td class="p0-15-30" style="padding: 0px 40px 45px 40px;">
                                                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                        <tr>
                                                            <td style="text-align:center; font-style:italic; padding-bottom:15px;">
                                                                {{ Illuminate\Mail\Markdown::parse($slot) }}

                                                                {{ $subcopy ?? '' }}
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            {{ $footer ?? '' }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
