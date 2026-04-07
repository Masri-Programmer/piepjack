<template>
    <div
        class="max-w-4xl mx-auto px-6 py-16 sm:py-24 selection:bg-main selection:text-accent"
    >
        <header class="mb-12 border-b-12 border-border pb-8">
            <div class="flex items-center gap-4 mb-4">
                <Button
                    v-if="activeStep > 1"
                    variant="ghost"
                    size="icon"
                    @click="goBack()"
                    class="hover:bg-accent_light rounded-none p-0 h-10 w-10 border border-main"
                >
                    <ChevronLeft class="w-6 h-6" />
                </Button>
                <p
                    class="text-xs font-bold uppercase tracking-[0.2em] text-muted-foreground"
                >
                    {{ $t("common.return.step", { current: activeStep, total: 3 }) }}
                </p>
            </div>
            <h1
                class="text-5xl sm:text-7xl font-bold uppercase tracking-tighter italic leading-none text-foreground"
            >
                {{ $t("common.titles.return_exchange") }}
            </h1>
        </header>

        <section
            v-if="activeStep === 1"
            class="space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-500"
        >
            <form @submit.prevent="submitForm" class="grid gap-6">
                <div class="space-y-2">
                    <label
                        class="text-xs font-bold uppercase tracking-widest text-muted-foreground"
                        >{{ $t("common.forms.emailLabel") }}</label
                    >
                    <input
                        v-model="form.email"
                        type="email"
                        :placeholder="$t('common.input.email')"
                        class="w-full bg-accent_light border-2 border-border p-4 text-base focus:border-main outline-none rounded-none transition-all"
                        required
                    />
                </div>
                <div class="space-y-2">
                    <label
                        class="text-xs font-bold uppercase tracking-widest text-muted-foreground"
                        >{{ $t("validation.form.orderNumber") }}</label
                    >
                    <input
                        v-model="form.orderNr"
                        type="text"
                        :placeholder="$t('common.input.orderTracking')"
                        class="w-full bg-accent_light border-2 border-border p-4 text-base focus:border-main outline-none rounded-none transition-all"
                        required
                    />
                </div>
                <Button
                    type="submit"
                    :disabled="isLoadingOrder"
                    class="view-all w-full h-16 text-lg font-bold uppercase tracking-widest"
                >
                    <Spinner v-if="isLoadingOrder" size="xs" />
                    <span v-else>{{
                        $t("components.buttons.returnOrder")
                    }}</span>
                </Button>
            </form>
        </section>

        <section
            v-if="activeStep === 2 && order?.data"
            class="space-y-10 animate-in fade-in duration-500"
        >
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div
                    v-for="product in order.data.products"
                    :key="product.item.id"
                    @click="toggleProductSelection(product.item)"
                    :class="[
                        'relative cursor-pointer border-2 transition-all duration-300 p-2',
                        isSelected(product.item)
                            ? 'border-main bg-accent_light'
                            : 'border-border opacity-60 hover:opacity-100',
                    ]"
                >
                    <div
                        v-if="isSelected(product.item)"
                        class="absolute top-2 right-2 z-10 bg-main text-accent px-2 py-1 text-[10px] font-bold uppercase"
                    >
                        {{ $t("common.return.selected") }}
                    </div>
                    <ProductSmallCard
                        :product="product"
                        :item="product.item"
                        class="border-none"
                    />
                </div>
            </div>

            <div class="space-y-4">
                <h2 class="text-xl font-bold uppercase italic tracking-tight">
                    {{ $t("common.return.provide_reason") }}
                </h2>
                <textarea
                    v-model="form.reason"
                    :placeholder="$t('common.return.reason_placeholder')"
                    class="w-full bg-accent_light border-2 border-border p-6 min-h-[150px] outline-none focus:border-main rounded-none transition-all"
                    required
                ></textarea>
            </div>

            <div class="flex flex-col sm:flex-row gap-4">
                <Button
                    @click="goBack"
                    variant="outline"
                    class="flex-1 h-16 border-2 border-main uppercase font-bold rounded-none"
                >
                    {{ $t("common.return.back") }}
                </Button>
                <Button
                    @click="goToNextStep"
                    class="view-all flex-1 h-16 uppercase font-bold"
                >
                    {{ $t("common.return.next") }}
                </Button>
            </div>
        </section>

        <section
            v-if="activeStep === 3"
            class="space-y-12 animate-in fade-in duration-500"
        >
            <div class="border-2 border-main p-8 space-y-8 bg-background">
                <h2
                    class="text-2xl font-bold uppercase italic border-b-2 border-border pb-4"
                >
                    {{ $t("common.return.select_shipping") }}
                </h2>

                <label
                    class="flex items-center justify-between cursor-pointer group"
                >
                    <div class="flex items-center gap-4">
                        <input
                            type="radio"
                            value="DHL Parcel Shop"
                            v-model="selectedService"
                            class="w-5 h-5 accent-main"
                        />
                        <span
                            class="font-bold uppercase tracking-wider group-hover:italic"
                            >{{ $t("common.return.dhl") }}</span
                        >
                    </div>
                    <span class="font-mono font-bold text-lg">4,90 €</span>
                </label>

                <div class="pt-8 border-t-2 border-border space-y-6">
                    <h3
                        class="text-sm font-bold uppercase tracking-widest text-muted-foreground"
                    >
                        {{ $t("common.return.insurance") }}
                    </h3>
                    <div class="grid gap-4">
                        <label
                            class="flex flex-col p-4 border-2 border-border cursor-pointer transition-colors"
                            :class="{
                                'border-main bg-accent_light': addInsurance,
                            }"
                        >
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-3">
                                    <input
                                        type="radio"
                                        :value="true"
                                        v-model="addInsurance"
                                        class="w-4 h-4 accent-main"
                                    />
                                    <span
                                        class="font-bold uppercase text-sm italic"
                                        >{{
                                            $t("common.return.insurance_yes")
                                        }}</span
                                    >
                                </div>
                                <span class="font-mono font-bold">1,99 €</span>
                            </div>
                            <p class="text-xs text-muted-foreground">
                                {{
                                    $t("common.return.insurance_info", {
                                        amount: coverageAmount,
                                    })
                                }}
                            </p>
                        </label>

                        <label
                            class="flex flex-col p-4 border-2 border-border cursor-pointer transition-colors"
                            :class="{
                                'border-main bg-accent_light': !addInsurance,
                            }"
                        >
                            <div class="flex items-center gap-3 mb-2">
                                <input
                                    type="radio"
                                    :value="false"
                                    v-model="addInsurance"
                                    class="w-4 h-4 accent-main"
                                />
                                <span
                                    class="font-bold uppercase text-sm italic"
                                    >{{
                                        $t("common.return.insurance_no")
                                    }}</span
                                >
                            </div>
                            <p class="text-xs text-muted-foreground">
                                {{ $t("common.return.insurance_warning") }}
                            </p>
                        </label>
                    </div>
                </div>
            </div>

            <div class="bg-main text-accent p-8 space-y-6">
                <div
                    class="flex justify-between items-center text-2xl font-bold uppercase italic"
                >
                    <span>{{ $t("common.return.total_amount_label") }}</span>
                    <span class="font-mono">{{ total.toFixed(2) }} €</span>
                </div>
                <label class="flex gap-3 items-start cursor-pointer">
                    <input
                        type="checkbox"
                        v-model="termsAccepted"
                        class="mt-1 w-5 h-5 accent-accent"
                    />
                    <span
                        class="text-xs font-medium uppercase tracking-tighter leading-tight opacity-80"
                    >
                        {{ $t("common.return.terms") }}
                    </span>
                </label>
            </div>

            <div class="flex flex-col sm:flex-row gap-4">
                <Button
                    @click="goBack"
                    variant="outline"
                    class="flex-1 h-16 border-2 border-main uppercase font-bold rounded-none"
                >
                    {{ $t("common.return.back_items") }}
                </Button>
                <Button
                    :disabled="!termsAccepted || isLoading"
                    @click="submitReturn"
                    class="view-all flex-1 h-16"
                >
                    <Spinner v-if="isLoading" size="xs" />
                    <span v-else>{{ $t("common.return.next_step") }}</span>
                </Button>
            </div>
        </section>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useI18n } from "vue-i18n";
import { useRoute } from "vue-router";
import { ChevronLeft } from "lucide-vue-next";
import { useToast } from "vue-toastification";
import { useSessionStorage } from "@vueuse/core";

// Components
import Spinner from "@components/ui/Spinner.vue";
import ProductSmallCard from "@components/shop/product/ProductSmallCard.vue";
import { Button } from "@/components/ui/button";

// Logic
import { apiQuery } from "@lib/helpers";
import { checkoutform } from "@lib/store/shop/index.js";

const { t } = useI18n();
const toast = useToast();
const route = useRoute();
const slug = computed(() => route.params.slug);

const activeStep = ref(1);
const selectedReturnedProducts = ref([]);
const form = useSessionStorage("active-return-form", {
    email: checkoutform.value.email || "",
    orderNr: route.params.id || "",
    orderId: null,
    reason: "",
});

const orderQueryKey = computed(() => form.value.orderNr);
const orderQueryParams = computed(() => ({ email: form.value.email }));
const {
    data: order,
    isLoading: isLoadingOrder,
    refetch: refetchOrder,
} = apiQuery("orders").useGetById(orderQueryKey, orderQueryParams);

const fetchOrder = async () => {
    try {
        const { data, isError, error } = await refetchOrder();

        if (isError) {
            toast.error(error?.message || t("common.alerts.fetchError"));
            return;
        }

        if (!data?.data?.products) {
            throw new Error(t("common.alerts.orderNotFound"));
        }

        activeStep.value = 2;
    } catch (error) {
        toast.error(error.message || t("common.alerts.fetchError"));
    }
};

const submitForm = async () => {
    if (!form.value.email || !form.value.orderNr) {
        toast.error(t("common.alerts.fillAllFields"));
        return;
    }
    await fetchOrder();
};

const toggleProductSelection = (item) => {
    const index = selectedReturnedProducts.value.findIndex(
        (p) => p.id === item.id,
    );
    if (index === -1) {
        selectedReturnedProducts.value.push({
            ...item,
            cartQuantity: item.cartQuantity,
        });
    } else {
        selectedReturnedProducts.value.splice(index, 1);
    }
};

const isSelected = (item) =>
    selectedReturnedProducts.value.some((p) => p.id === item.id);

const goBack = () => (activeStep.value = Math.max(activeStep.value - 1, 1));

const goToNextStep = () => {
    if (selectedReturnedProducts.value.length === 0) {
        toast.error(t("common.alerts.selectAtLeastOneProduct"));
        return;
    }
    if (!form.value.reason.trim()) {
        toast.error(t("common.alerts.provideReason"));
        return;
    }
    activeStep.value = 3;
};

const selectedService = ref("DHL Parcel Shop");
const termsAccepted = ref(false);
const addInsurance = ref(false);
const shippingFee = 4.9;
const insuranceFee = 1.99;
const coverageAmount = 20;
const total = computed(() =>
    addInsurance.value ? shippingFee + insuranceFee : shippingFee,
);

const { mutate: returnOrder, isLoading } = apiQuery("returns").useStore();

const submitReturn = () => {
    if (!order.value?.data) return;

    const payload = {
        total: total.value, // This is now the deduction amount
        order_number: form.value.orderNr,
        order_id: order.value.data.id,
        email: form.value.email,
        status: "requested",
        reason: form.value.reason,
        items: JSON.parse(JSON.stringify(selectedReturnedProducts.value)),
    };

    returnOrder(payload, {
        onSuccess: (data) => {
            // Because there is no checkout_url anymore, we just redirect directly to success
            window.location.href = `/return-order/success?return_number=${data.data.return_number}`;
        },
        onError: (error) => {
            toast.error(error.message || t("common.alerts.submissionFailed"));
        },
    });
};

onMounted(() => {
    if (slug.value === "success") {
        selectedReturnedProducts.value = [];
        activeStep.value = 1;
        toast.success(t("common.alerts.returnSuccess"));
    }
});
</script>
