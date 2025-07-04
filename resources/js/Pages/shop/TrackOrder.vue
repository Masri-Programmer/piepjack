<template>
    <div class="grid justify-center place-content-center justify-items-center">
        <div class="relative p-4 trackOrder min-w-48">
            <!-- Main Content -->
            <div class="tracking-container">
                <h1 class="title">{{ $t("pages.tracking.title") }}</h1>

                <!-- Step 1: Form -->
                <form
                    v-if="activeStep === 1"
                    class="tracking-form"
                    @submit.prevent="handleSubmit"
                    aria-label="Track Order Form"
                >
                    <input
                        v-model="form.email"
                        class="input-field"
                        type="email"
                        :placeholder="$t('validation.form.email')"
                        required
                        aria-label="Email Address"
                    />
                    <input
                        v-model="form.orderNr"
                        class="input-field"
                        type="text"
                        :placeholder="$t('validation.form.orderNumber')"
                        required
                        aria-label="Order Number"
                    />
                    <button
                        type="submit"
                        class="submit-button"
                        :disabled="isLoadingOrder"
                        aria-label="Track Order Button"
                    >
                        <Spinner v-if="isLoadingOrder" center size="xs" />
                        <span v-else>{{ $t("components.buttons.track") }}</span>
                    </button>
                </form>
                <!-- Step 2: Order Details -->
                <div
                    v-if="
                        activeStep === 2 &&
                        !isLoadingOrder &&
                        order?.data?.products
                    "
                    class="order-details"
                >
                    <h2 class="subtitle">
                        {{ $t("pages.tracking.orderDetails") }}
                    </h2>
                    <div class="details-grid">
                        <p>{{ $t("pages.tracking.status") }}:</p>
                        <p class="font-semibold">
                            {{ order?.data.status || $t("common.unknown") }}
                        </p>
                        <p>{{ $t("pages.tracking.date") }}:</p>
                        <p class="font-semibold">
                            {{ formatDate(order?.data.created_at) }}
                        </p>
                        <p>{{ $t("pages.tracking.orderNr") }}:</p>
                        <p class="font-semibold">
                            {{ order?.data.order_number }}
                        </p>
                    </div>
                    <hr />
                    <div class="products-list">
                        <ProductSmallCard
                            v-for="product in order?.data.products"
                            :key="product.item"
                            :product="product"
                            :item="product.item"
                        />
                    </div>
                    <hr />
                    <button @click="goBack" class="back-button">
                        {{ $t("components.buttons.back") }}
                    </button>
                </div>

                <!-- Error Message -->
                <div v-if="errorMessage" class="error-message">
                    {{ errorMessage }}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useI18n } from "vue-i18n";
import { useRoute } from "vue-router";
import Spinner from "@components/ui/Spinner.vue";
import { useToast } from "vue-toastification";
import { ref, computed, onMounted, watch } from "vue";
import { useSessionStorage } from "@vueuse/core";
import ProductSmallCard from "@components/shop/product/ProductSmallCard.vue";
import { apiQuery } from "@lib/helpers";
import { checkoutform } from "@lib/store/shop/index.js";

// Internationalization
const { t } = useI18n();
const toast = useToast();
const route = useRoute();
const slug = route.params.slug;

// Reactive State
const activeStep = ref(1);
const form = useSessionStorage("active-track-form", {
    email: checkoutform.value.email || "",
    orderNr: "",
});
form.value.orderNr = route.params.id ? route.params.id : form.value.orderNr;

// API Query
const orderQueryKey = computed(() => form.value.orderNr);
const {
    data: order,
    isLoading: isLoadingOrder,
    refetch: refetchOrder,
} = apiQuery("orders").useGetById(orderQueryKey);

// Error Handling
const errorMessage = ref("");

// Helper Functions
const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString(undefined, {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

// Fetch Order
const fetchOrder = async () => {
    try {
        const { data } = await refetchOrder();
        if (!data) {
            throw new Error(t("common.alerts.orderNotFound"));
        }
        activeStep.value = 2;
    } catch (error) {
        errorMessage.value = error.message;
        console.error("Failed to fetch order:", error);
    }
};

// Handle Form Submission
const handleSubmit = async () => {
    if (!form.value.email || !form.value.orderNr) {
        errorMessage.value = t("common.alerts.fillAllFields");
        return;
    }
    errorMessage.value = ""; // Clear previous errors
    orderQueryKey.value = form.value.orderNr;
    await fetchOrder();
};

// Go Back to Previous Step
const goBack = () => {
    activeStep.value = Math.max(activeStep.value - 1, 1); // Prevent negative steps
};

// Lifecycle Hooks
onMounted(() => {
    if (slug === "success") {
        activeStep.value = 1;
        toast.success(t("common.alerts.trackSuccess"));
    }

    // Auto-fetch order if form data exists
    if (form.value.email && form.value.orderNr) {
        handleSubmit();
    }
});

// Watch for Route Changes
watch(
    () => route.params.id,
    (newId) => {
        if (newId) {
            form.value.orderNr = newId;
            handleSubmit();
        }
    }
);
</script>

<style scoped>
/* Add your styles here */
.error-message {
    color: red;
    margin-top: 1rem;
}
</style>
