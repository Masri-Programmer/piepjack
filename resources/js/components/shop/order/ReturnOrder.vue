<template>
    <PageLayout
        title="Return Order"
        description="Initiate your return or exchange process for Piepjack orders."
    >
        <template #header>
            <div
                class="flex flex-wrap items-center justify-between border-b-4 border-primary pb-6"
            >
                <div>
                    <div
                        class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-[0.3em] text-muted-foreground mb-2"
                    >
                        <span :class="activeStep >= 1 ? 'text-primary' : ''"
                            >01 {{ $t("common.return.verify") }}</span
                        >
                        <ChevronRight class="w-3 h-3" />
                        <span :class="activeStep >= 2 ? 'text-primary' : ''"
                            >02 {{ $t("common.return.select") }}</span
                        >
                        <ChevronRight class="w-3 h-3" />
                        <span :class="activeStep === 3 ? 'text-primary' : ''"
                            >03 {{ $t("common.return.logistics") }}</span
                        >
                    </div>
                    <h1
                        class="text-5xl md:text-7xl font-bold uppercase tracking-tighter italic leading-none text-foreground"
                    >
                        {{ $t("common.titles.return_exchange") }}
                    </h1>
                </div>

                <Button
                    v-if="activeStep > 1"
                    variant="ghost"
                    @click="goBack"
                    class="rounded-none h-auto p-0 uppercase tracking-widest text-xs font-black hover:bg-transparent hover:text-primary transition-colors"
                >
                    <ArrowLeft class="w-4 h-4 mr-2" />
                    {{ $t("components.buttons.back") }}
                </Button>
            </div>
        </template>

        <main class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            <section
                v-if="activeStep === 1"
                class="lg:col-span-6 space-y-8 animate-in fade-in slide-in-from-bottom-4"
            >
                <p
                    class="text-muted-foreground text-sm font-bold uppercase tracking-widest leading-relaxed text-center"
                >
                    {{ $t("common.return.identify_desc") }}
                </p>

                <form
                    @submit.prevent="submitForm"
                    class="space-y-6 p-8 border-2 border-border bg-muted/30"
                >
                    <div class="space-y-2">
                        <Label
                            class="text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground"
                        >
                            {{ $t("common.forms.emailLabel") }}
                        </Label>
                        <Input
                            v-model="form.email"
                            type="email"
                            required
                            :placeholder="$t('common.input.email')"
                            class="rounded-none border-2 border-border bg-background focus-visible:ring-primary focus-visible:border-primary uppercase font-bold text-xs"
                        />
                    </div>

                    <div class="space-y-2">
                        <Label
                            class="text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground"
                        >
                            {{ $t("validation.form.orderNumber") }}
                        </Label>
                        <Input
                            v-model="form.orderNr"
                            type="text"
                            required
                            :placeholder="$t('common.input.orderTracking')"
                            class="rounded-none border-2 border-border bg-background focus-visible:ring-primary focus-visible:border-primary uppercase font-bold text-xs"
                        />
                    </div>

                    <Button
                        type="submit"
                        :disabled="isLoadingOrder"
                        class="view-all"
                    >
                        <Spinner v-if="isLoadingOrder" size="xs" class="mr-2" />
                        {{ $t("components.buttons.returnOrder") }}
                    </Button>
                </form>
            </section>

            <section
                v-else-if="activeStep === 2 && order?.data"
                class="lg:col-span-12 space-y-10 animate-in fade-in duration-500"
            >
                <div class="space-y-4">
                    <h2
                        class="text-xl font-black uppercase tracking-widest border-b-2 border-border pb-2"
                    >
                        {{ $t("common.return.select_items_title") }}
                    </h2>
                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
                    >
                        <div
                            v-for="product in order.data.products"
                            :key="product.item.id"
                            @click="
                                toggleProductSelection(
                                    product.item,
                                    product.quantity,
                                )
                            "
                            :class="[
                                'relative cursor-pointer border-2 transition-all duration-200 bg-background group',
                                isSelected(product.item)
                                    ? 'border-primary shadow-[4px_4px_0px_0px_var(--theme-primary)]'
                                    : 'border-border hover:border-primary/50',
                            ]"
                        >
                            <div
                                v-if="isSelected(product.item)"
                                class="absolute top-2 right-2 z-10 bg-primary text-primary-foreground px-3 py-1 text-[10px] font-black uppercase tracking-widest"
                            >
                                {{ $t("common.return.selected") }}
                            </div>
                            <ProductSmallCard
                                :product="product"
                                :item="product.item"
                                class="border-none pointer-events-none p-4"
                            />
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <Label
                        class="text-xl font-black uppercase tracking-widest border-b-2 border-border pb-2 block"
                    >
                        {{ $t("common.return.provide_reason") }}
                    </Label>
                    <Textarea
                        v-model="form.reason"
                        :placeholder="$t('common.return.reason_placeholder')"
                        required
                        class="rounded-none border-2 border-border bg-muted/30 focus-visible:ring-primary focus-visible:border-primary min-h-[150px] font-bold text-sm resize-y"
                    />
                </div>

                <div class="flex justify-end pt-4">
                    <Button
                        @click="goToNextStep"
                        class="view-all w-full sm:w-auto"
                    >
                        {{ $t("common.return.next") }}
                        <ChevronRight class="w-5 h-5 ml-2" />
                    </Button>
                </div>
            </section>

            <section
                v-else-if="activeStep === 3"
                class="lg:col-span-12 space-y-12 animate-in fade-in slide-in-from-right-4 duration-500"
            >
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                    <div class="lg:col-span-7 space-y-8">
                        <Card
                            class="rounded-none border-2 border-border shadow-none bg-background"
                        >
                            <CardHeader
                                class="border-b-2 border-border p-6 bg-muted/30"
                            >
                                <CardTitle
                                    class="text-xl font-black uppercase tracking-widest"
                                >
                                    {{ $t("common.return.select_shipping") }}
                                </CardTitle>
                            </CardHeader>
                            <CardContent class="p-6">
                                <label
                                    class="flex items-center justify-between cursor-pointer group p-4 border-2 border-primary bg-primary/5"
                                >
                                    <div class="flex items-center gap-4">
                                        <input
                                            type="radio"
                                            value="DHL Parcel Shop"
                                            v-model="selectedService"
                                            class="w-5 h-5 accent-primary cursor-pointer"
                                        />
                                        <div class="space-y-1">
                                            <span
                                                class="block font-black uppercase tracking-widest text-sm group-hover:text-primary transition-colors"
                                            >
                                                {{ $t("common.return.dhl") }}
                                            </span>
                                            <span
                                                class="block text-xs font-bold text-muted-foreground uppercase"
                                                >{{
                                                    $t(
                                                        "common.return.drop_off_desc",
                                                    )
                                                }}</span
                                            >
                                        </div>
                                    </div>
                                    <span
                                        class="font-black uppercase tracking-widest text-sm text-primary"
                                        >{{ $t("common.forms.free") }}</span
                                    >
                                </label>
                            </CardContent>
                        </Card>
                    </div>

                    <div class="lg:col-span-5 space-y-8">
                        <Card
                            class="rounded-none border-none bg-primary text-primary-foreground shadow-none"
                        >
                            <CardHeader class="p-8 pb-4">
                                <CardTitle
                                    class="text-sm font-black uppercase tracking-[0.2em] opacity-80"
                                >
                                    {{ $t("common.return.summary_title") }}
                                </CardTitle>
                            </CardHeader>
                            <CardContent class="p-8 pt-0 space-y-6">
                                <div
                                    class="space-y-3 text-sm font-bold uppercase tracking-widest"
                                >
                                    <div class="flex justify-between">
                                        <span class="opacity-80">{{
                                            $t("common.return.selected_carrier")
                                        }}</span>
                                        <span class="font-mono">{{
                                            selectedService
                                        }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="opacity-80">{{
                                            $t("common.return.fee")
                                        }}</span>
                                        <span class="font-mono">0.00 €</span>
                                    </div>
                                    <div
                                        class="flex justify-between pt-2 border-t border-primary-foreground/20"
                                    >
                                        <span class="font-black">{{
                                            $t(
                                                "common.return.total_amount_label",
                                            )
                                        }}</span>
                                        <span class="font-black">0.00 €</span>
                                    </div>
                                </div>

                                <Separator
                                    class="bg-primary-foreground/20 h-[2px]"
                                />

                                <div class="pt-2">
                                    <label
                                        class="flex gap-4 items-start cursor-pointer group"
                                    >
                                        <input
                                            type="checkbox"
                                            v-model="termsAccepted"
                                            class="mt-1 w-5 h-5 accent-primary-foreground cursor-pointer flex-shrink-0"
                                        />
                                        <span
                                            class="text-[10px] font-bold uppercase tracking-wider leading-relaxed opacity-80 group-hover:opacity-100 transition-opacity"
                                        >
                                            {{ $t("common.return.terms") }}
                                        </span>
                                    </label>
                                </div>
                            </CardContent>
                            <div class="p-8 pt-0 mt-4">
                                <Button
                                    :disabled="!termsAccepted || isLoading"
                                    @click="submitReturn"
                                    variant="outline"
                                    class="view-all"
                                >
                                    <Spinner
                                        v-if="isLoading"
                                        size="xs"
                                        class="mr-2"
                                    />
                                    <span v-else>{{
                                        $t("common.return.next_step")
                                    }}</span>
                                </Button>
                            </div>
                        </Card>
                    </div>
                </div>
            </section>
        </main>
    </PageLayout>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useI18n } from "vue-i18n";
import { useRoute } from "vue-router";
import { ChevronRight, ArrowLeft } from "lucide-vue-next";
import { useToast } from "vue-toastification";
import { useSessionStorage } from "@vueuse/core";

// components
import PageLayout from "@components/shop/general/PageLayout.vue";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Textarea } from "@/components/ui/textarea";
import { Card, CardHeader, CardTitle, CardContent } from "@/components/ui/card";
import { Separator } from "@/components/ui/separator";

// Existing custom components & logic
import Spinner from "@components/ui/Spinner.vue";
import ProductSmallCard from "@components/shop/product/ProductSmallCard.vue";
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

const toggleProductSelection = (item, orderQuantity = 1) => {
    const index = selectedReturnedProducts.value.findIndex(
        (p) => p.id === item.id,
    );
    if (index === -1) {
        selectedReturnedProducts.value.push({
            ...item,
            cartQuantity: item.cartQuantity || orderQuantity,
        });
    } else {
        selectedReturnedProducts.value.splice(index, 1);
    }
};

const isSelected = (item) =>
    selectedReturnedProducts.value.some((p) => p.id === item.id);

const goBack = () => {
    activeStep.value = Math.max(activeStep.value - 1, 1);
};

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

// Logistics State
const selectedService = ref("DHL Parcel Shop");
const termsAccepted = ref(false);

const { mutate: returnOrder, isLoading } = apiQuery("returns").useStore();

const submitReturn = () => {
    if (!order.value?.data) return;

    const payload = {
        total: 0, // Deduction amount is 0 for free returns
        order_number: form.value.orderNr,
        order_id: order.value.data.id,
        email: form.value.email,
        status: "requested",
        reason: form.value.reason,
        items: JSON.parse(JSON.stringify(selectedReturnedProducts.value)),
    };

    returnOrder(payload, {
        onSuccess: (data) => {
            window.location.href = `/return-order/success?return_number=${data.data.return_number}`;
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
