@extends('emails.layout')

@section('title', 'Neuer Rücksendeantrag: #' . $return->id)

@section('preheader')
    Ein neuer Rücksendeantrag für Bestellung #{{ $return->order->reference ?? $return->order_id }} wurde gestellt.
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
                        <p style="margin: 0 0 5px 0; font-size: 13px; color: #666666;">
                            #{{ $return->order->reference ?? $return->order_id }}
                        </p>
                        <p style="margin: 0 0 15px 0; font-size: 13px;">
                            <a href="{{ config('app.url') }}/lunar/orders/{{ $return->order_id }}" style="color: #171717; text-decoration: underline;">
                                Im Hub ansehen &rarr;
                            </a>
                        </p>

                        <p style="margin: 0 0 5px 0; font-size: 13px; color: #111111; font-weight: bold;">Kunde:</p>
                        <p style="margin: 0 0 15px 0; font-size: 13px; color: #666666;">{{ $user->first_name }} {{ $user->last_name }}<br>{{ $user->email }}</p>
                    </td>
                    <td valign="top" width="50%" style="padding-top: 15px; padding-left: 10px;">
                        <p style="margin: 0 0 5px 0; font-size: 13px; color: #111111; font-weight: bold;">Rücksendegrund:</p>
                        <p style="margin: 0 0 15px 0; font-size: 13px; color: #666666; font-style: italic;">"{{ $return->reason }}"</p>

                        <p style="margin: 0 0 5px 0; font-size: 13px; color: #111111; font-weight: bold;">Erstattungsbetrag:</p>
                        <p style="margin: 0 0 15px 0; font-size: 13px; color: #111111; font-weight: bold;">€{{ number_format($return->total ?? 0, 2, ',', '.') }}</p>
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
                <tr>
                    <td colspan="2" style="padding-top: 20px;">
                        <ul style="padding: 0; margin: 0;">
                            @foreach($items as $product)
                                <li style="margin-bottom: 20px; list-style: none; display: flex; gap: 15px;">
                        
                                    @if(!empty($product['image']))
                                        <div>
                                            <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}" style="width: 80px; height: auto; border-radius: 4px;">
                                        </div>
                                    @endif
                        
                                    <div>
                                        <strong>Product:</strong> {{ $product['name'] }} <br>
                                        <strong>Quantity:</strong> {{ $product['quantity'] }} <br>
                                        <strong>Price:</strong> {{ $product['price_per_item'] }} € <br>
                        
                                        @if(!empty($product['options']))
                                            <small style="color: #555;">
                                                @foreach($product['options'] as $option)
                                                    {{ $option['name'] }}: {{ $option['value'] }}<br>
                                                @endforeach
                                            </small>
                                        @endif
                                    </div>
                        
                                </li>
                            @endforeach
                        </ul>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td style="padding: 30px 40px 40px 40px;" align="center">
            <a href="{{ config('app.url') }}/lunar/orders/{{ $return->order_id }}"
                style="display: inline-block; background-color: #f3f4f6; color: #111111; border: 1px solid #dddddd; padding: 12px 25px; text-decoration: none; font-size: 14px; font-weight: bold; border-radius: 0; margin-right: 10px;">
                Bestellung ansehen
            </a>
            <a href="{{ config('app.url') }}/lunar/returns/{{ $return->id }}"
                style="display: inline-block; background-color: #171717; color: #ffffff; padding: 12px 25px; text-decoration: none; font-size: 14px; font-weight: bold; border-radius: 0;">
                Rücksendung bearbeiten
            </a>
        </td>
    </tr>
@endsection