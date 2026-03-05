@extends('emails.layout')

@section('title', 'PIEPJACK IST JETZT LIVE!')

@section('preheader', 'Der Moment ist da – Entdecke jetzt die neue Kollektion.')

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
                es ist soweit: Der Piepjack Shop ist ab sofort online!
            </p>
            <p
                style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 1.6; color: #374151; margin: 20px 0;">
                Du hast dich für diesen Moment registriert, und jetzt hast du als einer der Ersten die Chance, dir unsere exklusiven Pieces aus dem neuen Drop zu sichern.
            </p>
            <p
                style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 16px; line-height: 1.6; color: #374151; margin: 20px 0;">
                Warte nicht zu lange – die Bestände sind limitiert!
            </p>

            {{-- CTA Button --}}
            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top: 30px;">
                <tr>
                    <td align="center">
                        <a href="{{ url('/') }}"
                            style="background-color: #111111; color: #ffffff; padding: 15px 30px; text-decoration: none; font-size: 14px; font-weight: bold; border-radius: 4px; display: inline-block; letter-spacing: 1px;">
                            JETZT SHOPPEN
                        </a>
                    </td>
                </tr>
            </table>

            <p
                style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 14px; line-height: 1.6; color: #6b7280; margin: 40px 0 0 0;">
                Viel Spaß beim Shoppen,<br>
                Dein Piepjack Team
            </p>
        </td>
    </tr>
@endsection
