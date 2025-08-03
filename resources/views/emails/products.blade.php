<tr>
    <td style="padding: 20px 0; border-bottom: 1px solid #444444;">
        <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                {{-- Product Image --}}
                <td class="product-image" width="100" valign="top">
                    <img src="{{ $product['image'] ?? 'https://placehold.co/100x100/252526/9e9e9e?text=Produkt' }}" alt="{{ $product['name'] ?? 'Produktbild' }}" width="100" height="100" style="display: block; border-radius: 6px; object-fit: cover;">
                </td>

                {{-- Product Details --}}
                <td valign="top" style="padding-left: 20px; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #dddee0;">
                    <div style="font-size: 16px; font-weight: bold;">{{ $product['name'] ?? 'Produktname' }}</div>

                    {{-- Product Options (e.g., Size, Color) --}}
                    @if (!empty($product['options']))
                    <div style="font-size: 14px; color: #9e9e9e; padding-top: 5px;">
                        @foreach ($product['options'] as $option)
                        {{ $option['name'] }}: {{ $option['value'] }}<br>
                        @endforeach
                    </div>
                    @endif

                    <div style="font-size: 14px; color: #9e9e9e; padding-top: 5px;">Menge: {{ $product['quantity'] ?? 1 }}</div>
                </td>

                {{-- Product Price --}}
                <td width="80" valign="top" align="right" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 16px; font-weight: bold; color: #f5f5f5;">
                    €{{ number_format(($product['price_per_item'] ?? 0) * ($product['quantity'] ?? 1), 2, ',', '.') }}
                </td>
            </tr>
        </table>
    </td>
</tr>