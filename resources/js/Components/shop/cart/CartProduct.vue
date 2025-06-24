<template>
    <td class="cart-product-product">
        <div class="cart-product-product-info">
            <img
                :src="
                    item?.image_url ??
                    'https://raw.githubusercontent.com/Loopple/loopple-public-assets/main/riva-dashboard-tailwind/img/img-49-new.jpg'
                "
                :alt="product.name"
                loading="lazy"
                class="cart-product-product-image"
            />
            <h6 class="cart-product-product-name">
                <strong>{{ product.name }} (#{{ product.id }})</strong>
            </h6>
            <div class="cart-product-product-options">
                <p class="cart-product-product-id" v-if="item?.price">
                    id : {{ item?.id }}
                </p>
                <div
                    v-for="(option, index) in item?.options"
                    :key="index"
                    class="cart-product-product-option"
                >
                    <p class="cart-product-option-name">{{ option.name }}:</p>
                    <p class="cart-product-option-value">{{ option.value }}</p>
                </div>
                <p class="cart-product-product-price" v-if="item?.price">
                    {{ item?.price }} {{ $currency }}
                </p>
            </div>
        </div>
    </td>
    <td class="cart-product-quantity">
        <CustomNumberInput
            :addToCart="options.addToCart"
            :modelValue="item.cartQuantity"
            :product="product"
            :item="item"
            :max="item.quantity"
        />
    </td>
    <td class="cart-product-total">
        {{ (item.cartQuantity * item.price).toFixed(2) }} {{ $currency }}
    </td>
</template>

<script setup>
import "@assets/css/cart/cartProduct.css";
import CustomNumberInput from "@ui/CustomNumberInput.vue";

const props = defineProps({
    product: { type: Object, required: true },
    item: { type: Object, required: true },
    options: { type: Object, required: false, default: () => ({}) },
});
</script>
