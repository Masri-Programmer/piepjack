<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Piepjack Clothing')</title>
    <style>
        /* Basic Reset & Font Import */
        body,
        table,
        td,
        a {
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table,
        td {
            mso-table-lspace: 0pt;
            mso-table-rspace: 0pt;
        }

        img {
            -ms-interpolation-mode: bicubic;
            border: 0;
            height: auto;
            line-height: 100%;
            outline: none;
            text-decoration: none;
        }

        body {
            height: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            width: 100% !important;
            background-color: #1e1e1e;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        }

        /* Mobile Responsive Styles */
        @media screen and (max-width: 600px) {
            .content-table {
                width: 100% !important;
                padding: 15px !important;
            }

            .header-text,
            .section-header {
                font-size: 24px !important;
            }

            .product-image img {
                width: 80px !important;
                height: 80px !important;
            }
        }
    </style>
</head>

<body style="margin: 0 !important; padding: 0 !important; background-color: #1e1e1e;">
    <div style="display: none; max-height: 0; overflow: hidden;">
        @yield('preheader')
    </div>

    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td bgcolor="#1e1e1e" align="center" style="padding: 20px 0;">
                <table class="content-table" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #252526; border-radius: 8px; box-shadow: 0 8px 16px rgba(0,0,0,0.2);">
                    <tr>
                        <td align="center" style="padding: 40px 0 20px 0;">
                            <img src="https://testing.piepjack-clothing.de/images/logo_new_gray_bg_black.jpeg" alt="Piepjack Clothing" width="180" style="display: block; width: 180px; filter: brightness(90%);">
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding: 0 30px;">
                            <h1 class="header-text" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 28px; font-weight: 500; color: #f5f5f5; margin: 0;">
                                @yield('title')
                            </h1>

                            <p style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 16px; color: #9e9e9e; margin-top: 10px;">
                                @yield('subtitle')
                            </p>

                            <p style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px; color: #dddee0; padding: 10px 0 20px 0;">
                                @yield('subtitle_2')
                            </p>
                        </td>
                    </tr>
                    @yield('content')

                    <tr>
                        <td align="center" style="padding: 30px 30px;">
                            <p style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; margin: 0; font-size: 12px; color: #9e9e9e;">
                                Bei Fragen, antworte einfach auf diese E-Mail oder kontaktiere uns unter <a href="mailto:info@piepjack-clothing.de" style="color: #dddee0; text-decoration: none;">info@piepjack-clothing.de</a>.
                            </p>
                            <p style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; margin: 10px 0 0 0; font-size: 12px; color: #9e9e9e;">
                                &copy; {{ date('Y') }} Piepjack Clothing. Alle Rechte vorbehalten.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>