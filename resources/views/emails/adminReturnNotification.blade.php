@extends('emails.layout')

@section('title', 'Neuer Rücksendeantrag: #' . $return->id)

@section('preheader')
    Ein neuer Rücksendeantrag für Bestellung #{{ $return->order_number }} wurde gestellt.
@endsection

@section('content')
    <tr>
        <td style="padding: 40px 40px 20px 40px;">
            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                <tr>
                    <td colspan="2"
                        style="padding-bottom: 15px; border-bottom: 1px solid #eeeeee; font-size: 16px; font-weight: bold; color: #111111;">
                        Rücksendungs-Details (Admin) <span class="accent-color" style="float: right;">#{{ $return->id }}</span>
                    </td>
                </tr>
                <tr>
                    <td valign="top" width="50%" style="padding-top: 15px; padding-right: 10px;">
                        <p style="margin: 0 0 5px 0; font-size: 13px; color: #111111; font-weight: bold;">Ursprüngliche Bestellung:</p>
                        <p style="margin: 0 0 15px 0; font-size: 13px; color: #666666;">#{{ $return->order_number }}</p>

                        <p style="margin: 0 0 5px 0; font-size: 13px; color: #111111; font-weight: bold;">Kunde:</p>
                        <p style="margin: 0 0 15px 0; font-size: 13px; color: #666666;">{{ $user->first_name }} {{ $user->last_name }}<br>{{ $user->email }}</p>
                    </td>
                    <td valign="top" width="50%" style="padding-top: 15px; padding-left: 10px;">
                        <p style="margin: 0 0 5px 0; font-size: 13px; color: #111111; font-weight: bold;">Rücksendegrund:</p>
                        <p style="margin: 0 0 15px 0; font-size: 13px; color: #666666; font-style: italic;">"{{ $return->reason }}"</p>

                        <p style="margin: 0 0 5px 0; font-size: 13px; color: #111111; font-weight: bold;">Erstattungsbetrag:</p>
                        <p style="margin: 0 0 15px 0; font-size: 13px; color: #111111; font-weight: bold;">€{{ number_format($return->total, 2, ',', '.') }}</p>
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
                @foreach ($items as $product)
                    <tr>
                        <td style="padding: 12px 10px; border-bottom: 1px solid #f3f4f6; vertical-align: top;">
                            <span style="font-size: 13px; color: #111111; font-weight: bold; display: block;">{{ $product['product_name'] }}</span>
                            @if (!empty($product['options']))
                                <div style="font-size: 11px; color: #888888; margin-top: 4px;">
                                    @foreach ($product['options'] as $option)
                                        {{ $option['name'] }}: {{ $option['value'] }}@if(!$loop->last), @endif
                                    @endforeach
                                </div>
                            @endif
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
        <td style="padding: 30px 40px 40px 40px;" align="center">
            <a href="{{ config('app.url') }}/lunar/returns/{{ $return->id }}"
                style="display: inline-block; background-color: #171717; color: #ffffff; padding: 12px 25px; text-decoration: none; font-size: 14px; font-weight: bold; border-radius: 0;">
                Rücksendung bearbeiten
            </a>
        </td>
    </tr>
@endsection
