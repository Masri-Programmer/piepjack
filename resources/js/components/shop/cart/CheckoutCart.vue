<template>
    <div class="space-y-8">
        <!-- Product List -->
        <div class="space-y-1">
            <template v-for="product in cartState.cartItems" :key="product.id">
                <template v-for="item in product.items" :key="item.id">
                    <div class="bg-background border border-muted">
                        <ProductSmallCard :product="product" :item="item" />
                    </div>
                </template>
            </template>
        </div>

        <div class="pt-8 border-t border-muted space-y-4 text-main">
            <!-- Subtotal -->
            <div class="flex justify-between items-center">
                <p class="text-sm font-medium">
                    {{
                        $t("pages.checkout.subtotal", {
                            itemCount: cartTotalQuantity,
                        })
                    }}
                </p>
                <p class="text-sm font-bold">
                    {{ cartTotalPrice.toFixed(2) }} {{ $currency }}
                </p>
            </div>

            <!-- Selected Shipping Method Display -->
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium">Versand</p>
                    <p
                        class="text-[10px] text-muted-foreground uppercase tracking-wider"
                    >
                        {{ selectedShippingMethod.name }}
                    </p>
                </div>
                <p class="text-sm font-bold">
                    {{
                        selectedShippingMethod.price === 0
                            ? "Kostenlos"
                            : selectedShippingMethod.price.toFixed(2) + " €"
                    }}
                </p>
            </div>

            <!-- Discount -->
            <div
                v-if="cartTotalPrice > 100"
                class="flex justify-between items-center text-green-600"
            >
                <p class="text-sm font-medium">5% Discount (Orders > 100€)</p>
                <p class="text-sm font-bold">
                    -{{ (cartTotalPrice * 0.05).toFixed(2) }} {{ $currency }}
                </p>
            </div>

            <!-- Promo Code -->
            <div class="pt-4 space-y-3">
                <label
                    for="promoCode"
                    class="block text-[10px] font-bold uppercase tracking-widest text-gray-400"
                >
                    {{ $t("pages.checkout.promoCode") }}
                </label>
                <div class="flex items-center gap-2">
                    <Input
                        type="text"
                        id="promoCode"
                        v-model="promoCode"
                        class="flex-1 h-12 text-sm border border-muted focus:ring-0 focus:border-accent_dark text-main bg-background"
                        :placeholder="$t('pages.checkout.enterPromoCode')"
                    />
                    <Button
                        @click="applyPromoCode"
                        class="h-12 px-6 text-xs checkout-cart-promo-button view-all bg-primary text-primary-foreground hover:bg-gray-800 transition-colors"
                    >
                        {{ $t("pages.checkout.apply") }}
                    </Button>
                </div>
                <transition name="fade">
                    <p
                        v-if="promoApplied"
                        class="text-xs text-green-600 font-medium"
                    >
                        {{ $t("pages.checkout.promoApplied") }}
                    </p>
                    <p
                        v-else-if="promoError"
                        class="text-xs text-red-600 font-medium"
                    >
                        {{ $t("pages.checkout.invalidPromo") }}
                    </p>
                </transition>
            </div>

            <!-- Total -->
            <div class="pt-8 border-t border-muted">
                <div class="flex justify-between items-baseline mb-1">
                    <p class="text-sm font-bold uppercase tracking-widest">
                        {{ $t("pages.checkout.total") }}
                    </p>
                    <div class="text-right">
                        <p class="text-3xl font-accent_dark text-main">
                            {{ calculateFinalPrice() }}
                            <span class="text-sm font-bold ml-1">{{
                                $currency
                            }}</span>
                        </p>
                    </div>
                </div>
                <p
                    class="text-[10px] text-gray-400 font-medium text-right uppercase tracking-wider"
                >
                    {{
                        $t("pages.checkout.includingTaxes", {
                            taxAmount: "19%",
                        })
                    }}
                </p>
            </div>
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
    checkoutform,
} from "@lib/store/shop/index.js";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";

const promoCode = ref("");
const promoDiscount = ref(0);
const promoApplied = ref(false);
const promoError = ref(false);

const selectedShippingMethod = computed(() => {
    // Rely on the API data stored in the state
    return (
        checkoutform.value.shippingMethod || {
            price: 0,
            name: "Select Shipping",
        }
    );
});

const calculateFinalPrice = () => {
    let price = cartTotalPrice.value;

    // Apply Promo Discount
    if (promoApplied.value && promoDiscount.value > 0) {
        price = Math.max(0, price - promoDiscount.value);
    }

    // Apply 5% discount for orders > 100 EUR
    if (price > 100) {
        price = price * 0.95;
    }

    // Add API-driven Shipping
    price += selectedShippingMethod.value.price;

    return price.toFixed(2);
};

const applyPromoCode = () => {
    promoApplied.value = false;
    promoError.value = false;
    promoDiscount.value = 0;

    if (promoCode.value && promoCode.value.trim() !== "") {
        if (promoCode.value.trim().toLowerCase() === "pickup") {
            cartState.value.promoCode = "pickup";
            promoApplied.value = true;
            promoDiscount.value = 10;
        } else {
            promoError.value = true;
        }
    } else {
        promoError.value = true;
    }
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
