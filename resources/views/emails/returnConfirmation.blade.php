@extends('emails.layout')

@section('title', 'Bestätigung deiner Rücksendung')
@section('subtitle') Rücksende-Nr. #{{ $return->id ?? '' }}@endsection
@section('subtitle_2') Hallo {{ $user->first_name ?? '' }}, wir haben die Artikel aus deiner Rücksendung erhalten und
werden sie in Kürze bearbeiten.@endsection

@section('preheader', 'Wir haben deine Rücksendung erhalten und werden sie in Kürze bearbeiten.')

@section('content')

    <tr>
        <td style="padding: 0 30px 30px 30px;">
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td class="section-header"
                        style="padding-bottom: 20px; border-bottom: 1px solid #444444; color: #444444; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 20px; font-weight: bold;">
                        Zusammenfassung der Rücksendung
                    </td>
                </tr>
                @foreach ($items as $product)
                    @include('emails.products', ['product' => $product])
                @endforeach
            </table>
        </td>
    </tr>
@endsection