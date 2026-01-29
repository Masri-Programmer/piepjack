@extends('emails.layout')

@section('title', 'Rücksendung erhalten')
@section('subtitle') Rücksende-Nr. #{{ $return->id ?? '' }} @endsection
@section('subtitle_2')
    Hallo {{ $user->first_name ?? 'Kunde' }},<br>
    wir haben deine Rücksendung erhalten. Unten findest du eine Übersicht der eingegangenen Artikel.
@endsection

@section('preheader', 'Status-Update zu deiner Rücksendung #' . ($return->id ?? ''))

@section('content')
    <tr>
        <td align="center" style="padding: 0 20px 40px 20px;">
            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                style="max-width: 600px; background-color: #ffffff; border-radius: 8px; overflow: hidden;">

                <tr>
                    <td style="padding: 30px 30px 15px 30px; border-bottom: 2px solid #f0f0f0;">
                        <h2
                            style="margin: 0; color: #333333; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                            Zusammenfassung
                        </h2>
                    </td>
                </tr>

                <tr>
                    <td style="padding: 0 30px;">
                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                            {{-- Ensure your 'emails.products' view outputs <tr> elements with <td> cells --}}
                                    @foreach ($items as $product)
                                                @include('emails.products', ['product' => $product])

                                                {{-- Optional: Add a spacer row between items if your include doesn't have borders --}}
                                        <tr>
                                            <td height="1" style="background-color: #f0f0f0; line-height: 1px; font-size: 1px;">&nbsp;
                                            </td>
                                        </tr>
                                    @endforeach
                        </table>
                    </td>
                </tr>

                <tr>
                    <td style="padding: 30px;">
                        <table border="0" cellpadding="0" cellspacing="0" width="100%"
                            style="background-color: #f9f9f9; border-radius: 6px; border: 1px solid #e5e5e5;">
                            <tr>
                                <td
                                    style="padding: 15px; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 14px; color: #555555; line-height: 1.5;">
                                    <strong>Wie geht es weiter?</strong><br>
                                    Unsere Logistik prüft nun die Ware. Die Erstattung erfolgt in der Regel innerhalb von
                                    3-5 Werktagen auf dein ursprüngliches Zahlungsmittel.
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
@endsection