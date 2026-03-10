@extends('emails.layout')

@section('title', 'Willkommen bei Piepjack!')

@section('preheader', 'Du bist erfolgreich für den nächsten Drop registriert.')

@section('content')
    {{-- Content Section --}}
    <tr>
        <td style="padding: 20px 40px 40px 40px; text-align: left;">
            <p
                style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 1.6; color: #374151; margin: 0;">
                Hallo {{ $registration->name }},
            </p>
            <p
                style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 1.6; color: #374151; margin: 20px 0;">
                vielen Dank für dein Interesse an Piepjack! Wir haben deine Anmeldung erhalten und werden dich
                benachrichtigen, sobald unsere neue Kollektion am {{ \Carbon\Carbon::parse(config('app.launch_date'))->translatedFormat('d. F') }} um {{ \Carbon\Carbon::parse(config('app.launch_date'))->format('H:i') }} Uhr live geht.
            </p>
            <p
                style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 1.6; color: #374151; margin: 20px 0;">
                Bereite dich auf den Drop vor.
            </p>

            {{-- CTA Button --}}
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top: 30px;">
                <tr>
                    <td align="center">
                        <a href="{{ url('/') }}"
                            style="background-color: #111111; color: #ffffff; padding: 15px 30px; text-decoration: none; font-size: 14px; font-weight: bold; border-radius: 4px; display: inline-block; letter-spacing: 1px;">
                            ZUM SHOP
                        </a>
                    </td>
                </tr>
            </table>

            <p
                style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; color: #6b7280; margin: 40px 0 0 0;">
                Bis bald,<br>
                Dein Piepjack Team
            </p>
        </td>
    </tr>
@endsection