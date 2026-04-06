<template>
    <div
        class="min-h-[60vh] flex items-center justify-center p-6 selection:bg-main selection:text-accent"
    >
        <div
            class="w-full max-w-2xl bg-background border-2 border-border p-8 sm:p-12 animate-in fade-in zoom-in-95 duration-500"
        >
            <header
                class="mb-10 border-b-4 border-main pb-6 flex justify-between items-end"
            >
                <div>
                    <p
                        class="mb-2 text-xs font-bold uppercase tracking-[0.2em] text-muted-foreground"
                    >
                        {{ activeStep === 1 ? "Logistics" : "Details" }}
                    </p>
                    <h1
                        class="text-4xl sm:text-5xl font-bold uppercase tracking-tighter italic text-foreground leading-none"
                    >
                        {{ $t("pages.tracking.title") }}
                    </h1>
                </div>
                <button
                    v-if="activeStep === 2"
                    @click="goBack"
                    class="text-xs font-bold uppercase tracking-widest text-muted-foreground hover:text-main transition-colors mb-1 underline underline-offset-4"
                >
                    {{ $t("components.buttons.back") }}
                </button>
            </header>

            <div
                v-if="errorMessage"
                class="mb-8 p-4 bg-destructive text-destructive-foreground font-bold uppercase text-sm tracking-wider border-2 border-destructive animate-in slide-in-from-top-2"
            >
                ⚠️ {{ errorMessage }}
            </div>

            <form
                v-if="activeStep === 1"
                class="space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-500"
                @submit.prevent="handleSubmit"
                aria-label="Track Order Form"
            >
                <div class="space-y-2">
                    <label
                        class="text-xs font-bold uppercase tracking-widest text-muted-foreground"
                    >
                        {{ $t("validation.form.email") }}
                    </label>
                    <input
                        v-model="form.email"
                        type="email"
                        class="w-full bg-accent_light border-2 border-border p-5 text-base focus:border-main outline-none rounded-none transition-all"
                        required
                        aria-label="Email Address"
                    />
                </div>

                <div class="space-y-2">
                    <label
                        class="text-xs font-bold uppercase tracking-widest text-muted-foreground"
                    >
                        {{ $t("validation.form.orderNumber") }}
                    </label>
                    <input
                        v-model="form.orderNr"
                        type="text"
                        class="w-full bg-accent_light border-2 border-border p-5 text-base focus:border-main outline-none rounded-none transition-all"
                        required
                        aria-label="Order Number"
                    />
                </div>

                <button
                    type="submit"
                    :disabled="isLoadingOrder"
                    class="view-all w-full h-16 mt-4 text-lg font-bold uppercase tracking-widest flex items-center justify-center disabled:opacity-50"
                    aria-label="Track Order Button"
                >
                    <Spinner v-if="isLoadingOrder" size="xs" />
                    <span v-else>{{ $t("components.buttons.track") }}</span>
                </button>
            </form>

            <div
                v-if="
                    activeStep === 2 && !isLoadingOrder && order?.data?.products
                "
                class="space-y-8 animate-in fade-in duration-500"
            >
                <div
                    class="bg-main text-accent p-6 flex flex-col sm:flex-row justify-between items-start sm:items-center border-2 border-main"
                >
                    <div>
                        <p
                            class="text-xs font-bold uppercase tracking-[0.2em] opacity-70 mb-1"
                        >
                            {{ $t("pages.tracking.status") }}
                        </p>
                        <p
                            class="text-2xl font-bold uppercase italic tracking-wide"
                        >
                            {{ order?.data.status || $t("common.unknown") }}
                        </p>
                    </div>
                    <div class="mt-4 sm:mt-0 sm:text-right">
                        <p
                            class="text-xs font-bold uppercase tracking-[0.2em] opacity-70 mb-1"
                        >
                            {{ $t("pages.tracking.orderNr") }}
                        </p>
                        <p class="text-lg font-mono font-bold">
                            {{ order?.data.order_number }}
                        </p>
                    </div>
                </div>

                <div
                    class="flex items-center justify-between border-b-2 border-border pb-4"
                >
                    <p
                        class="text-sm font-bold uppercase tracking-widest text-muted-foreground"
                    >
                        {{ $t("pages.tracking.date") }}
                    </p>
                    <p class="font-bold text-foreground">
                        {{ formatDateLocal(order?.data.created_at) }}
                    </p>
                </div>

                <div>
                    <h2
                        class="text-sm font-bold uppercase tracking-widest text-muted-foreground mb-4"
                    >
                        Items in Shipment
                    </h2>
                    <div class="grid gap-4 border-l-2 border-border pl-4">
                        <ProductSmallCard
                            v-for="product in order?.data.products"
                            :key="product.item.id || product.item"
                            :product="product"
                            :item="product.item"
                            class="border border-border hover:border-main transition-colors"
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { useI18n } from "vue-i18n";
import { useRoute } from "vue-router";
import { useToast } from "vue-toastification";
import { useSessionStorage } from "@vueuse/core";

// Components
import Spinner from "@components/ui/Spinner.vue";
import ProductSmallCard from "@components/shop/product/ProductSmallCard.vue";

// Logic
import { apiQuery } from "@lib/helpers";
import { checkoutform } from "@lib/store/shop/index.js";

const { t } = useI18n();
const toast = useToast();
const route = useRoute();
const slug = route.params.slug;

// State
const activeStep = ref(1);
const errorMessage = ref("");
const form = useSessionStorage("active-track-form", {
    email: checkoutform.value.email || "",
    orderNr: route.params.id || "",
});

// API
const orderQueryKey = computed(() => form.value.orderNr);
const orderQueryParams = computed(() => ({ email: form.value.email }));
const {
    data: order,
    isLoading: isLoadingOrder,
    refetch: refetchOrder,
} = apiQuery("orders").useGetById(orderQueryKey, orderQueryParams);

// Helpers
const formatDateLocal = (dateString) => {
    if (!dateString) return t("common.unknown");
    const date = new Date(dateString);
    return date.toLocaleDateString(undefined, {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

const fetchOrder = async () => {
    errorMessage.value = "";
    try {
        const { data } = await refetchOrder();
        if (!data?.data) throw new Error(t("common.alerts.orderNotFound"));
        activeStep.value = 2;
    } catch (error) {
        errorMessage.value = error.message;
    }
};

const handleSubmit = async () => {
    if (!form.value.email || !form.value.orderNr) {
        errorMessage.value = t("common.alerts.fillAllFields");
        return;
    }
    await fetchOrder();
};

const goBack = () => {
    activeStep.value = 1;
    errorMessage.value = "";
};

// Lifecycle
onMounted(() => {
    if (slug === "success") {
        activeStep.value = 1;
        toast.success(t("common.alerts.trackSuccess"));
    }
    if (form.value.email && form.value.orderNr) {
        handleSubmit();
    }
});

watch(
    () => route.params.id,
    (newId) => {
        if (newId) {
            form.value.orderNr = newId;
            handleSubmit();
        }
    },
);
</script>
