@extends('emails.layout')

@section('title', 'Auftragsbestätigung')
@section('subtitle') #{{ $order->order_number }}@endsection
@section('subtitle_2') Hallo {{ $user->first_name ?? '' }}, wir haben deine Bestellung erhalten und werden sie in Kürze
bearbeiten.@endsection

@section('preheader')
    Hallo {{ $user->first_name ?? '' }}, wir haben deine Bestellung erhalten und werden sie in Kürze bearbeiten.
@endsection

@section('content')
    {{-- Call to Action Button --}}
    <tr>
        <td align="center" style="padding: 0 30px 30px 30px;">
            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                <tr>
                    <td align="center">
                        <table border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td align="center" style="border-radius: 5px;" bgcolor="#dddee0">
                                    <a href="{{ config('app.url') }}/track-order/{{ $order->order_number }}" target="_blank"
                                        style="font-size: 14px; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #171717; text-decoration: none; text-transform: uppercase; font-weight: bold; border-radius: 5px; padding: 14px 25px; border: 1px solid #dddee0; display: inline-block;">Bestellung
                                        ansehen</a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- Order Summary --}}
    <tr>
        <td style="padding: 0 30px;">
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                {{-- Header --}}
                <tr>
                    <td class="section-header"
                        style="padding-bottom: 20px; border-bottom: 1px solid #444444; color: #f5f5f5; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 20px; font-weight: bold;">
                        Bestellübersicht
                    </td>
                </tr>
                {{-- Products Loop --}}
                {{-- FIX: The product loop is now simpler as `$products` contains all needed data. --}}
                @foreach ($products as $product)
                    @include('emails.products', ['product' => $product])
                @endforeach
            </table>
        </td>
    </tr>

    {{-- Totals --}}
    <tr>
        <td style="padding: 20px 30px;">
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="75%" align="left"
                        style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 16px; font-weight: bold; color: #f5f5f5; padding-top: 10px;">
                        Gesamtsumme
                    </td>
                    <td width="25%" align="right"
                        style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 16px; font-weight: bold; color: #f5f5f5; padding-top: 10px;">
                        €{{ number_format($order->total_price, 2) }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    {{-- Shipping Address --}}
    <tr>
        <td style="padding: 20px 30px; border-top: 1px solid #444444;">
            <h3
                style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 20px; font-weight: bold; color: #f5f5f5; margin:0 0 10px 0;">
                Lieferadresse</h3>
            {{-- FIX: Use the separate $user and $address objects to display details. --}}
            <p
                style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 24px; color: #dddee0; margin: 0;">
                {{ $user->first_name }} {{ $user->last_name }}<br>
                {{ $address->street_address }}<br>
                {{ $address->postal_code }} {{ $address->city }}<br>
                {{ $address->state_province }}, {{ $address->country_name }}
            </p>
        </td>
    </tr>
@endsection