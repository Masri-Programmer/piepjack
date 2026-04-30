<template>
    <div class="space-y-8">
        <!-- Product List -->
        <div class="space-y-1">
            <template v-for="product in cartState.cartItems" :key="product.id">
                <template v-for="item in product.items" :key="item.id">
                    <SwipeToDelete
                        :isUnavailable="item.quantity === 0"
                        @delete="
                            removeFromCart({
                                productId: product.id,
                                itemId: item.id,
                            })
                        "
                        class="border-b border-muted last:border-b-0"
                    >
                        <ProductSmallCard :product="product" :item="item" />

                        <!-- Status message for out of stock -->
                        <div
                            v-if="item.quantity === 0"
                            class="px-4 pb-4 flex items-center justify-between"
                        >
                            <p
                                class="text-[10px] font-black uppercase tracking-widest text-destructive"
                            >
                                {{ $t("common.product.outOfStock") }}
                            </p>
                        </div>
                    </SwipeToDelete>
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
                    <p class="text-sm font-medium">
                        {{ $t("pages.checkout.shipping") }}
                    </p>
                    <p
                        class="text-[10px] text-muted-foreground uppercase tracking-wider"
                    >
                        {{ selectedShippingMethod.name }}
                    </p>
                </div>
                <p class="text-sm font-bold">
                    {{
                        selectedShippingMethod.price === 0
                            ? $t("pages.checkout.free")
                            : selectedShippingMethod.price.toFixed(2) +
                              " " +
                              $currency
                    }}
                </p>
            </div>

            <!-- Dynamic Discounts -->
            <div
                v-for="discount in appliedDiscounts"
                :key="discount.id"
                class="flex justify-between items-center text-green-600"
            >
                <p class="text-sm font-medium">
                    {{ discount.name }}
                </p>
                <p class="text-sm font-bold">
                    -{{ discount.amount.toFixed(2) }} {{ $currency }}
                </p>
            </div>

            <!-- Promo Code -->
            <div class="pt-4 space-y-3">
                <label
                    for="promoCode"
                    class="block text-[10px] font-bold uppercase tracking-widest text-muted-foreground"
                >
                    {{ $t("pages.checkout.promoCode") }}
                </label>

                <div class="grid grid-cols-[1fr_auto] gap-3 w-full">
                    <Input
                        type="text"
                        id="promoCode"
                        v-model="promoCode"
                        :placeholder="$t('pages.checkout.enterPromoCode')"
                    />
                    <Button
                        @click="applyPromoCode"
                        class="view-all whitespace-nowrap"
                    >
                        {{ $t("pages.checkout.apply") }}
                    </Button>
                </div>

                <transition name="fade">
                    <p
                        v-if="promoApplied"
                        class="text-xs text-green-600 dark:text-green-400 font-bold uppercase tracking-wide"
                    >
                        {{ $t("pages.checkout.promoApplied") }}
                    </p>
                    <p
                        v-else-if="promoError"
                        class="text-xs text-destructive font-bold uppercase tracking-wide"
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
import SwipeToDelete from "../../ui/SwipeToDelete.vue";
import { reactive, computed, ref, onBeforeMount } from "vue";
import { loadStripe } from "@stripe/stripe-js";
import { useI18n } from "vue-i18n";
import {
    cartState,
    cartTotalPrice,
    cartTotalQuantity,
    checkoutform,
    fetchDiscounts,
    removeFromCart,
} from "@lib/store/shop/index.js";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";

const { t } = useI18n();

const promoCode = ref("");
const promoApplied = ref(false);
const promoError = ref(false);

const selectedShippingMethod = computed(() => {
    return (
        checkoutform.value.shippingMethod || {
            price: 0,
            name: t("pages.checkout.selectShipping"),
        }
    );
});
const applyPromoCode = () => {
    promoError.value = false;
    promoApplied.value = false;

    const code = promoCode.value.trim().toLowerCase();

    if (!code) {
        cartState.value.promoCode = "";
        return;
    }

    // Check backend discounts
    const matched = (cartState.value.discounts || []).find(
        (d) => d.coupon_code && d.coupon_code.toLowerCase() === code,
    );

    // Hardcoded check for "pickup" or backend match
    if (matched || code === "pickup") {
        promoApplied.value = true;
        // If it's the hardcoded 'pickup', set it, otherwise use the backend code
        cartState.value.promoCode = matched ? matched.coupon_code : "PICKUP";
    } else {
        promoError.value = true;
        cartState.value.promoCode = "";
    }
};

onBeforeMount(async () => {
    await fetchDiscounts();
    // If there's already a promo code in the store, hydrate it
    if (cartState.value.promoCode) {
        promoCode.value = cartState.value.promoCode;
        applyPromoCode();
    }

    const stripeLoaded = ref(false);
    const stripePublicKey = import.meta.env.VITE_STRIPE_PUBLIC_KEY;
    const stripePromise = loadStripe(stripePublicKey);
    stripePromise.then(() => {
        stripeLoaded.value = true;
    });
});

// Watch for manual promo code entry
import { watch } from "vue";
watch(promoCode, (newVal) => {
    if (newVal?.trim().toLowerCase() === "pickup") {
        applyPromoCode();
    }
});

const appliedDiscounts = computed(() => {
    const discounts = [];
    const subtotal = cartTotalPrice.value;
    const availableDiscounts = cartState.value.discounts || [];
    const currentItems = cartState.value.cartItems || [];

    // Add logic for hardcoded PICKUP discount (e.g., 0 discount but valid code)
    if (promoApplied.value && promoCode.value.toLowerCase() === "pickup") {
        // Only push if you actually want 'PICKUP' to take money off
        // Otherwise, it just stays 'Applied' with no price change
    }

    availableDiscounts.forEach((discount) => {
        // 1. Check if it's a coupon discount
        if (discount.coupon_code) {
            // Only apply if it matches the current applied promo code
            if (
                !promoApplied.value ||
                discount.coupon_code.toLowerCase() !==
                    cartState.value.promoCode.toLowerCase()
            ) {
                return;
            }
        }

        // 2. Check Min Basket Value
        // The min_prices and fixed_amount are now correctly mapped in the resource
        const minPrice = discount.config.min_prices?.EUR || 0;
        if (minPrice > 0 && subtotal * 100 < minPrice) {
            return;
        }

        // 3. Calculate Discount Amount
        let amount = 0;
        if (discount.config.is_fixed && discount.config.fixed_amount) {
            amount = Number(discount.config.fixed_amount) / 100;
        } else if (discount.config.percentage) {
            amount = subtotal * (Number(discount.config.percentage) / 100);
        }

        if (amount > 0) {
            discounts.push({
                id: discount.id,
                name: discount.name,
                amount: amount,
            });
        }
    });

    return discounts;
});

const calculateFinalPrice = () => {
    const originalSubtotal = cartTotalPrice.value;
    const totalDiscount = appliedDiscounts.value.reduce(
        (acc, d) => acc + d.amount,
        0,
    );

    const finalPrice =
        Math.max(0, originalSubtotal - totalDiscount) +
        selectedShippingMethod.value.price;

    return finalPrice.toFixed(2);
};
</script>
