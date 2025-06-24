<!DOCTYPE html>
<html>

<head>
    <title>Return Request Confirmation</title>
</head>

<body>
    <h1>Your Return Request Has Been Approved</h1>
    <p>Dear {{ $customer->name }},</p>
    <p>We are pleased to inform you that your return request for the following items has been approved:</p>
    <ul>
        @foreach ($items as $item)
        <li>
            {{ $item->orderProduct->product_name }} - Quantity: {{ $item->quantity }}
        </li>
        @endforeach
    </ul>
    <p>If you have any questions or need further assistance, feel free to contact us.</p>
    <p>Thank you for shopping with us!</p>
    <p>Best regards,<br>{{env('APP_NAME')}}</p>
</body>

</html>