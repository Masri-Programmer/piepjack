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
                    class="block text-[10px] font-bold uppercase tracking-widest text-gray-400"
                >
                    {{ $t("pages.checkout.promoCode") }}
                </label>
                <div class="flex items-center gap-2">
                    <Input
                        type="text"
                        id="promoCode"
                        v-model="promoCode"
                        class="flex-1 h-16 text-sm border border-muted focus:ring-0 focus:border-accent_dark text-main bg-background"
                        :placeholder="$t('pages.checkout.enterPromoCode')"
                    />
                    <Button @click="applyPromoCode" class="view-all">
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
import { useI18n } from "vue-i18n";
import {
    cartState,
    cartTotalPrice,
    cartTotalQuantity,
    checkoutform,
    fetchDiscounts,
} from "@lib/store/shop/index.js";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";

const { t } = useI18n();

const promoCode = ref("");
const promoApplied = ref(false);
const promoError = ref(false);

const selectedShippingMethod = computed(() => {
    const method = checkoutform.value.shippingMethod || {
        price: 0,
        name: t("pages.checkout.selectShipping"),
    };

    // Auto-apply Free Shipping if cart total is over 100
    if (cartTotalPrice.value >= 100) {
        return {
            ...method,
            price: 0,
            name: `${method.name} (Free)`,
        };
    }

    return method;
});

const applyPromoCode = () => {
    promoError.value = false;
    promoApplied.value = false;

    if (!promoCode.value.trim()) {
        cartState.value.promoCode = "";
        return;
    }

    const matched = (cartState.value.discounts || []).find(
        (d) =>
            d.coupon_code &&
            d.coupon_code.toLowerCase() ===
                promoCode.value.trim().toLowerCase(),
    );

    if (matched) {
        promoApplied.value = true;
        cartState.value.promoCode = matched.coupon_code;
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
