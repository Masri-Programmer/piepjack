<template>
    <div class="product-card">
        <span v-if="item?.cartQuantity" class="cart-badge">
            {{ item.cartQuantity }}
        </span>
        <img
            :src="
                item?.image_url ??
                'https://raw.githubusercontent.com/Loopple/loopple-public-assets/main/riva-dashboard-tailwind/img/img-49-new.jpg'
            "
            :alt="product.name"
            width="1667"
            height="2500"
            loading="lazy"
            sizes="100px"
            class="product-image"
        />
        <div class="product-details">
            <h6>
                <strong>{{ product.name }} (#{{ product.id }})</strong>
            </h6>
            <p v-if="item?.price" class="product-id">Id : {{ item?.id }}</p>
            <div class="product-options">
                <div
                    v-for="(option, index) in item?.options"
                    :key="index"
                    class="option-item"
                >
                    <p class="option-name">{{ option.name }}:</p>
                    <p class="option-value">{{ option.value }}</p>
                </div>
            </div>
            <p v-if="item?.price" class="product-price">
                {{ item?.price }} {{ $currency }}
            </p>
            <!-- <button
                v-if="options.addToCart && options.label !== 'no-label'"
                class="add-to-cart"
                @click="addItemToCart"
            >
                {{ $t("common.product.addToCart") }}
            </button> -->
            <CustomNumberInput
                v-if="options.addToCart"
                :modelValue="item.cartQuantity"
                :addToCart="options.addToCart"
                :max="item.quantity"
                :product="product"
                :item="item"
            />
        </div>
    </div>
</template>
<script setup>
import CustomNumberInput from "@ui/CustomNumberInput.vue";
import "@assets/css/components/product/card.css";

const props = defineProps({
    product: { type: Object, required: true },
    item: { type: Object, required: true },
    options: { type: Object, required: false, default: () => ({}) },
});
</script>
