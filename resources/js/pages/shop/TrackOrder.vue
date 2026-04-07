<template>
    <div
        class="min-h-screen bg-background p-4 md:p-8 lg:p-12 selection:bg-primary selection:text-primary-foreground font-sans"
    >
        <div class="max-w-4xl mx-auto">
            <header class="mb-12 space-y-6">
                <div
                    class="flex items-center justify-between border-b-4 border-primary pb-6"
                >
                    <div>
                        <div
                            class="flex items-center space-x-2 text-[10px] font-black uppercase tracking-[0.3em] text-muted-foreground mb-2"
                        >
                            <span
                                :class="activeStep === 1 ? 'text-primary' : ''"
                                >01 {{ $t("pages.tracking.verification") }}</span
                            >
                            <ChevronRight class="w-3 h-3" />
                            <span
                                :class="activeStep === 2 ? 'text-primary' : ''"
                                >02 {{ $t("pages.tracking.logistics") }}</span
                            >
                        </div>
                        <h1
                            class="text-5xl md:text-7xl font-bold uppercase tracking-tighter italic leading-none text-foreground"
                        >
                            {{
                                activeStep === 1
                                    ? $t("pages.tracking.title")
                                    : $t("pages.tracking.status")
                            }}
                        </h1>
                    </div>

                    <Button
                        v-if="activeStep === 2"
                        variant="ghost"
                        @click="goBack"
                        class="rounded-none h-auto p-0 uppercase tracking-widest text-xs font-black hover:bg-transparent hover:text-primary transition-colors"
                    >
                        <ArrowLeft class="w-4 h-4 mr-2" />
                        {{ $t("components.buttons.back") }}
                    </Button>
                </div>

                <Alert
                    v-if="errorMessage"
                    variant="destructive"
                    class="rounded-none border-2 bg-destructive text-destructive-foreground animate-in slide-in-from-top-2"
                >
                    <AlertCircle class="w-4 h-4" />
                    <AlertTitle
                        class="uppercase font-black tracking-widest text-xs"
                        >{{ $t("pages.tracking.error") }}</AlertTitle
                    >
                    <AlertDescription class="font-bold uppercase text-[10px]">
                        {{ errorMessage }}
                    </AlertDescription>
                </Alert>
            </header>

            <main class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                <section
                    v-if="activeStep === 1"
                    class="lg:col-span-6 space-y-8 animate-in fade-in slide-in-from-bottom-4"
                >
                    <p
                        class="text-muted-foreground text-sm leading-relaxed max-w-sm"
                    >
                        {{ $t("pages.tracking.credentialsDesc") }}
                    </p>

                    <form @submit.prevent="handleSubmit" class="space-y-6">
                        <div class="space-y-2">
                            <Label
                                class="text-[10px] font-black uppercase tracking-[0.2em] text-muted-foreground"
                            >
                                {{ $t("validation.form.email") }}
                            </Label>
                            <Input
                                v-model="form.email"
                                type="email"
                                required
                                class="rounded-none border-2 border-border bg-accent_light h-14 focus-visible:ring-primary focus-visible:border-primary uppercase font-bold text-xs"
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
                                class="rounded-none border-2 border-border bg-accent_light h-14 focus-visible:ring-primary focus-visible:border-primary uppercase font-bold text-xs"
                            />
                        </div>

                        <Button
                            type="submit"
                            :disabled="isLoadingOrder"
                            class="rounded-none w-full h-20 text-lg font-black uppercase tracking-[0.2em] bg-primary text-primary-foreground hover:bg-primary/90 transition-all"
                        >
                            <Spinner
                                v-if="isLoadingOrder"
                                size="xs"
                                class="mr-2"
                            />
                            {{ $t("components.buttons.track") }}
                        </Button>
                    </form>
                </section>

                <template v-else-if="activeStep === 2 && order?.data">
                    <section class="lg:col-span-7 space-y-12">
                        <Card
                            class="rounded-none border-none bg-primary text-primary-foreground overflow-hidden"
                        >
                            <CardHeader
                                class="flex flex-row items-center justify-between space-y-0 p-8"
                            >
                                <div>
                                    <p
                                        class="text-[10px] font-bold uppercase tracking-widest opacity-60"
                                    >
                                        {{ $t("pages.tracking.status") }}
                                    </p>
                                    <CardTitle
                                        class="text-4xl font-black uppercase italic tracking-tighter"
                                    >
                                        {{
                                            order.data.status ||
                                            $t("common.unknown")
                                        }}
                                    </CardTitle>
                                </div>
                                <PackageCheck class="w-12 h-12 opacity-20" />
                            </CardHeader>
                            <CardContent class="px-8 pb-8">
                                <div
                                    class="grid grid-cols-2 gap-4 border-t border-primary-foreground/20 pt-6"
                                >
                                    <div>
                                        <p
                                            class="text-[10px] font-bold uppercase tracking-widest opacity-60"
                                        >
                                            {{ $t("pages.tracking.reference") }}
                                        </p>
                                        <p class="font-mono font-bold">
                                            {{ order.data.order_number }}
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p
                                            class="text-[10px] font-bold uppercase tracking-widest opacity-60"
                                        >
                                            {{ $t("pages.tracking.date") }}
                                        </p>
                                        <p class="font-bold">
                                            {{
                                                formatDateLocal(
                                                    order.data.created_at,
                                                )
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>

                        <div class="space-y-6">
                            <h3
                                class="flex items-center space-x-2 text-xs font-black uppercase tracking-[0.2em]"
                            >
                                <Box class="w-4 h-4" />
                                <span>{{ $t("pages.tracking.items") }}</span>
                            </h3>
                            <div class="space-y-4">
                                <ProductSmallCard
                                    v-for="product in order.data.products"
                                    :key="product.item.id || product.item"
                                    :product="product"
                                    :item="product.item"
                                    class="rounded-none border-2 border-border"
                                />
                            </div>
                        </div>
                    </section>

                    <aside class="lg:col-span-5 space-y-8">
                        <Card
                            v-if="order.data.tracking.number"
                            class="rounded-none border-2 border-primary bg-transparent"
                        >
                            <CardHeader class="p-6">
                                <div
                                    class="flex justify-between items-center mb-4"
                                >
                                    <Badge
                                        variant="outline"
                                        class="rounded-none border-primary text-primary font-black uppercase text-[9px]"
                                        >{{ $t("pages.tracking.liveTracking") }}</Badge
                                    >
                                    <Truck class="w-5 h-5" />
                                </div>
                                <p class="font-mono text-xl font-bold">
                                    {{ order.data.tracking.number }}
                                </p>
                                <p
                                    class="text-[10px] uppercase font-bold text-muted-foreground"
                                >
                                    {{ order.data.tracking.carrier }}
                                </p>
                            </CardHeader>
                            <CardFooter class="p-6 pt-0">
                                <Button
                                    as="a"
                                    :href="order.data.tracking.url"
                                    target="_blank"
                                    class="w-full rounded-none uppercase font-black tracking-widest text-xs h-12"
                                >
                                    {{ $t("pages.tracking.carrierSite") }}
                                </Button>
                            </CardFooter>
                        </Card>

                        <Card
                            class="rounded-none border-2 border-border bg-accent_light shadow-none"
                        >
                            <CardHeader class="p-6">
                                <CardTitle
                                    class="text-xs font-black uppercase tracking-widest"
                                    >{{ $t("pages.tracking.deliveryAddress") }}</CardTitle
                                >
                            </CardHeader>
                            <CardContent
                                class="p-6 pt-0 text-sm font-bold uppercase leading-tight italic"
                            >
                                <div v-if="order.data.shipping_address">
                                    {{ order.data.shipping_address.first_name }}
                                    {{ order.data.shipping_address.last_name
                                    }}<br />
                                    <span
                                        class="text-muted-foreground not-italic"
                                        >{{
                                            order.data.shipping_address.line_one
                                        }}</span
                                    ><br />
                                    <span
                                        class="text-muted-foreground not-italic"
                                        >{{
                                            order.data.shipping_address.postcode
                                        }}
                                        {{
                                            order.data.shipping_address.city
                                        }}</span
                                    >
                                </div>
                            </CardContent>
                        </Card>

                        <Card
                            class="rounded-none border-2 border-border shadow-none"
                        >
                            <CardHeader class="p-6">
                                <CardTitle
                                    class="text-xs font-black uppercase tracking-widest border-b border-border pb-3"
                                    >{{ $t("pages.tracking.summary") }}</CardTitle
                                >
                            </CardHeader>
                            <CardContent
                                class="p-6 pt-0 space-y-3 uppercase font-bold text-xs"
                            >
                                <div class="flex justify-between">
                                    <span class="text-muted-foreground"
                                        >{{ $t("pages.tracking.subtotal") }}</span
                                    >
                                    <span
                                        >{{
                                            order.data.totals.sub_total
                                        }}
                                        {{ $currency }}</span
                                    >
                                </div>
                                <div
                                    v-if="order.data.totals.shipping_total > 0"
                                    class="flex justify-between"
                                >
                                    <span class="text-muted-foreground"
                                        >{{ $t("pages.tracking.shipping") }}</span
                                    >
                                    <span
                                        >+
                                        {{
                                            order.data.totals.shipping_total
                                        }}
                                        {{ $currency }}</span
                                    >
                                </div>
                                <Separator class="bg-primary h-[2px]" />
                                <div
                                    class="flex justify-between text-lg font-black pt-2"
                                >
                                    <span>{{ $t("pages.tracking.total") }}</span>
                                    <span
                                        >{{
                                            order.data.totals.total_price
                                        }}
                                        {{ $currency }}</span
                                    >
                                </div>
                            </CardContent>
                        </Card>
                    </aside>
                </template>
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { useI18n } from "vue-i18n";
import { useRoute } from "vue-router";
import { useToast } from "vue-toastification";
import { useSessionStorage } from "@vueuse/core";
import {
    ChevronRight,
    ArrowLeft,
    PackageCheck,
    Truck,
    AlertCircle,
    Box,
} from "lucide-vue-next";

// shadcn-vue components
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
    Card,
    CardHeader,
    CardTitle,
    CardContent,
    CardFooter,
} from "@/components/ui/card";
import { Alert, AlertTitle, AlertDescription } from "@/components/ui/alert";
import { Separator } from "@/components/ui/separator";
import { Badge } from "@/components/ui/badge";

// Existing custom components & logic
import Spinner from "@components/ui/Spinner.vue";
import ProductSmallCard from "@components/shop/product/ProductSmallCard.vue";
import { apiQuery } from "@lib/helpers";
import { checkoutform } from "@lib/store/shop/index.js";

const { t } = useI18n();
const toast = useToast();
const route = useRoute();
const slug = route.params.slug;

const activeStep = ref(1);
const errorMessage = ref("");
const form = useSessionStorage("active-track-form", {
    email: checkoutform.value.email || "",
    orderNr: route.params.id || "",
});

const orderQueryKey = computed(() => form.value.orderNr);
const orderQueryParams = computed(() => ({ email: form.value.email }));
const {
    data: order,
    isLoading: isLoadingOrder,
    refetch: refetchOrder,
} = apiQuery("orders").useGetById(orderQueryKey, orderQueryParams);

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
