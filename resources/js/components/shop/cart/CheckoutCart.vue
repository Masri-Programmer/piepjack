<template>
    <div
        class="md:max-h-[50%] overflow-auto border grid grid-cols-1 md:grid-cols-2 border-gray"
    >
        <template v-for="product in cartState.cartItems">
            <template v-for="item in product.items">
                <ProductSmallCard :product="product" :item="item" />
            </template>
        </template>
    </div>

    <div class="grid gap-3 m-6 text-main">
        <div class="flex justify-between">
            <p class="text-sm">
                {{
                    $t("pages.checkout.subtotal", {
                        itemCount: cartTotalQuantity(),
                    })
                }}
            </p>
            <p class="text-sm">{{ cartTotalPrice() }} {{ $currency }}</p>
        </div>
        <div class="flex justify-between">
            <p class="text-sm">{{ $t("pages.checkout.shipping") }}</p>
            <p v-if="cartTotalPrice() >= 100 || freeShipping" class="text-sm">
                {{ $t("pages.checkout.free") }}
            </p>
            <p v-else class="text-sm">5.90 {{ $currency }}</p>
        </div>

        <div class="py-3 border-t border-b border-gray">
            <label for="promoCode" class="block mb-2 text-sm">
                {{ $t("pages.checkout.promoCode") }}
            </label>
            <div class="flex items-center gap-2">
                <input
                    type="text"
                    id="promoCode"
                    v-model="promoCode"
                    class="flex-1 px-4 py-4 text-sm border border-gray focus:outline-none text-accent"
                    :placeholder="$t('pages.checkout.enterPromoCode')"
                />
                <button
                    @click="applyPromoCode"
                    class="text-xs checkout-cart-promo-button view-all"
                >
                    {{ $t("pages.checkout.apply") }}
                </button>
            </div>
            <p v-if="promoApplied" class="mt-2 text-xs text-green-500">
                {{ $t("pages.checkout.promoApplied") }}
            </p>
            <p v-if="promoError" class="mt-2 text-xs text-red-500">
                {{ $t("pages.checkout.invalidPromo") }}
            </p>
        </div>

        <div class="mb-4">
            <div class="flex justify-between">
                <p class="text-xl font-bold">
                    <strong>{{ $t("pages.checkout.total") }}</strong>
                </p>
                <p class="text-xl font-bold">
                    {{ calculateFinalPrice() }}
                    {{ $currency }}
                </p>
            </div>
            <p class="text-sm text-zinc-200">
                {{ $t("pages.checkout.includingTaxes", { taxAmount: "19%" }) }}
            </p>
        </div>
    </div>
</template>

<script setup>
import ProductSmallCard from "../product/ProductSmallCard.vue";
import { reactive, computed, ref, onBeforeMount } from "vue";
import { loadStripe } from "@stripe/stripe-js";
import {
    cartState,
    cartTotalPrice,
    cartTotalQuantity,
} from "@lib/store/shop/index.js";

const coupon = reactive(null);
const promoCode = ref("");
const promoDiscount = ref(0);
const promoApplied = ref(false);
const promoError = ref(false);
const freeShipping = ref(false);

const applyPromoCode = () => {
    promoApplied.value = false;
    promoError.value = false;
    promoDiscount.value = 0;
    freeShipping.value = false;

    if (promoCode.value && promoCode.value.trim() !== "") {
        if (promoCode.value.trim().toLowerCase() === "pickup") {
            cartState.value.promoCode = "pickup";
            promoApplied.value = true;
            freeShipping.value = true;
        } else {
            promoApplied.value = false;
            promoDiscount.value = 10;
        }
    } else {
        promoError.value = true;
    }
};

const calculateFinalPrice = () => {
    let price = cartTotalPrice();

    if (price < 100 && !freeShipping.value) {
        price += 5.9;
    }

    if (promoApplied.value && promoDiscount.value > 0) {
        price = Math.max(0, price - promoDiscount.value);
    }

    return price.toFixed(2);
};

onBeforeMount(() => {
    cartState.value.promoCode = "";
    const stripeLoaded = ref(false);
    const stripePublicKey = import.meta.env.VITE_STRIPE_PUBLIC_KEY;
    const stripePromise = loadStripe(stripePublicKey);
    stripePromise.then(() => {
        stripeLoaded.value = true;
    });
});
</script>
