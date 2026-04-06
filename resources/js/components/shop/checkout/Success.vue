<template>
    <div class="my-3">
        <div class="bg-gray p-24 md:mx-auto">
            <!-- Success State -->
            <div
                v-if="['paid', 'shipped', 'delivered'].includes(orderStatus)"
                class="max-w-4xl mx-auto"
            >
                <div
                    class="bg-background p-8 shadow-sm border border-gray-100 mb-8"
                >
                    <svg
                        viewBox="0 0 24 24"
                        class="text-green-600 w-20 h-20 mx-auto mb-6"
                    >
                        <path
                            fill="currentColor"
                            d="M12,0A12,12,0,1,0,24,12,12.014,12.014,0,0,0,12,0Zm6.927,8.2-6.845,9.289a1.011,1.011,0,0,1-1.43.188L5.764,13.769a1,1,0,1,1,1.25-1.562l4.076,3.261,6.227-8.451A1,1,0,1,1,18.927,8.2Z"
                        />
                    </svg>
                    <div class="text-center text-main">
                        <h3
                            class="md:text-4xl text-2xl text-accent font-bold text-center uppercase mb-4"
                        >
                            {{ $t("validation.success.title") }}
                        </h3>
                        <p class="text-lg font-semibold mb-2">
                            Order #{{ orderReference }}
                        </p>
                        <p class="my-2 text-gray-700">
                            {{ $t("validation.success.thankYou") }}
                        </p>
                        <p
                            class="text-sm text-muted-foreground max-w-md mx-auto"
                        >
                            {{
                                $t("validation.success.checkEmail", {
                                    payment: $t("validation.success.order"),
                                })
                            }}
                        </p>
                    </div>

                    <!-- Order Summary -->
                    <div
                        v-if="orderDetailsData?.data"
                        class="mt-12 pt-8 border-t border-gray-100 animate-fade-in"
                    >
                        <h4
                            class="text-lg font-bold uppercase mb-6 tracking-wider"
                        >
                            Order Summary
                        </h4>
                        <div class="space-y-4 mb-8">
                            <div
                                v-for="product in orderDetailsData.data
                                    .products"
                                :key="product.id"
                                class="flex items-center gap-4"
                            >
                                <div
                                    class="w-16 h-16 bg-accent_light overflow-hidden flex-shrink-0"
                                >
                                    <img
                                        :src="product.image_url"
                                        :alt="product.name"
                                        class="w-full h-full object-cover"
                                    />
                                </div>
                                <div class="flex-1">
                                    <p class="font-bold text-sm">
                                        {{ product.name }}
                                    </p>
                                    <p class="text-xs text-muted-foreground">
                                        Qty: {{ product.item.quantity }} |
                                        {{
                                            product.item.options
                                                .map((o) => o.value)
                                                .join(" / ")
                                        }}
                                    </p>
                                </div>
                                <p class="font-bold text-sm">
                                    {{ product.item.price.toFixed(2) }}
                                    {{ $currency }}
                                </p>
                            </div>
                        </div>
                        <div class="bg-accent_light p-6 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span>Subtotal</span>
                                <span
                                    >{{
                                        orderDetailsData.data.total_price.toFixed(
                                            2,
                                        )
                                    }}
                                    {{ $currency }}</span
                                >
                            </div>
                            <div
                                class="flex justify-between font-bold text-lg pt-2 border-t border-muted"
                            >
                                <span>Total Paid</span>
                                <span
                                    >{{
                                        orderDetailsData.data.total_price.toFixed(
                                            2,
                                        )
                                    }}
                                    {{ $currency }}</span
                                >
                            </div>
                        </div>
                    </div>

                    <div
                        class="flex flex-col sm:flex-row gap-4 justify-center mt-12"
                    >
                        <Button
                            as-child
                            class="view-all bg-primary text-primary-foreground px-8 py-4 text-xs font-bold uppercase tracking-widest text-center h-auto"
                        >
                            <router-link to="/shop">
                                Continue Shopping
                            </router-link>
                        </Button>
                        <Button
                            as-child
                            variant="outline"
                            class="view-all bg-background border border-accent_dark text-main px-8 py-4 text-xs font-bold uppercase tracking-widest text-center h-auto"
                        >
                            <router-link :to="'/track-order/' + orderReference">
                                Track Order
                            </router-link>
                        </Button>
                    </div>
                </div>
            </div>

            <!-- Processing State -->
            <div
                v-else-if="orderStatus === 'pending'"
                class="text-center py-20"
            >
                <div class="relative w-24 h-24 mx-auto mb-8">
                    <Spinner class="w-full h-full" />
                </div>
                <h3
                    class="text-2xl font-bold text-accent uppercase tracking-wider"
                >
                    {{ $t("validation.success.processingTitle") }}
                </h3>
                <p class="mt-4 text-gray-600 max-w-sm mx-auto leading-relaxed">
                    {{ $t("validation.success.processingDesc") }}
                </p>
            </div>

            <!-- Fallback / Timeout State -->
            <div v-else class="text-center py-20">
                <div v-if="isTimeout" class="max-w-md mx-auto">
                    <div class="text-orange-500 mb-6">
                        <svg
                            class="w-16 h-16 mx-auto"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-accent uppercase mb-4">
                        {{ $t("validation.success.timeoutTitle") }}
                    </h3>
                    <p class="text-gray-600 mb-4">
                        {{ $t("validation.success.timeoutDesc") }}
                    </p>
                    <p class="text-sm font-medium text-muted-foreground italic">
                        {{ $t("validation.success.timeoutCheckEmail") }}
                    </p>
                    <Button
                        as-child
                        class="mt-8 inline-block view-all bg-primary text-primary-foreground px-8 py-3 text-xs uppercase font-bold tracking-widest h-auto"
                    >
                        <router-link to="/shop"> Return to Shop </router-link>
                    </Button>
                </div>
                <Spinner class="grid justify-center" v-else />
            </div>
        </div>
    </div>

    <template v-if="data?.data">
        <template v-for="c in data.data">
            <HomeCarousel
                v-if="c.promoted"
                :name="c.name"
                :slug="c.slug"
                :id="c.id"
                :title="c.name"
            />
        </template>
    </template>
</template>

<script setup>
import { onMounted, ref, computed, watch } from "vue";
import { useRoute } from "vue-router";
import Spinner from "@components/ui/Spinner.vue";
import HomeCarousel from "@components/shop/home/HomeCarousel.vue";
import { apiQuery, apiRequest } from "@lib/helpers";
import { cartState } from "@lib/store/shop/index.js";
import { useQuery } from "@tanstack/vue-query";
import { Button } from "@/components/ui/button";

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
        // Stop polling if order is paid or we timed out
        if (data?.status === "paid" || isTimeout.value) {
            return false;
        }
        return 3000; // Poll every 3 seconds
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
            (orderStatus.value === "paid" || orderStatus.value === "shipped") &&
            !!orderReference.value &&
            !!userEmail,
    ),
});

onMounted(() => {
    if (cartId) {
        // Clear cart and progress immediately upon landing on success page
        cartState.value.cartItems = [];
        localStorage.removeItem("checkout-max-step");

        // Set a 60-second timeout for processing
        setTimeout(() => {
            if (orderStatus.value === "pending") {
                isTimeout.value = true;
            }
        }, 60000);
    }
});

watch(orderStatus, (newStatus) => {
    if (newStatus === "paid") {
        isTimeout.value = false;
    }
});
</script>
