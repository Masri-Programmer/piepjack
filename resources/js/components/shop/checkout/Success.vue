<template>
    <PageLayout :title="pageTitle" :description="pageDescription">
        <template #header>
            <div
                class="border-b-12 border-border pb-8 text-center md:text-left"
            >
                <p
                    class="mb-4 text-xs font-bold uppercase tracking-[0.3em] text-muted-foreground"
                >
                    {{ $t("validation.success.order") }}
                    {{ $t("validation.success.status") }}
                </p>
                <h1
                    class="text-6xl sm:text-7xl font-bold uppercase tracking-tighter leading-none italic text-foreground"
                >
                    {{ pageTitle }}
                </h1>
            </div>
        </template>

        <div
            v-if="
                [
                    'paid',
                    'payment-received',
                    'dispatched',
                    'requires-capture',
                ].includes(orderStatus)
            "
            class="space-y-12 animate-in fade-in duration-700"
        >
            <div
                class="bg-main text-accent p-8 sm:p-12 border-4 border-main flex flex-col items-center text-center"
            >
                <svg
                    viewBox="0 0 24 24"
                    class="w-24 h-24 mb-8 text-accent animate-in zoom-in duration-500 delay-200"
                >
                    <path
                        fill="currentColor"
                        d="M12,0A12,12,0,1,0,24,12,12.014,12.014,0,0,0,12,0Zm6.927,8.2-6.845,9.289a1.011,1.011,0,0,1-1.43.188L5.764,13.769a1,1,0,1,1,1.25-1.562l4.076,3.261,6.227-8.451A1,1,0,1,1,18.927,8.2Z"
                    />
                </svg>
                <p class="text-2xl font-bold uppercase tracking-widest mb-2">
                    {{ $t("validation.success.confirmed") }}
                </p>
                <p class="text-4xl font-mono font-bold mb-6">
                    #{{ orderReference }}
                </p>

                <!-- Pickup Information -->
                <div
                    v-if="orderDetailsData?.data?.shipping_lines?.some(line => line.identifier === 'PICKUP')"
                    class="mb-6 bg-accent border-2 border-main p-6 w-full max-w-lg text-main animate-in slide-in-from-top-4 duration-500"
                >
                    <p class="text-lg font-bold uppercase tracking-widest mb-2">
                        {{ $t("pages.checkout.pickupSuccess", { address: $t("pages.store.address") }) }}
                    </p>
                    <p class="text-sm font-medium opacity-80">
                        {{ $t("validation.success.checkEmailPickup", "You will receive an email as soon as your order is ready for collection.") }}
                    </p>
                </div>

                <div
                    v-if="orderDetailsData?.data?.tracking_number"
                    class="mb-6 bg-background border-2 border-border p-4 w-full max-w-sm"
                >
                    <p
                        class="text-sm font-bold uppercase tracking-widest text-muted-foreground mb-1"
                    >
                        {{
                            $t(
                                "validation.success.trackingNumber",
                                "Tracking Number",
                            )
                        }}
                    </p>
                    <a
                        v-if="orderDetailsData?.data?.label_url"
                        :href="orderDetailsData.data.label_url"
                        target="_blank"
                        class="text-xl font-mono font-bold text-main hover:underline"
                    >
                        {{ orderDetailsData.data.tracking_number }}
                    </a>
                    <p v-else class="text-xl font-mono font-bold">
                        {{ orderDetailsData.data.tracking_number }}
                    </p>
                </div>
                <p class="text-lg font-medium opacity-80 mb-2">
                    {{ $t("validation.success.thankYou") }}
                </p>
                <p
                    class="text-sm font-bold uppercase tracking-widest opacity-60"
                >
                    {{
                        $t("validation.success.checkEmail", {
                            payment: $t("validation.success.order"),
                        })
                    }}
                </p>
            </div>

            <div
                v-if="orderDetailsData?.data"
                class="border-2 border-border bg-background animate-in slide-in-from-bottom-4 duration-500 delay-300"
            >
                <div class="p-6 border-b-2 border-border bg-muted">
                    <h4
                        class="text-lg font-bold uppercase tracking-widest text-foreground"
                    >
                        {{ $t("validation.success.summary") }}
                    </h4>
                </div>

                <div class="divide-y-2 divide-border">
                    <div
                        v-for="product in orderDetailsData.data.products"
                        :key="product.id"
                        class="p-6 flex flex-col sm:flex-row items-start sm:items-center gap-6"
                    >
                        <div
                            class="w-20 sm:w-24 aspect-square bg-accent_light border-2 border-border shrink-0 overflow-hidden"
                        >
                            <img
                                :src="product.image_url"
                                :alt="product.name"
                                class="w-full h-full object-cover grayscale-20"
                            />
                        </div>
                        <div class="flex-1">
                            <p
                                class="font-bold text-lg uppercase tracking-tight"
                            >
                                {{ product.name }}
                            </p>
                            <p
                                class="text-sm font-bold uppercase tracking-widest text-muted-foreground mt-1"
                            >
                                {{ $t("validation.success.qty") }}:
                                {{ product.item.quantity }} |
                                {{
                                    product.item.options
                                        .map((o) => o.value)
                                        .join(" / ")
                                }}
                            </p>
                        </div>
                        <p class="font-bold font-mono text-xl">
                            {{ product.item.price.toFixed(2) }}
                            {{ $currency }}
                        </p>
                    </div>
                </div>

                <div
                    class="p-6 bg-accent_light border-t-4 border-border space-y-4"
                >
                    <div
                        class="flex justify-between items-center text-sm font-bold uppercase tracking-widest text-muted-foreground"
                    >
                        <span>{{ $t("validation.success.subtotal") }}</span>
                        <span class="font-mono text-foreground"
                            >{{ orderDetailsData.data.total_price.toFixed(2) }}
                            {{ $currency }}</span
                        >
                    </div>
                    <div
                        class="flex justify-between items-center text-2xl font-bold uppercase italic border-t-2 border-border pt-4"
                    >
                        <span>{{ $t("validation.success.totalPaid") }}</span>
                        <span class="font-mono"
                            >{{ orderDetailsData.data.total_price.toFixed(2) }}
                            {{ $currency }}</span
                        >
                    </div>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-4 pt-8">
                <Button as-child variant="outline" class="view-all">
                    <router-link :to="'/track-order/' + orderReference">
                        {{ $t("validation.success.trackOrder") }}
                    </router-link>
                </Button>
            </div>
        </div>

        <div
            v-else-if="paymentStateDetails"
            class="space-y-8 animate-in fade-in duration-700 max-w-2xl mx-auto"
        >
            <div
                :class="[
                    'p-8 sm:p-12 border-4 flex flex-col items-center text-center',
                    paymentStateDetails.colorClass,
                ]"
            >
                <svg
                    class="w-20 h-20 mb-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        :d="paymentStateDetails.icon"
                    />
                </svg>
                <p class="text-2xl font-bold uppercase tracking-widest mb-2">
                    {{ paymentStateDetails.title }}
                </p>
                <p class="text-lg font-medium opacity-80 mb-6 text-foreground">
                    {{ paymentStateDetails.description }}
                </p>
                <Button
                    as-child
                    :class="['view-all', paymentStateDetails.buttonClass]"
                >
                    <router-link :to="paymentStateDetails.buttonLink">
                        {{ paymentStateDetails.buttonText }}
                    </router-link>
                </Button>
            </div>
        </div>

        <div v-else class="text-center py-32 border-4 border-border">
            <div
                v-if="isTimeout"
                class="max-w-lg mx-auto p-8 border-2 border-destructive bg-destructive/10"
            >
                <h3
                    class="text-2xl font-bold text-destructive uppercase tracking-widest mb-4"
                >
                    {{ $t("validation.success.timeoutTitle") }}
                </h3>
                <p class="text-base font-medium text-foreground mb-4">
                    {{ $t("validation.success.timeoutDesc") }}
                </p>
                <p
                    class="text-sm font-bold uppercase tracking-widest text-muted-foreground mb-8"
                >
                    {{ $t("validation.success.timeoutCheckEmail") }}
                </p>
                <Button as-child class="view-all">
                    <router-link to="/shop">
                        {{ $t("validation.success.returnToShop") }}
                    </router-link>
                </Button>
            </div>
            <div v-else class="space-y-6">
                <Spinner class="w-16 h-16 mx-auto text-main" />
                <p
                    class="text-sm font-bold uppercase tracking-widest text-muted-foreground animate-pulse"
                >
                    {{ $t("validation.success.waitProcessing") }}
                </p>
            </div>
        </div>

        <div
            v-if="data?.data"
            class="mt-32 pt-16 border-t-4 border-border space-y-24"
        >
            <template v-for="c in data.data" :key="c.id">
                <HomeCarousel
                    v-if="c.promoted"
                    :name="c.name"
                    :slug="c.slug"
                    :id="c.id"
                    :title="c.name"
                />
            </template>
        </div>
    </PageLayout>
</template>

<script setup>
import { onMounted, ref, computed, watch } from "vue";
import { useRoute } from "vue-router";
import { useQuery } from "@tanstack/vue-query";
import { useI18n } from "vue-i18n";

import PageLayout from "@components/shop/general/PageLayout.vue";
import Spinner from "@components/ui/Spinner.vue";
import HomeCarousel from "@components/shop/home/HomeCarousel.vue";
import { Button } from "@/components/ui/button";

import { apiQuery, apiRequest } from "@lib/helpers";
import { cartState } from "@lib/store/shop/index.js";

const { t } = useI18n();
const route = useRoute();
const cartId = route.query.cart_id;
const userEmail = route.query.email;
const { data, isLoading } = apiQuery("categories").useGet({});

const isTimeout = ref(false);

const { data: orderData } = useQuery({
    queryKey: ["orderLookup", cartId, userEmail],
    queryFn: () =>
        apiRequest("get", `/order-lookup/${cartId}`, {}, { email: userEmail }),
    enabled: !!cartId && !!userEmail,
    refetchInterval: (data) => {
        const terminalStates = [
            "paid",
            "payment-received",
            "failed",
            "cancelled",
            "requires-capture",
            "processing",
        ];

        if (data && terminalStates.includes(data.status)) return false;
        if (isTimeout.value) return false;

        return 3000;
    },
    retry: false,
});

const orderStatus = computed(() => orderData.value?.status || "pending");
const orderReference = computed(() => orderData.value?.reference || "");

const pageTitle = computed(() => {
    if (paymentStateDetails.value) {
        return paymentStateDetails.value.title;
    }
    if (isTimeout.value) {
        return t("validation.success.timeoutTitle");
    }

    const terminalSuccessStates = [
        "paid",
        "payment-received",
        "dispatched",
        "requires-capture",
    ];

    if (!terminalSuccessStates.includes(orderStatus.value)) {
        return t("validation.success.order");
    }

    return t("validation.success.title");
});

const pageDescription = computed(() => {
    if (paymentStateDetails.value) {
        return paymentStateDetails.value.description;
    }
    if (isTimeout.value) {
        return t("validation.success.timeoutDesc");
    }

    const terminalSuccessStates = [
        "paid",
        "payment-received",
        "dispatched",
        "requires-capture",
    ];

    if (!terminalSuccessStates.includes(orderStatus.value)) {
        return t("validation.success.waitProcessing");
    }

    return t("validation.success.thankYou");
});

const { data: orderDetailsData } = useQuery({
    queryKey: ["orderDetails", orderReference, userEmail],
    queryFn: () =>
        apiRequest(
            "get",
            `/shop/orders/${orderReference.value}`,
            {},
            { email: userEmail },
        ),
    enabled: computed(
        () =>
            (orderStatus.value === "paid" ||
                orderStatus.value === "payment-received" ||
                orderStatus.value === "shipped" ||
                orderStatus.value === "dispatched" ||
                orderStatus.value === "requires-capture") &&
            !!orderReference.value &&
            !!userEmail,
    ),
});

onMounted(() => {
    if (cartId) {
        cartState.value.cartItems = [];
        localStorage.removeItem("checkout-max-step");

        setTimeout(() => {
            if (orderStatus.value === "pending") isTimeout.value = true;
        }, 60000);
    }
});

watch(orderStatus, (newStatus) => {
    if (newStatus !== "pending") {
        isTimeout.value = false;
    }
});

const paymentStateDetails = computed(() => {
    switch (orderStatus.value) {
        case "failed":
            return {
                title: t("validation.success.issue"),
                description: t("validation.success.issueDesc"),
                buttonText: t("validation.success.returnToCheckout"),
                buttonLink: "/checkout",
                icon: "M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z",
                colorClass:
                    "text-destructive border-destructive bg-destructive/10",
                buttonClass:
                    "bg-destructive hover:bg-destructive/90 text-destructive-foreground",
            };
        case "cancelled":
            return {
                title: t("validation.success.issue"),
                description: t("validation.success.issueDesc"),
                buttonText: t("validation.success.returnToCheckout"),
                buttonLink: "/checkout",
                icon: "M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z",
                colorClass: "text-muted-foreground border-border bg-muted/50",
                buttonClass:
                    "bg-primary hover:bg-primary/90 text-primary-foreground",
            };
        case "awaiting-payment":
            return {
                title: t("validation.success.pendingTitle"),
                description: t("validation.success.pendingDesc"),
                buttonText: t("validation.success.returnToShop"),
                buttonLink: "/shop",
                icon: "M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z",
                colorClass:
                    "text-orange-500 border-orange-500 bg-orange-500/10",
                buttonClass: "bg-orange-500 hover:bg-orange-600 text-white",
            };
        case "requires-capture":
            return {
                title: t("validation.success.authorizedTitle"),
                description: t("validation.success.authorizedDesc"),
                buttonText: t("validation.success.trackOrder"),
                buttonLink: "/track-order/" + orderReference.value,
                icon: "M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z",
                colorClass: "text-green-600 border-green-600 bg-green-600/10",
                buttonClass: "bg-green-600 hover:bg-green-700 text-white",
            };
        case "processing":
            return {
                title: t("validation.success.processingTitle"),
                description: t("validation.success.processingDesc"),
                buttonText: t("validation.success.returnToShop"),
                buttonLink: "/shop",
                icon: "M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z",
                colorClass: "text-blue-500 border-blue-500 bg-blue-500/10",
                buttonClass: "bg-blue-500 hover:bg-blue-600 text-white",
            };
        default:
            return null;
    }
});
</script>
