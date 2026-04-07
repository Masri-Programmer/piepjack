<template>
    <div
        class="min-h-screen py-16 sm:py-24 selection:bg-main selection:text-accent"
    >
        <div class="container mx-auto px-6">
            <header
                class="mb-12 border-b-[12px] border-border pb-8 text-center md:text-left"
            >
                <p
                    class="mb-4 text-xs font-bold uppercase tracking-[0.3em] text-muted-foreground"
                >
                    {{ $t("validation.success.order") }} Status
                </p>
                <h1
                    class="text-6xl sm:text-7xl font-bold uppercase tracking-tighter leading-none italic text-foreground"
                >
                    {{ $t("validation.success.title") }}
                </h1>
            </header>

            <div
                v-if="
                    [
                        'paid',
                        'payment-received',
                        'shipped',
                        'delivered',
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
                    <p
                        class="text-2xl font-bold uppercase tracking-widest mb-2"
                    >
                        Order Confirmed
                    </p>
                    <p class="text-4xl font-mono font-bold mb-6">
                        #{{ orderReference }}
                    </p>
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
                            Order Summary
                        </h4>
                    </div>

                    <div class="divide-y-2 divide-border">
                        <div
                            v-for="product in orderDetailsData.data.products"
                            :key="product.id"
                            class="p-6 flex flex-col sm:flex-row items-start sm:items-center gap-6"
                        >
                            <div
                                class="w-24 h-24 bg-accent_light border-2 border-border flex-shrink-0"
                            >
                                <img
                                    :src="product.image_url"
                                    :alt="product.name"
                                    class="w-full h-full object-cover grayscale-[20%]"
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
                                    Qty: {{ product.item.quantity }} |
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
                            <span>Subtotal</span>
                            <span class="font-mono text-foreground"
                                >{{
                                    orderDetailsData.data.total_price.toFixed(2)
                                }}
                                {{ $currency }}</span
                            >
                        </div>
                        <div
                            class="flex justify-between items-center text-2xl font-bold uppercase italic border-t-2 border-border pt-4"
                        >
                            <span>Total Paid</span>
                            <span class="font-mono"
                                >{{
                                    orderDetailsData.data.total_price.toFixed(2)
                                }}
                                {{ $currency }}</span
                            >
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 pt-8">
                    <!-- <Button
                        as-child
                        class="view-all flex-1 h-16 text-sm font-bold uppercase tracking-widest"
                    >
                        <router-link to="/shop">Continue Shopping</router-link>
                    </Button> -->
                    <Button
                        as-child
                        variant="outline"
                        class="flex-1 h-16 border-2 border-main text-sm font-bold uppercase tracking-widest rounded-none hover:bg-main hover:text-accent transition-colors"
                    >
                        <router-link :to="'/track-order/' + orderReference"
                            >Track Order</router-link
                        >
                    </Button>
                </div>
            </div>

            <div
                v-else-if="orderStatus === 'pending' && !isTimeout"
                class="text-center py-32 border-4 border-border animate-pulse"
            >
                <div class="relative w-24 h-24 mx-auto mb-8 text-main">
                    <Spinner class="w-full h-full" />
                </div>
                <h3
                    class="text-3xl font-bold text-foreground uppercase tracking-tighter italic mb-4"
                >
                    {{ $t("validation.success.processingTitle") }}
                </h3>
                <p
                    class="text-sm font-bold uppercase tracking-widest text-muted-foreground max-w-md mx-auto"
                >
                    {{ $t("validation.success.processingDesc") }}
                </p>
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
                    <Button
                        as-child
                        class="view-all w-full h-16 text-sm font-bold uppercase tracking-widest"
                    >
                        <router-link to="/shop">Return to Shop</router-link>
                    </Button>
                </div>
                <Spinner v-else class="w-16 h-16 mx-auto text-main" />
            </div>

            <div
                v-if="data?.data"
                class="mt-32 pt-16 border-t-4 border-border grid gap-16"
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
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref, computed, watch } from "vue";
import { useRoute } from "vue-router";
import { useQuery } from "@tanstack/vue-query";

import Spinner from "@components/ui/Spinner.vue";
import HomeCarousel from "@components/shop/home/HomeCarousel.vue";
import { Button } from "@/components/ui/button";

import { apiQuery, apiRequest } from "@lib/helpers";
import { cartState } from "@lib/store/shop/index.js";

const route = useRoute();
const cartId = route.query.cart_id;
const userEmail = route.query.email;
const { data, error, isLoading } = apiQuery("categories").useGet({});

const isTimeout = ref(false);

const { data: orderData, isError } = useQuery({
    queryKey: ["orderLookup", cartId, userEmail],
    queryFn: () =>
        apiRequest("get", `/order-lookup/${cartId}`, {}, { email: userEmail }),
    enabled: !!cartId && !!userEmail,
    refetchInterval: (data) => {
        if (
            data?.status === "paid" ||
            data?.status === "payment-received" ||
            isTimeout.value
        )
            return false;
        return 3000;
    },
    retry: false,
});

const orderStatus = computed(() => orderData.value?.status || "pending");
const orderReference = computed(() => orderData.value?.reference || "");

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
                orderStatus.value === "shipped") &&
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
    if (newStatus === "paid" || newStatus === "payment-received")
        isTimeout.value = false;
});
</script>
