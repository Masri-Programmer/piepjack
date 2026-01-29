@extends('emails.layout')

@section('title', 'Danke für deine Bestellung')

@section('preheader')
    Hallo {{ $user->first_name ?? '' }}, wir haben deine Bestellung #{{ $order->order_number }} erhalten.
@endsection

@section('content')
    {{-- Order Details Grid --}}
    <tr>
        <td style="padding: 40px 40px 20px 40px;">
            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                <tr>
                    <td colspan="2"
                        style="padding-bottom: 15px; border-bottom: 1px solid #eeeeee; font-size: 16px; font-weight: bold; color: #111111;">
                        Bestelldetails <span class="accent-color" style="float: right;">#{{ $order->order_number }}</span>
                    </td>
                </tr>
                <tr>
                    {{-- Left Column --}}
                    <td valign="top" width="50%" style="padding-top: 15px; padding-right: 10px;">
                        <p style="margin: 0 0 5px 0; font-size: 13px; color: #111111; font-weight: bold;">Bestellung:</p>
                        <p style="margin: 0 0 15px 0; font-size: 13px; color: #666666;">#{{ $order->order_number }}</p>

                        <p style="margin: 0 0 5px 0; font-size: 13px; color: #111111; font-weight: bold;">Kunde:</p>
                        <p style="margin: 0 0 15px 0; font-size: 13px; color: #666666;">{{ $user->first_name }}
                            {{ $user->last_name }}
                        </p>
                    </td>
                    {{-- Right Column --}}
                    <td valign="top" width="50%" style="padding-top: 15px; padding-left: 10px;">
                        <p style="margin: 0 0 5px 0; font-size: 13px; color: #111111; font-weight: bold;">Bestelldatum:</p>
                        <p style="margin: 0 0 15px 0; font-size: 13px; color: #666666;">
                            {{ $order->created_at->format('d. F Y') }}
                        </p>

                        <p style="margin: 0 0 5px 0; font-size: 13px; color: #111111; font-weight: bold;">E-Mail:</p>
                        <p style="margin: 0 0 15px 0; font-size: 13px; color: #666666;">{{ $user->email }}</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- Product Table Headers --}}
    <tr>
        <td style="padding: 0 40px;">
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr style="background-color: #f3f4f6;">
                    <td style="padding: 10px; font-size: 12px; font-weight: bold; color: #111111; width: 50%;">Produkt</td>
                    <td
                        style="padding: 10px; font-size: 12px; font-weight: bold; color: #111111; text-align: center; width: 15%;">
                        Menge</td>
                    <td
                        style="padding: 10px; font-size: 12px; font-weight: bold; color: #111111; text-align: right; width: 15%;">
                        Preis</td>
                    <td
                        style="padding: 10px; font-size: 12px; font-weight: bold; color: #111111; text-align: right; width: 20%;">
                        Total</td>
                </tr>

                {{-- Loop Products --}}
                @foreach ($products as $index => $product)
                    {{-- Pass index to stripe rows if needed, or keep clean white --}}
                    @include('emails.products', ['product' => $product, 'last' => $loop->last])
                @endforeach

                {{-- Totals Section --}}
                <tr>
                    <td colspan="4" style="padding-top: 15px; border-top: 1px solid #eeeeee;"></td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td style="padding: 5px 10px; font-size: 13px; color: #666666; text-align: right;">Zwischensumme</td>
                    <td style="padding: 5px 10px; font-size: 13px; color: #111111; text-align: right; font-weight: bold;">
                        €{{ number_format($order->total_price, 2) }}
                    </td>
                </tr>
                {{-- Assuming tax is part of total or needs calculation, simplistic view here --}}
                <tr>
                    <td colspan="2"></td>
                    <td style="padding: 5px 10px; font-size: 13px; color: #666666; text-align: right;">Versand</td>
                    <td style="padding: 5px 10px; font-size: 13px; color: #111111; text-align: right;">Standard</td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td
                        style="padding: 10px; font-size: 15px; color: #111111; text-align: right; font-weight: bold; border-top: 1px solid #eeeeee; margin-top: 5px;">
                        Gesamtbetrag</td>
                    <td class="accent-color"
                        style="padding: 10px; font-size: 15px; text-align: right; font-weight: bold; border-top: 1px solid #eeeeee; margin-top: 5px;">
                        €{{ number_format($order->total_price, 2) }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- Bottom Info (Payment & Shipping) --}}
    <tr>
        <td style="padding: 30px 40px 40px 40px;">
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td
                        style="padding-bottom: 10px; border-bottom: 1px solid #eeeeee; font-size: 14px; font-weight: bold; color: #111111;">
                        Zahlung & Versand
                    </td>
                </tr>
                <tr>
                    <td style="padding-top: 15px; font-size: 13px; line-height: 1.6; color: #666666;">
                        <strong>Lieferadresse:</strong><br>
                        {{ $address->street_address }}<br>
                        {{ $address->postal_code }} {{ $address->city }}<br>
                        {{ $address->country_name }}
                    </td>
                </tr>
                <tr>
                    <td style="padding-top: 10px;">
                        <a href="{{ env('APP_URL') }}/track-order/{{ $order->order_number }}"
                            style="font-size: 13px; color: #4f46e5; text-decoration: none; font-weight: bold;">
                            Status ansehen &rarr;
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    <tr>
        <td style="padding: 20px 40px; border-top: 1px solid #eeeeee;">
            <p style="font-size: 14px; font-weight: bold; color: #111111; margin-bottom: 10px;">Widerrufsbelehrung</p>
            <p style="font-size: 12px; color: #666666; line-height: 1.5;">
                <strong>Widerrufsrecht</strong><br>
                Sie haben das Recht, binnen vierzehn Tagen ohne Angabe von Gründen diesen Vertrag zu widerrufen. Die
                Widerrufsfrist beträgt vierzehn Tage ab dem Tag, an dem Sie oder ein von Ihnen benannter Dritter, der nicht
                der Beförderer ist, die Waren in Besitz genommen haben bzw. hat.<br><br>
                Um Ihr Widerrufsrecht auszuüben, müssen Sie uns mittels einer eindeutigen Erklärung (z. B. ein mit der Post
                versandter Brief oder E-Mail) über Ihren Entschluss, diesen Vertrag zu widerrufen, informieren.<br><br>
                <strong>Folgen des Widerrufs</strong><br>
                Wenn Sie diesen Vertrag widerrufen, haben wir Ihnen alle Zahlungen, die wir von Ihnen erhalten haben,
                einschließlich der Lieferkosten (mit Ausnahme der zusätzlichen Kosten, die sich daraus ergeben, dass Sie
                eine andere Art der Lieferung als die von uns angebotene, günstigste Standardlieferung gewählt haben),
                unverzüglich und spätestens binnen vierzehn Tagen ab dem Tag zurückzuzahlen, an dem die Mitteilung über
                Ihren Widerruf dieses Vertrags bei uns eingegangen ist.<br><br>
                Wir können die Rückzahlung verweigern, bis wir die Waren wieder zurückerhalten haben oder bis Sie den
                Nachweis erbracht haben, dass Sie die Waren zurückgesandt haben.<br><br>
                Der Kunde trägt die unmittelbaren Kosten der Rücksendung der Waren.
            </p>
        </td>
    </tr>
@endsection