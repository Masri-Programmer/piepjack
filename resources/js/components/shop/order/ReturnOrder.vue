<template>
    <div class="return-order-container">
        <!-- Title -->
        <h1 class="return-order-title">
            <button
                v-if="activeStep > 1"
                @click="goBack()"
                aria-label="Go Back"
                class="return-order-back-button"
            >
                <ChevronLeft size="30" />
            </button>
            {{ $t("common.titles.return_exchange") }}
        </h1>

        <!-- Step 1: Form -->
        <form
            v-if="activeStep === 1"
            @submit.prevent="submitForm"
            class="return-order-form"
            aria-label="Return Order Form"
        >
            <input
                v-model="form.email"
                class="return-order-input"
                id="email"
                type="email"
                :placeholder="$t('common.input.email')"
                required
                aria-label="Email Address"
            />
            <input
                v-model="form.orderNr"
                class="return-order-input"
                id="orderNr"
                type="text"
                :placeholder="$t('common.input.orderTracking')"
                required
                aria-label="Order Number"
            />
            <button
                type="submit"
                :disabled="isLoadingOrder"
                class="return-order-button-submit view-all"
            >
                <Spinner v-if="isLoadingOrder" center size="xs" />
                <span v-else>{{ $t("components.buttons.returnOrder") }}</span>
            </button>
        </form>

        <!-- Step 2: Product Selection -->
        <div
            v-if="activeStep === 2 && !isLoadingOrder && order?.data.products"
            class="return-order-products"
        >
            <div class="return-order-grid">
                <div
                    v-for="product in order?.data.products"
                    :key="product.item"
                    class="return-order-product-card"
                    :class="{ 'is-selected': isSelected(product.item) }"
                    @click="toggleProductSelection(product.item)"
                >
                    <ProductSmallCard :product="product" :item="product.item" />
                </div>
            </div>
            <p>{{ $t("common.return.provide_reason") }}</p>
            <textarea
                v-model="form.reason"
                class="return-order-reason-textarea"
                :placeholder="$t('common.return.reason_placeholder')"
                required
                aria-label="Reason for Return"
            ></textarea>
            <button @click="goBack" class="return-order-button-back view-all">
                {{ $t("common.return.back") }}
            </button>
            <button
                @click="goToNextStep"
                class="return-order-button-next view-all"
            >
                {{ $t("common.return.next") }}
            </button>
        </div>

        <!-- Step 3: Shipping Options -->
        <div v-if="activeStep === 3" class="return-order-shipping">
            <p>{{ $t("common.return.reason", { reason: form.reason }) }}</p>
            <div
                v-for="product in order?.data.products"
                :key="product.item"
                class="return-order-selected-product"
            >
                <ProductSmallCard
                    :product="product"
                    v-if="isSelected(product.item)"
                    :item="product.item"
                />
            </div>
            <h1 class="return-order-total">
                {{ $t("common.return.select_shipping") }}
            </h1>
            <p class="return-order-description">
                {{ $t("common.return.select_shipping_desc") }}
            </p>
            <div class="return-order-shipping-option">
                <label class="return-order-radio">
                    <input
                        type="radio"
                        value="DHL Parcel Shop"
                        v-model="selectedService"
                        required
                    />
                    {{ $t("common.return.dhl") }}
                    <span class="return-order-price">4,90 €</span>
                </label>
            </div>
            <div class="return-order-insurance">
                <h2 class="return-order-subtitle">
                    {{ $t("common.return.insurance") }}
                </h2>
                <div class="return-order-insurance-option">
                    <label class="return-order-radio">
                        <input
                            type="radio"
                            :value="true"
                            v-model="addInsurance"
                            :true-value="true"
                            :false-value="false"
                            required
                        />
                        {{ $t("common.return.insurance_yes") }}
                        <span class="return-order-price">1,99 €</span>
                    </label>
                    <p class="return-order-info">
                        {{
                            $t("common.return.insurance_info", {
                                amount: coverageAmount,
                            })
                        }}
                    </p>
                </div>
                <div class="return-order-insurance-option">
                    <label class="return-order-radio">
                        <input
                            type="radio"
                            :value="false"
                            v-model="addInsurance"
                            :true-value="true"
                            :false-value="false"
                            required
                        />
                        {{ $t("common.return.insurance_no") }}
                    </label>
                    <p class="return-order-info">
                        {{ $t("common.return.insurance_warning") }}
                    </p>
                </div>
            </div>
            <div class="return-order-summary">
                <p class="return-order-total-price">
                    <strong>{{
                        $t("common.return.total_amount", {
                            total: total.toFixed(2),
                        })
                    }}</strong>
                </p>
            </div>
            <div class="return-order-terms">
                <label class="return-order-checkbox">
                    <input
                        type="checkbox"
                        v-model="termsAccepted"
                        required
                        aria-label="Accept Terms and Conditions"
                    />
                    {{ $t("common.return.terms") }}
                </label>
            </div>
            <button @click="goBack" class="return-order-button-back">
                {{ $t("common.return.back_products") }}
            </button>
            <button
                :disabled="!termsAccepted"
                @click="submitReturn"
                class="return-order-button-final view-all"
            >
                {{ $t("common.return.next_step") }}
            </button>
        </div>
    </div>
</template>

<script setup>
import "@assets/css/order/returnOrder.css";
import { useI18n } from "vue-i18n";
import { useRoute } from "vue-router";
import { ChevronLeft } from "lucide-vue-next";
import { useToast } from "vue-toastification";
import { ref, computed, onMounted } from "vue";
import Spinner from "@components/ui/Spinner.vue";
import { useSessionStorage } from "@vueuse/core";
import ProductSmallCard from "@components/shop/product/ProductSmallCard.vue";
import { apiQuery } from "@lib/helpers";
import { checkoutform } from "@lib/store/shop/index.js";

// Internationalization
const { t } = useI18n();
const toast = useToast();
const route = useRoute();
const slug = computed(() => route.params.slug);

// Reactive State
const activeStep = ref(1);
const selectedReturnedProducts = ref([]);
const form = useSessionStorage("active-return-form", {
    email:
        checkoutform.value.email ||
        useSessionStorage("active-return-form").value?.email ||
        "",
    orderNr: "",
    orderId: null,
    reason: "",
});
const returnData = ref({});

// API Query
const orderQueryKey = computed(() => form.value.orderNr);
const {
    data: order,
    isLoading: isLoadingOrder,
    refetch: refetchOrder,
} = apiQuery("orders").useGetById(orderQueryKey);

// Fetch Order
const fetchOrder = async () => {
    try {
        const { data } = await refetchOrder();
        if (!data || !data.data.products) {
            throw new Error(t("common.alerts.orderNotFound"));
        }
        activeStep.value = 2;
    } catch (error) {
        alert(error.message || t("common.alerts.fetchError"));
        console.error("Failed to fetch order:", error);
    }
};

// Form Submission
const submitForm = async () => {
    if (!form.value.email || !form.value.orderNr) {
        alert(t("common.alerts.fillAllFields"));
        return;
    }
    await fetchOrder();
};

// Product Selection
const toggleProductSelection = (item) => {
    const index = selectedReturnedProducts.value.findIndex(
        (p) => p.id === item.id
    );
    if (index === -1) {
        selectedReturnedProducts.value.push(item);
    } else {
        selectedReturnedProducts.value.splice(index, 1);
    }
};
const isSelected = (item) =>
    selectedReturnedProducts.value.some((p) => p.id === item.id);

// Navigation
const goBack = () => {
    activeStep.value = Math.max(activeStep.value - 1, 1);
};
const goToNextStep = () => {
    if (selectedReturnedProducts.value.length === 0) {
        alert(t("common.alerts.selectAtLeastOneProduct"));
        return;
    }
    if (!form.value.reason.trim()) {
        alert(t("common.alerts.provideReason"));
        return;
    }
    activeStep.value = 3;
};

// Shipping and Insurance
const selectedService = ref("DHL Parcel Shop");
const termsAccepted = ref(false);
const addInsurance = ref(false);
const shippingFee = 4.9;
const insuranceFee = 1.99;
const coverageAmount = 20;
const total = computed(() =>
    addInsurance.value ? shippingFee + insuranceFee : shippingFee
);

// Submit Return
const { mutate: returnOrder, isLoading } =
    apiQuery("returns").useStore(returnData);
const submitReturn = () => {
    const payload = {
        total: total.value,
        order_number: form.value.orderNr,
        order_id: order.value.data.id,
        email: form.value.email,
        status: "requested",
        reason: form.value.reason,
        items: JSON.parse(JSON.stringify(selectedReturnedProducts.value)),
    };

    returnOrder(payload, {
        onSuccess: (data) => {
            if (data?.checkout_url) {
                window.location.href = data.checkout_url;
            }
        },
    });
};

// Reset Stepper
const resetStepper = () => {
    selectedReturnedProducts.value = [];
    form.value = {
        email:
            checkoutform.value.email ||
            useSessionStorage("active-return-form").value?.email ||
            "",
        orderNr: "",
        reason: "",
    };
    activeStep.value = 1;
    returnData.value = {};
};

// Lifecycle Hooks
onMounted(() => {
    if (slug.value === "success") {
        resetStepper();
        toast.success(t("common.alerts.returnSuccess"));
    }
});
</script>
