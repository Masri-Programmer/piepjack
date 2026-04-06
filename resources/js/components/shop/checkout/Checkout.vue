<template>
    <div class="min-h-screen bg-background">
        <div
            class="max-w-[1400px] mx-auto grid grid-cols-1 lg:grid-cols-12 min-h-screen"
        >
            <!-- Left Column: Form Content -->
            <div class="lg:col-span-7 px-6 py-12 md:px-12 lg:px-20">
                <div class="max-w-xl mx-auto lg:ml-auto lg:mr-0">
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

                    <!-- Updated Breadcrumbs for multi-step flow -->
                    <Breadcrumbs :data="currentBreadcrumbs" class="mb-12" />

                    <!-- Dynamic Step Components -->
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

                    <!-- Footer inside left column for better flow on desktop -->
                    <div
                        class="mt-24 pt-8 border-t border-gray-100 flex flex-wrap gap-4 text-[10px] uppercase tracking-widest font-bold text-gray-400"
                    >
                        <router-link
                            to="/widerrufsbelehrung"
                            class="hover:text-main transition-colors"
                        >
                            {{ $t("layout.footer.widerrufsrecht") }}
                        </router-link>
                        <router-link
                            to="/shipping"
                            class="hover:text-main transition-colors"
                        >
                            {{ $t("layout.footer.versand") }}
                        </router-link>
                        <router-link
                            to="/datenschutzerklarung"
                            class="hover:text-main transition-colors"
                        >
                            {{ $t("layout.footer.datenschutzerklaerung") }}
                        </router-link>
                        <router-link
                            to="/terms-of-service"
                            class="hover:text-main transition-colors"
                        >
                            {{ $t("layout.footer.agb") }}
                        </router-link>
                        <router-link
                            to="/impressum"
                            class="hover:text-main transition-colors"
                        >
                            {{ $t("layout.footer.impressum") }}
                        </router-link>
                    </div>
                </div>
            </div>

            <!-- Right Column: Cart Summary -->
            <div
                class="lg:col-span-5 bg-accent_light border-l border-muted px-6 py-12 md:px-12 lg:px-20 h-full lg:sticky lg:top-0"
            >
                <div class="max-w-md mx-auto lg:mr-auto lg:ml-0">
                    <CheckoutCart />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, defineAsyncComponent } from "vue";
import "@assets/css/checkout/checkout.css";
import logo from "@img/logo-new.png";
import Breadcrumbs from "../../Breadcrumbs.vue";
import CheckoutCart from "../cart/CheckoutCart.vue";

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

const currentStep = ref(1);
const maxReachedStep = useStorage("checkout-max-step", 1);

const steps = [
    { id: 1, title: "Information", component: ShippingAddressStep },
    { id: 2, title: "Shipping", component: ShippingMethodStep },
    { id: 3, title: "Billing", component: BillingAddressStep },
    { id: 4, title: "Payment", component: PaymentStep },
];

const currentStepComponent = computed(() => {
    return steps.find((s) => s.id === currentStep.value)?.component;
});

const currentBreadcrumbs = computed(() => {
    return [
        { title: "Warenkorb", link: "/cart" },
        ...steps.map((s) => ({
            title: s.title,
            current: s.id === currentStep.value,
            disabled: s.id > maxReachedStep.value,
            // Allow jumping to any step already reached
            action:
                s.id <= maxReachedStep.value && s.id !== currentStep.value
                    ? () => goToStep(s.id)
                    : null,
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
