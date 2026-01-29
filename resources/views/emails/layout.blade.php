<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Piepjack Clothing')</title>
    <style>
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
            background-color: #f4f4f5;
            /* Light Gray Background */
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #171717;
        }

        /* Utility for vibrant color */
        .accent-color {
            color: #4f46e5;
            /* Vibrant Indigo */
        }
    </style>
</head>

<body style="margin: 0 !important; padding: 0 !important; background-color: #f4f4f5;">
    <div style="display: none; max-height: 0; overflow: hidden;">
        @yield('preheader')
    </div>

    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding: 40px 0;">
                {{-- Main "Paper" Container --}}
                <table class="content-table" border="0" cellpadding="0" cellspacing="0" width="600"
                    style="background-color: #ffffff; box-shadow: 0 4px 6px rgba(0,0,0,0.05); overflow: hidden;">

                    {{-- Logo Section --}}
                    <tr>
                        <td align="center" style="padding: 40px 0 20px 0;">
                            {{-- Assuming you have a dark version of the logo for white paper, otherwise use the
                            existing one --}}
                            <img src="https://testing.piepjack-clothing.de/images/logo_new_gray_bg_black.jpeg"
                                alt="Piepjack Clothing" width="120" style="display: block; width: 120px;">
                        </td>
                    </tr>

                    {{-- Main Title --}}
                    <tr>
                        <td align="center" style="padding: 0 30px;">
                            <h1
                                style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 24px; font-weight: 400; color: #111111; margin: 0; letter-spacing: 0.5px;">
                                @yield('title')
                            </h1>
                        </td>
                    </tr>

                    @yield('content')

                    {{-- Footer --}}
                    <tr>
                        <td align="center"
                            style="padding: 30px 40px; background-color: #f9fafb; border-top: 1px solid #eeeeee;">
                            <p
                                style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; margin: 0; font-size: 12px; color: #6b7280; line-height: 1.5;">
                                Vielen Dank, dass du Teil von Piepjack bist. Diese E-Mail gilt sowohl als
                                Bestellbestätigung als auch als Rechnung.
                            </p>
                            <p
                                style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; margin: 10px 0 0 0; font-size: 12px; color: #9ca3af;">
                                &copy; {{ date('Y') }} Piepjack Clothing.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>