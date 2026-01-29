{{-- Product Row --}}
<tr>
    {{-- Name & Options --}}
    <td style="padding: 12px 10px; border-bottom: 1px solid #f3f4f6; vertical-align: top;">
        <span style="font-size: 13px; color: #111111; font-weight: bold; display: block;">
            {{ $product['name'] ?? 'Produkt' }}
        </span>
        @if (!empty($product['options']))
            <div style="font-size: 11px; color: #888888; margin-top: 4px;">
                @foreach ($product['options'] as $option)
                    {{ $option['name'] }}: {{ $option['value'] }}@if(!$loop->last), @endif
                @endforeach
            </div>
        @endif
    </td>

    {{-- Quantity --}}
    <td
        style="padding: 12px 10px; border-bottom: 1px solid #f3f4f6; vertical-align: top; text-align: center; font-size: 13px; color: #555555;">
        {{ $product['quantity'] ?? 1 }}
    </td>

    {{-- Price per item --}}
    <td
        style="padding: 12px 10px; border-bottom: 1px solid #f3f4f6; vertical-align: top; text-align: right; font-size: 13px; color: #555555;">
        €{{ number_format($product['price_per_item'] ?? 0, 2, ',', '.') }}
    </td>

    {{-- Total Line Item --}}
    <td
        style="padding: 12px 10px; border-bottom: 1px solid #f3f4f6; vertical-align: top; text-align: right; font-size: 13px; color: #111111; font-weight: bold;">
        €{{ number_format(($product['price_per_item'] ?? 0) * ($product['quantity'] ?? 1), 2, ',', '.') }}
    </td>
</tr>