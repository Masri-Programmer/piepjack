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
                        €{{ number_format($subtotal ?? 0, 2, ',', '.') }}
                    </td>
                </tr>
                @if(($discount ?? 0) > 0)
                <tr>
                    <td colspan="2"></td>
                    <td style="padding: 5px 10px; font-size: 13px; color: #10b981; text-align: right;">Rabatt</td>
                    <td style="padding: 5px 10px; font-size: 13px; color: #10b981; text-align: right; font-weight: bold;">
                        -€{{ number_format($discount, 2, ',', '.') }}
                    </td>
                </tr>
                @endif
                <tr>
                    <td colspan="2"></td>
                    <td style="padding: 5px 10px; font-size: 13px; color: #666666; text-align: right;">Versand</td>
                    <td style="padding: 5px 10px; font-size: 13px; color: #111111; text-align: right;">
                        {{ ($shipping ?? 0) > 0 ? '€' . number_format($shipping, 2, ',', '.') : 'Gratis' }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td
                        style="padding: 10px; font-size: 15px; color: #111111; text-align: right; font-weight: bold; border-top: 1px solid #eeeeee; margin-top: 5px;">
                        Gesamtbetrag</td>
                    <td class="accent-color"
                        style="padding: 10px; font-size: 15px; text-align: right; font-weight: bold; border-top: 1px solid #eeeeee; margin-top: 5px;">
                        €{{ number_format($order->total_price ?? $total ?? 0, 2, ',', '.') }}
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
                        Versandinformationen
                    </td>
                </tr>
                <tr>
                    <td style="padding-top: 15px; font-size: 13px; line-height: 1.6; color: #666666;">
                        <strong>Lieferadresse:</strong><br>
                        {{ $address->street_address ?? $address->line_one }}<br>
                        {{ $address->postal_code ?? $address->postcode }} {{ $address->city }}<br>
                        {{ $address->country_name ?? $address->country->name }}
                    </td>
                </tr>
                <tr>
                    <td style="padding-top: 25px;" align="center">
                        <a href="{{ config('app.url') }}/track-order/{{ $order->order_number }}"
                            style="display: inline-block; background-color: #171717; color: #ffffff; padding: 12px 30px; text-decoration: none; font-size: 14px; font-weight: bold; border-radius: 0; text-transform: uppercase; letter-spacing: 1px;">
                            Bestellstatus verfolgen
                        </a>
                    </td>
                </tr>
                @if($order->tracking_number)
                <tr>
                    <td style="padding-top: 20px; font-size: 13px; line-height: 1.6; color: #666666; border-top: 1px solid #f3f4f6; margin-top: 20px;">
                        <strong>Sendungsnummer (DHL):</strong><br>
                        <span style="font-family: monospace; background-color: #f3f4f6; padding: 2px 5px;">{{ $order->tracking_number }}</span>
                        @if($order->label_url)
                            <br><a href="{{ $order->label_url }}" style="color: #4f46e5; text-decoration: underline; font-weight: bold;">Tracking Link öffnen &rarr;</a>
                        @endif
                    </td>
                </tr>
                @endif
            </table>
        </td>
    </tr>

    <tr>
        <td style="padding: 20px 40px; border-top: 1px solid #eeeeee;">
            <p style="font-size: 14px; font-weight: bold; color: #111111; margin-bottom: 10px;">Widerrufsbelehrung</p>
            <p style="font-size: 11px; color: #999999; line-height: 1.5;">
                Sie haben das Recht, binnen vierzehn Tagen ohne Angabe von Gründen diesen Vertrag zu widerrufen. Die
                Widerrufsfrist beträgt vierzehn Tage ab dem Tag, an dem Sie oder ein von Ihnen benannter Dritter, der nicht
                der Beförderer ist, die Waren in Besitz genommen haben. Um Ihr Widerrufsrecht auszuüben, müssen Sie uns mittels
                einer eindeutigen Erklärung über Ihren Entschluss informieren.
            </p>
        </td>
    </tr>
@endsection
