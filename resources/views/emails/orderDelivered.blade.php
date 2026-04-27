@extends('emails.layout')

@section('title', __('Deine Bestellung wurde geliefert!'))

@section('preheader')
    {{ __('Hallo :name, Deine Bestellung #:reference wurde erfolgreich zugestellt.', ['name' => $order->user->first_name ?? '', 'reference' => $order->reference]) }}
@endsection

@section('content')
    <tr>
        <td style="padding: 40px 40px 20px 40px;">
            <table border="0" cellspacing="0" cellpadding="0" width="100%">
                <tr>
                    <td style="padding-bottom: 20px; font-size: 18px; font-weight: bold; color: #111111;">
                        {{ __('Bestellung zugestellt!') }}
                    </td>
                </tr>
                <tr>
                    <td style="padding-bottom: 20px; font-size: 14px; line-height: 1.6; color: #666666;">
                        {{ __('Hallo :name, laut unserem Versandpartner wurde Deine Bestellung', ['name' => $order->user->first_name]) }} <strong>#{{ $order->reference }}</strong> {{ __('erfolgreich zugestellt.') }}
                    </td>
                </tr>
                <tr>
                    <td style="padding-bottom: 20px; font-size: 14px; line-height: 1.6; color: #666666;">
                        {{ __('Wir hoffen, dass Du mit Deinen neuen Piepjack-Teilen zufrieden bist!') }}
                    </td>
                </tr>
                <tr>
                    <td style="padding-top: 10px; font-size: 14px; line-height: 1.6; color: #666666;">
                        {{ __('Falls Du Fragen hast oder uns Feedback geben möchtest, antworte einfach auf diese E-Mail.') }}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
@endsection
