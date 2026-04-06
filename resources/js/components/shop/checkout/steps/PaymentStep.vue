<template>
    <div
        class="space-y-8 animate-in fade-in duration-500 selection:bg-main selection:text-accent"
    >
        <header class="border-b-[6px] border-border pb-4">
            <h1
                class="text-4xl sm:text-5xl font-bold uppercase tracking-tighter italic text-foreground leading-none"
            >
                {{ $t("common.forms.payment") }}
            </h1>
        </header>

        <div
            class="relative overflow-hidden rounded-none border-4 border-border bg-accent_light p-6 sm:p-8 transition-colors duration-300"
            :class="{ 'border-main': isLoading }"
        >
            <div
                :class="{ 'opacity-20 blur-sm pointer-events-none': isLoading }"
                class="transition-all duration-300"
            >
                <div
                    class="flex flex-col sm:flex-row sm:items-center gap-6 mb-8"
                >
                    <div
                        class="w-16 h-16 rounded-none bg-background border-2 border-border flex items-center justify-center shrink-0"
                    >
                        <CreditCard
                            class="w-8 h-8 text-foreground"
                            stroke-width="1.5"
                        />
                    </div>
                    <div>
                        <p
                            class="font-bold text-lg uppercase tracking-widest text-foreground"
                        >
                            {{ $t("common.forms.stripeCheckout") }}
                        </p>
                        <p
                            class="text-sm font-bold uppercase tracking-widest text-muted-foreground mt-1"
                        >
                            {{ $t("common.forms.paymentMethodsInfo") }}
                        </p>
                    </div>
                </div>

                <div class="border-l-4 border-main pl-4">
                    <p
                        class="text-sm font-medium italic text-muted-foreground leading-relaxed"
                    >
                        {{ $t("common.forms.stripeRedirectInfo") }}
                    </p>
                </div>
            </div>

            <div
                v-if="isLoading"
                class="absolute inset-0 z-10 bg-background/90 flex flex-col items-center justify-center m-[-4px] border-4 border-main"
            >
                <Spinner class="w-12 h-12 text-main mb-6" />
                <p
                    class="font-bold uppercase tracking-widest text-sm text-foreground animate-pulse text-center px-4"
                >
                    {{ $t("common.forms.openingSecureCheckout") }}
                </p>
            </div>
        </div>

        <div
            class="flex justify-between items-center pt-8 border-t border-gray-100 gap-4"
        >
            <Button
                @click="$emit('prev')"
                as-child
                variant="ghost"
                :disabled="isLoading"
                class="rounded-none hover:bg-transparent px-0 group"
            >
                <router-link to="/cart" class="flex items-center gap-2">
                    <ChevronLeft
                        :size="20"
                        class="transition-transform group-hover:-translate-x-1"
                    />
                    {{ $t("common.forms.backToBilling") }}
                </router-link>
            </Button>

            <Button
                @click="submitCheckout"
                :disabled="isLoading"
                type="submit"
                class="view-all"
            >
                <template v-if="!isLoading">
                    {{ $t("components.buttons.payNow") }}
                </template>
                <template v-else>
                    {{ $t("common.forms.processing") }}
                </template>
            </Button>
        </div>
    </div>
</template>

<script setup>
import { ChevronLeft, CreditCard } from "lucide-vue-next";
import { checkoutform, cartState } from "@lib/store/shop/index.js";
import { apiQuery } from "@lib/helpers";

import Spinner from "@components/ui/Spinner.vue";
import { Button } from "@/components/ui/button";

const emit = defineEmits(["prev"]);
const { mutate: checkout, isLoading } = apiQuery("checkout").useStore();

const submitCheckout = () => {
    const countryCode =
        checkoutform.value.land?.code || checkoutform.value.land;
    const billingCountryCode =
        checkoutform.value.billing.land?.code ||
        checkoutform.value.billing.land;

    const requestform = {
        email: checkoutform.value.email,
        first_name: checkoutform.value.firstName,
        last_name: checkoutform.value.lastName,
        street_address: checkoutform.value.address,
        city: checkoutform.value.city,
        postal_code: checkoutform.value.zip,
        country_code: countryCode,
        state_province: checkoutform.value.stateProvince,
        shipping_method_id: checkoutform.value.shippingMethodId || 8,

        // Billing Info
        billing_same_as_shipping: checkoutform.value.billingSameAsShipping,
        billing_first_name: checkoutform.value.billingSameAsShipping
            ? checkoutform.value.firstName
            : checkoutform.value.billing.firstName,
        billing_last_name: checkoutform.value.billingSameAsShipping
            ? checkoutform.value.lastName
            : checkoutform.value.billing.lastName,
        billing_street_address: checkoutform.value.billingSameAsShipping
            ? checkoutform.value.address
            : checkoutform.value.billing.address,
        billing_city: checkoutform.value.billingSameAsShipping
            ? checkoutform.value.city
            : checkoutform.value.billing.city,
        billing_postal_code: checkoutform.value.billingSameAsShipping
            ? checkoutform.value.zip
            : checkoutform.value.billing.zip,
        billing_country_code: checkoutform.value.billingSameAsShipping
            ? countryCode
            : billingCountryCode,

        products: transformCartItems(cartState.value.cartItems),
        promo_code: cartState.value.promoCode || null,
    };

    checkout(requestform, {
        onSuccess: (data) => {
            if (data?.url) {
                // Using standard browser API for redirection per constraints
                window.location.href = data.url;
            }
        },
    });
};

const transformCartItems = (cartItems) => {
    return cartItems.flatMap((product) => {
        return product.items.map((item) => ({
            id: item.id,
            quantity: parseInt(item.cartQuantity) || 1,
        }));
    });
};
</script>
