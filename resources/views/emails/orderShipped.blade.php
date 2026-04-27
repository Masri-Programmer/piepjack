@extends('emails.layout')

@section('title', __('Deine Bestellung wurde versandt!'))

@section('preheader')
    {{ __('Hallo :name, Deine Bestellung #:reference ist auf dem Weg zu Dir.', ['name' => $order->user->first_name ?? '', 'reference' => $order->reference]) }}
@endsection

@section('content')
    <tr>
        <td style="padding: 40px 40px 20px 40px;">
            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                <tr>
                    <td style="padding-bottom: 20px; font-size: 18px; font-weight: bold; color: #111111;">
                        {{ __('Großartige Neuigkeiten!') }}
                    </td>
                </tr>
                <tr>
                    <td style="padding-bottom: 20px; font-size: 14px; line-height: 1.6; color: #666666;">
                        {{ __('Deine Bestellung mit der Nummer') }} <strong>#{{ $order->reference }}</strong> {{ __('wurde soeben versandt und ist auf dem Weg zu Dir.') }}
                    </td>
                </tr>
                
                @if($trackingNumber)
                <tr>
                    <td style="padding: 20px; background-color: #f3f4f6; border-radius: 4px;">
                        <p style="margin: 0 0 10px 0; font-size: 14px; color: #111111; font-weight: bold;">{{ __('Sendungsverfolgung') }}:</p>
                        <p style="margin: 0 0 15px 0; font-family: monospace; font-size: 16px; color: #111111; letter-spacing: 1px;">{{ $trackingNumber }}</p>
                        
                        @php
                            // Sendcloud tracking URL pattern or use label_url if available
                            $trackingUrl = $order->label_url ?? "https://track.sendcloud.sc/tracking/" . $trackingNumber;
                        @endphp
                        
                        <a href="{{ $trackingUrl }}" 
                           style="display: inline-block; background-color: #171717; color: #ffffff; padding: 10px 20px; text-decoration: none; font-size: 13px; font-weight: bold; text-transform: uppercase;">
                            {{ __('Sendung verfolgen') }}
                        </a>
                    </td>
                </tr>
                @endif
                
                <tr>
                    <td style="padding-top: 30px; font-size: 14px; line-height: 1.6; color: #666666;">
                        {{ __('Vielen Dank für Dein Vertrauen in Piepjack!') }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
@endsection
