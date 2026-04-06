<template>
    <div class="contact-form">
        <h1 class="contact-form-title">{{ $t("common.forms.payment") }}</h1>

        <div class="space-y-4 mb-8">
            <div class="p-6 border border-accent_dark bg-accent_light">
                <div class="flex items-center gap-4 mb-4">
                    <div
                        class="w-12 h-12 bg-background border border-muted grid place-content-center"
                    >
                        <CreditCard size="24" strokeWidth="1" />
                    </div>
                    <div>
                        <p class="font-bold text-sm">
                            Stripe (Kreditkarte / PayPal / Klarna)
                        </p>
                        <p class="text-xs text-muted-foreground">
                            Sicher und schnell bezahlen
                        </p>
                    </div>
                </div>
                <p class="text-xs text-gray-600 italic">
                    Nach dem Klicken auf "Jetzt bezahlen" werden Sie zu Stripe
                    weitergeleitet, um Ihren Kauf sicher abzuschließen.
                </p>
            </div>
        </div>

        <div class="contact-form-footer mt-8">
            <Button
                @click="$emit('prev')"
                variant="ghost"
                class="contact-form-back-link"
            >
                <ChevronLeft size="20" />{{ $t("common.forms.backToBilling") }}
            </Button>
            <Button
                @click="submitCheckout"
                :disabled="isLoading"
                class="contact-form-submit view-all h-auto"
            >
                <template v-if="!isLoading">
                    {{ $t("components.buttons.payNow") }}
                </template>
                <Spinner v-else class="h-5 w-5" />
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
import "@assets/css/checkout/contactFrom.css";

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
