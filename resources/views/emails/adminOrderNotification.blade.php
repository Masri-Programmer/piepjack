@extends('emails.layout')

@section('title', 'Neue Bestellung: #' . $order->reference)

@section('preheader')
    Eine neue Bestellung von {{ $user->first_name }} {{ $user->last_name }} ist eingegangen.
@endsection

@section('content')
    <tr>
        <td style="padding: 40px 40px 20px 40px;">
            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                <tr>
                    <td colspan="2"
                        style="padding-bottom: 15px; border-bottom: 1px solid #eeeeee; font-size: 16px; font-weight: bold; color: #111111;">
                        Bestellübersicht (Admin) <span class="accent-color" style="float: right;">#{{ $order->reference }}</span>
                    </td>
                </tr>
                <tr>
                    {{-- Left Column --}}
                    <td valign="top" width="50%" style="padding-top: 15px; padding-right: 10px;">
                        <p style="margin: 0 0 5px 0; font-size: 13px; color: #111111; font-weight: bold;">Kunde:</p>
                        <p style="margin: 0 0 15px 0; font-size: 13px; color: #666666;">{{ $user->first_name }} {{ $user->last_name }}<br>{{ $user->email }}</p>

                        <p style="margin: 0 0 5px 0; font-size: 13px; color: #111111; font-weight: bold;">Status:</p>
                        <p style="margin: 0 0 15px 0; font-size: 13px; color: #10b981; font-weight: bold; text-transform: uppercase;">{{ $order->status }}</p>
                    </td>
                    {{-- Right Column --}}
                    <td valign="top" width="50%" style="padding-top: 15px; padding-left: 10px;">
                        <p style="margin: 0 0 5px 0; font-size: 13px; color: #111111; font-weight: bold;">Datum:</p>
                        <p style="margin: 0 0 15px 0; font-size: 13px; color: #666666;">{{ $order->created_at->format('d.m.Y H:i') }}</p>

                        <p style="margin: 0 0 5px 0; font-size: 13px; color: #111111; font-weight: bold;">Gesamtbetrag:</p>
                        <p style="margin: 0 0 15px 0; font-size: 13px; color: #111111; font-weight: bold;">€{{ number_format($total ?? 0, 2, ',', '.') }}</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td style="padding: 0 40px;">
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr style="background-color: #f3f4f6;">
                    <td style="padding: 10px; font-size: 12px; font-weight: bold; color: #111111; width: 70%;">Produkt</td>
                    <td style="padding: 10px; font-size: 12px; font-weight: bold; color: #111111; text-align: center; width: 30%;">Menge</td>
                </tr>
                @foreach ($products as $product)
                    <tr>
                        <td style="padding: 12px 10px; border-bottom: 1px solid #f3f4f6; vertical-align: top;">
                            <span style="font-size: 13px; color: #111111; font-weight: bold; display: block;">{{ $product['name'] }}</span>
                        </td>
                        <td style="padding: 12px 10px; border-bottom: 1px solid #f3f4f6; vertical-align: top; text-align: center; font-size: 13px; color: #555555;">
                            {{ $product['quantity'] }}
                        </td>
                    </tr>
                @endforeach
            </table>
        </td>
    </tr>

    <tr>
        <td style="padding: 30px 40px 40px 40px;">
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td style="padding-bottom: 10px; border-bottom: 1px solid #eeeeee; font-size: 14px; font-weight: bold; color: #111111;">
                        {{ $order->shippingLines->first()?->identifier === 'PICKUP' ? 'Abholung' : 'Lieferadresse' }}
                    </td>
                </tr>
                <tr>
                    <td style="padding-top: 15px; font-size: 13px; line-height: 1.6; color: #666666;">
                        @if($order->shippingLines->first()?->identifier === 'PICKUP')
                            <strong style="color: #111111; font-size: 15px;">ABHOLUNG: {{ strtoupper(config('shop.address.full')) }}</strong><br>
                            Kunde wird benachrichtigt, sobald bereit.
                        @else
                            {{ $address->first_name }} {{ $address->last_name }}<br>
                            {{ $address->line_one }}<br>
                            {{ $address->postcode }} {{ $address->city }}<br>
                            {{ $address->country->name }}
                        @endif
                    </td>
                </tr>

                {{-- NEW: Added tracking info for the admin to see as well --}}
                @if(!empty($order->meta['tracking_number']))
                <tr>
                    <td style="padding-top: 20px; font-size: 13px; line-height: 1.6; color: #666666; border-top: 1px solid #f3f4f6; margin-top: 20px;">
                        <strong>Sendungsnummer (Sendcloud):</strong> <span style="font-family: monospace; background-color: #f3f4f6; padding: 2px 5px;">{{ $order->meta['tracking_number'] }}</span>
                    </td>
                </tr>
                @endif

                <tr>
                    <td style="padding-top: 25px;" align="center">
                        <a href="{{ config('app.url') }}/lunar/orders/{{ $order->id }}"
                            style="display: inline-block; background-color: #171717; color: #ffffff; padding: 12px 25px; text-decoration: none; font-size: 14px; font-weight: bold; border-radius: 0;">
                            Bestellung im Admin-Panel öffnen
                        </a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
@endsection