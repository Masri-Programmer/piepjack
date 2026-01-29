<template>
    <div class="my-3" v-if="!Updating">
        <div class="bg-gray p-24 md:mx-auto">
            <!-- Success State -->
            <div v-if="orderStatus === 'paid'">
                <svg
                    viewBox="0 0 24 24"
                    class="text-green-600 w-16 h-16 mx-auto mb-6"
                >
                    <path
                        fill="currentColor"
                        d="M12,0A12,12,0,1,0,24,12,12.014,12.014,0,0,0,12,0Zm6.927,8.2-6.845,9.289a1.011,1.011,0,0,1-1.43.188L5.764,13.769a1,1,0,1,1,1.25-1.562l4.076,3.261,6.227-8.451A1,1,0,1,1,18.927,8.2Z"
                    />
                </svg>
                <div class="text-center text-accent_dark">
                    <h3
                        class="md:text-3xl text-base text-accent font-semibold text-center"
                    >
                        # {{ orderId }} <br />
                        {{ $t("validation.success.title") }}
                    </h3>
                    <p class="my-2">{{ $t("validation.success.thankYou") }}</p>
                    <p>
                        {{
                            $t("validation.success.checkEmail", {
                                payment: $t("validation.success.order"),
                            })
                        }}
                    </p>
                </div>
            </div>

            <!-- Processing State -->
            <div v-else-if="orderStatus === 'pending'" class="text-center">
                <Spinner />
                <h3 class="text-xl mt-4 font-semibold text-accent">
                    {{ $t("validation.success.processingTitle") }}
                </h3>
                <p class="mt-2 text-gray-600">
                    {{ $t("validation.success.processingDesc") }}
                </p>
            </div>

            <!-- Fallback / Timeout State -->
            <div v-else class="text-center">
                <div v-if="isTimeout">
                    <h3 class="text-xl mt-4 font-semibold text-accent">
                        {{ $t("validation.success.timeoutTitle") }}
                    </h3>
                    <p class="mt-2 text-gray-600">
                        {{ $t("validation.success.timeoutDesc") }}
                    </p>
                    <p class="mt-1">
                        {{ $t("validation.success.timeoutCheckEmail") }}
                    </p>
                </div>
                <Spinner v-else />
            </div>
        </div>
    </div>
    <div v-else><Spinner /></div>

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
import { apiQuery, createApiResource } from "@lib/helpers";
import { cartState } from "@lib/store/shop/index.js";
import { useQuery } from "@tanstack/vue-query";

const route = useRoute();
const orderId = route.query.order_number;
const { data, error, isLoading } = apiQuery("categories").useGet({});

const ordersApi = createApiResource("orders");
const isTimeout = ref(false);

const { data: orderData, isError } = useQuery({
    queryKey: ["orderStatus", orderId],
    queryFn: () => ordersApi.getById(orderId),
    enabled: !!orderId,
    refetchInterval: (data) => {
        if (data?.data?.status === "paid" || isTimeout.value) {
            return false;
        }
        return 3000; // Poll every 3 seconds
    },
    retry: false,
});

const orderStatus = computed(() => orderData.value?.data?.status || "pending");

// Timeout logic: stop polling after 60 seconds if still pending
onMounted(() => {
    if (orderId) {
        cartState.value.cartItems = [];
        setTimeout(() => {
            if (orderStatus.value === "pending") {
                isTimeout.value = true;
            }
        }, 60000);
    }
});

watch(orderStatus, (newStatus) => {
    if (newStatus === "paid") {
        isTimeout.value = false; // Ensure timeout message doesn't show if it eventually pays
    }
});
</script>
