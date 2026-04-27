<template>
    <div class="min-h-screen bg-background">
        <div
            class="max-w-[1400px] mx-auto grid grid-cols-1 lg:grid-cols-12 min-h-screen gap-6 md:gap-8 lg:gap-12 xl:gap-16"
        >
            <div
                class="lg:col-span-7 flex flex-col pt-8 lg:pt-12 px-4 sm:px-6 lg:px-8"
            >
                <div class="w-full max-w-xl mx-auto lg:ml-auto lg:mr-0 grow">
                    <router-link
                        to="/"
                        class="inline-block mb-12 w-full mx-auto"
                    >
                        <img
                            :src="logo"
                            alt="logo"
                            class="w-24 h-auto mx-auto"
                        />
                    </router-link>

                    <Breadcrumbs :data="currentBreadcrumbs" class="mb-12" />

                    <div class="relative">
                        <transition name="fade" mode="out-in">
                            <component
                                :is="currentStepComponent"
                                @next="nextStep"
                                @prev="prevStep"
                                class="w-full"
                            />
                        </transition>
                    </div>

                    <div
                        class="hidden lg:flex mt-24 pt-8 border-t border-border flex-wrap gap-4 text-[10px] uppercase tracking-widest font-bold text-muted-foreground"
                    >
                        <router-link
                            v-for="link in footerLinks"
                            :key="link.to"
                            :to="link.to"
                            class="hover:text-primary transition-colors"
                        >
                            {{ $t(link.label) }}
                        </router-link>
                    </div>
                </div>
            </div>

            <div
                class="lg:col-span-5 bg-accent-shadcn lg:border-l border-border h-full lg:sticky lg:top-0"
            >
                <div class="w-full max-w-xl mx-auto lg:ml-auto lg:mr-0 grow">
                    <CheckoutCart
                        class="lg:col-span-7 flex flex-col lg:pt-12 lg:px-8"
                    />
                </div>
            </div>

            <div
                class="flex lg:hidden justify-center items-center pb-12 pt-8 px-4 border-t border-border flex-wrap gap-4 text-[10px] uppercase tracking-widest font-bold text-muted-foreground"
            >
                <router-link
                    v-for="link in footerLinks"
                    :key="link.to"
                    :to="link.to"
                    class="hover:text-primary transition-colors"
                >
                    {{ $t(link.label) }}
                </router-link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, defineAsyncComponent, onMounted } from "vue";
import { useI18n } from "vue-i18n";
import "@assets/css/checkout/checkout.css";
import logo from "@img/logo-new.png";
import Breadcrumbs from "../../Breadcrumbs.vue";
import CheckoutCart from "../cart/CheckoutCart.vue";
import { validateCart } from "@lib/store/shop/index.js";

import { useStorage } from "@vueuse/core";

// Steps based on Lunar flow
const ShippingAddressStep = defineAsyncComponent(
    () => import("./steps/ShippingAddressStep.vue"),
);
const ShippingMethodStep = defineAsyncComponent(
    () => import("./steps/ShippingMethodStep.vue"),
);
const BillingAddressStep = defineAsyncComponent(
    () => import("./steps/BillingAddressStep.vue"),
);
const PaymentStep = defineAsyncComponent(
    () => import("./steps/PaymentStep.vue"),
);

onMounted(async () => {
    await validateCart();
    // Reset progress on fresh entry to ensure validation flow
    maxReachedStep.value = 1;
});

const currentStep = ref(1);
const maxReachedStep = useStorage("checkout-max-step", 1);

const steps = [
    {
        id: 1,
        title: "common.forms.information",
        component: ShippingAddressStep,
    },
    { id: 2, title: "common.forms.shipping", component: ShippingMethodStep },
    { id: 3, title: "common.forms.billing", component: BillingAddressStep },
    { id: 4, title: "common.forms.payment", component: PaymentStep },
];

// Extracted footer links to keep the template DRY
const footerLinks = [
    { to: "/widerrufsbelehrung", label: "layout.footer.widerrufsrecht" },
    { to: "/shipping", label: "layout.footer.versand" },
    {
        to: "/datenschutzerklarung",
        label: "layout.footer.datenschutzerklaerung",
    },
    { to: "/terms-of-service", label: "layout.footer.agb" },
    { to: "/impressum", label: "layout.footer.impressum" },
];

const currentStepComponent = computed(() => {
    return steps.find((s) => s.id === currentStep.value)?.component;
});

const { t } = useI18n();

const currentBreadcrumbs = computed(() => {
    return [
        { title: t("common.forms.backToCart") || "Warenkorb", link: "/cart" },
        ...steps.map((s) => ({
            title: t(s.title) || s.title,
            current: s.id === currentStep.value,
            disabled: s.id > maxReachedStep.value,
            action: s.id <= maxReachedStep.value ? () => goToStep(s.id) : null,
        })),
    ];
});

const goToStep = (stepId) => {
    if (stepId <= maxReachedStep.value) {
        currentStep.value = stepId;
        window.scrollTo({ top: 0, behavior: "smooth" });
    }
};

const nextStep = () => {
    if (currentStep.value < steps.length) {
        currentStep.value++;
        if (currentStep.value > maxReachedStep.value) {
            maxReachedStep.value = currentStep.value;
        }
        window.scrollTo({ top: 0, behavior: "smooth" });
    }
};

const prevStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--;
        window.scrollTo({ top: 0, behavior: "smooth" });
    }
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
