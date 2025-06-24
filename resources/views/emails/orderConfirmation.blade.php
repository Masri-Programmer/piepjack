<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auftragsbestätigung</title>
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
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td bgcolor="#1e1e1e" align="center" style="padding: 20px 0;">
                <table class="content-table" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #252526; border-radius: 8px; box-shadow: 0 8px 16px rgba(0,0,0,0.2);">
                    <tr>
                        <td align="center" style="padding: 40px 0 20px 0;">
                            <img src="https://api.piepjack-clothing.de/images/logo_new_gray_bg_black.jpeg" alt="Piepjack Clothing" width="180" style="display: block; width: 180px; filter: brightness(90%);">
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding: 0 30px;">
                            <h1 class="header-text" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 28px; font-weight: 500; color: #f5f5f5; margin: 0;">Vielen Dank für deinen Einkauf!</h1>
                            <p style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 16px; color: #9e9e9e; margin-top: 10px;">Bestellung #{{ $order['order_number'] }}</p>
                            <p style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px; color: #dddee0; padding: 10px 0 20px 0;">
                                Hallo {{ $customer['first_name'] }}, wir haben deine Bestellung erhalten und werden sie in Kürze bearbeiten.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding: 0 30px 30px 30px;">
                            <table border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center" style="border-radius: 5px;" bgcolor="#dddee0">
                                        <a href="{{ env('VITE_FRONTEND_URL') }}/track-order/{{ $order['order_number'] }}" target="_blank" style="font-size: 14px; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #171717; text-decoration: none; text-transform: uppercase; font-weight: bold; border-radius: 5px; padding: 14px 25px; border: 1px solid #dddee0; display: inline-block;">Bestellung ansehen</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 0 30px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td class="section-header" style="padding-bottom: 20px; border-bottom: 1px solid #444444; color: #f5f5f5; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 20px; font-weight: bold;">
                                        Bestellübersicht
                                    </td>
                                </tr>
                                @foreach ($products as $index => $product)
                                <tr>
                                    <td style="padding: 20px 0; border-bottom: 1px solid #444444;">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td class="product-image" width="100" valign="top">
                                                    <img src="{{ $items[$index]['image'] }}" alt="{{ $items[$index]['name'] }}" width="100" height="100" style="display: block; border-radius: 6px; object-fit: cover;">
                                                </td>
                                                <td valign="top" style="padding-left: 20px; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #dddee0;">
                                                    <div style="font-size: 16px; font-weight: bold;">{{ $items[$index]['name'] }}</div>
                                                    @if (!empty($items[$index]['options']))
                                                    <div style="font-size: 14px; color: #9e9e9e; padding-top: 5px;">
                                                        @foreach ($items[$index]['options'] as $option)
                                                        {{ $option['name'] }}: {{ $option['value'] }}<br>
                                                        @endforeach
                                                    </div>
                                                    @endif
                                                    <div style="font-size: 14px; color: #9e9e9e; padding-top: 5px;">Menge: {{ $product['quantity'] }}</div>
                                                </td>
                                                <td width="80" valign="top" align="right" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 16px; font-weight: bold; color: #f5f5f5;">
                                                    €{{ number_format($product['price_per_item'] * $product['quantity'], 2) }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                @endforeach
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 20px 30px;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td width="75%" align="left" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 16px; font-weight: bold; color: #f5f5f5; padding-top: 10px;">
                                        Gesamtsumme
                                    </td>
                                    <td width="25%" align="right" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 16px; font-weight: bold; color: #f5f5f5; padding-top: 10px;">
                                        €{{ number_format($product['price_per_item'] * $product['quantity'], 2) }} </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 20px 30px; border-top: 1px solid #444444;">
                            <h3 style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 20px; font-weight: bold; color: #f5f5f5; margin:0 0 10px 0;">Lieferadresse</h3>
                            <p style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px; color: #dddee0; margin: 0;">
                                {{ $customer['first_name'] }} {{ $customer['last_name'] }}<br>
                                {{ $customer['address_line_one'] }}<br>
                                <!-- @if($customer['address_line_two']) {{ $customer['address_line_two'] }}<br> @endif -->
                                <br>
                            </p>
                        </td>
                    </tr>
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