@extends('emails.layout')

@section('title', __('Return received'))
@section('subtitle') {{ __('Return No.') }} #{{ $return->id ?? '' }} @endsection
@section('subtitle_2')
    {{ __('Hello :name,', ['name' => $user->first_name ?? __('Customer')]) }}<br>
    {{ __('we have received your return. Below you will find an overview of the items received.') }}
@endsection

@section('preheader', __('Status update for your return #') . ($return->id ?? ''))

@section('content')
    <tr>
        <td align="center" style="padding: 0 20px 40px 20px;">
            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                style="max-width: 600px; background-color: #ffffff; border-radius: 8px; overflow: hidden;">

                <tr>
                    <td style="padding: 30px 30px 15px 30px; border-bottom: 2px solid #f0f0f0;">
                        <h2
                            style="margin: 0; color: #333333; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 18px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                            {{ __('Summary') }}
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

                @if($return->label_url || $return->qr_code_url)
                <tr>
                    <td style="padding: 20px 30px 0 30px;">
                        <table border="0" cellpadding="0" cellspacing="0" width="100%"
                            style="background-color: #f9f9f9; border-radius: 6px; border: 1px solid #e5e5e5;">
                            <tr>
                                <td style="padding: 20px; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; text-align: center;">
                                    <h3 style="margin: 0 0 15px 0; color: #333333; font-size: 16px; text-transform: uppercase; font-weight: 700;">{{ __('Your return label') }}</h3>
                                    
                                    @if($return->qr_code_url)
                                        <div style="margin-bottom: 20px;">
                                            <img src="{{ $return->qr_code_url }}" alt="QR Code" style="max-width: 150px; height: auto;">
                                            <p style="margin: 10px 0 0 0; font-size: 13px; color: #666666;">{{ __('Simply show this QR code at the parcel shop.') }}</p>
                                        </div>
                                    @endif

                                    @if($return->label_url)
                                        <a href="{{ $return->label_url }}" 
                                           style="display: inline-block; background-color: #000000; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 4px; font-weight: bold; font-size: 14px;">
                                            {{ __('DOWNLOAD LABEL') }}
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                @endif

                <tr>
                    <td style="padding: 30px;">
                        <table border="0" cellpadding="0" cellspacing="0" width="100%"
                            style="background-color: #f9f9f9; border-radius: 6px; border: 1px solid #e5e5e5;">
                            <tr>
                                <td
                                    style="padding: 15px; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 14px; color: #555555; line-height: 1.5;">
                                    <strong>{{ __('What happens next?') }}</strong><br>
                                    {{ __('Our logistics team is now checking the goods. The refund is usually made within 3-5 working days to your original payment method.') }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
@endsection
