@extends('emails.layout')

@section('title')
    {{ __('Return Label for Order #') }}{{ $order->reference }}
@endsection

@section('content')
    <tr>
        <td style="padding: 30px 40px;">
            <p style="font-size: 16px; line-height: 1.6; color: #171717; margin: 0;">
                {{ __('Hello') }} {{ $order->user->first_name }},
            </p>
            <p style="font-size: 16px; line-height: 1.6; color: #171717; margin: 20px 0;">
                {{ __('Your return request for order #') }}<strong>{{ $order->reference }}</strong>
                {{ __('has been accepted.') }}
            </p>
            <p style="font-size: 16px; line-height: 1.6; color: #171717; margin: 20px 0;">
                {{ __('You can download your shipping label using the button below. Please print it out and attach it to your package.') }}
            </p>

            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin: 30px 0;">
                <tr>
                    <td align="center">
                        <a href="{{ $labelUrl }}" target="_blank"
                            style="background-color: #171717; color: #ffffff; padding: 15px 30px; text-decoration: none; font-weight: bold; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; display: inline-block;">
                            {{ __('Download Return Label') }}
                        </a>
                    </td>
                </tr>
            </table>

            <p style="font-size: 14px; line-height: 1.6; color: #6b7280; margin: 20px 0;">
                {{ __("If the button doesn't work, you can also copy and paste this link into your browser:") }}<br>
                <a href="{{ $labelUrl }}" style="color: #4f46e5;">{{ $labelUrl }}</a>
            </p>

            <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #eeeeee;">
                <p style="font-size: 14px; font-weight: bold; color: #111111; margin-bottom: 10px;">
                    {{ __('Return Details:') }}
                </p>
                <p style="font-size: 14px; color: #6b7280; margin: 5px 0;">
                    <strong>{{ __('Return Number:') }}</strong> #{{ $returning->return_number }}
                </p>
            </div>
        </td>
    </tr>
@endsection