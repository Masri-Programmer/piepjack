<template>
    <PageLayout
        :title="$t('components.cart.title')"
        :description="$t('components.cart.shippingFree')"
    >
        <template #header>
            <div class="space-y-4 border-b-4 border-border pb-6">
                <h1
                    class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold uppercase tracking-tighter italic wrap-break-word hyphens-auto"
                >
                    {{ $t("components.cart.title") }}
                </h1>
            </div>
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-16">
            <div class="lg:col-span-8">
                <div class="w-full overflow-x-auto pb-4">
                    <Table class="rounded-none border-2 border-border w-full">
                        <TableHeader class="bg-muted/50">
                            <TableRow
                                class="border-b-2 border-border hover:bg-transparent"
                            >
                                <TableHead
                                    class="font-black uppercase tracking-widest text-[10px] sm:text-xs text-foreground py-4 sm:py-6 whitespace-nowrap"
                                >
                                    {{ $t("components.cart.products") }}
                                </TableHead>
                                <TableHead
                                    class="text-center font-black uppercase tracking-widest text-[10px] sm:text-xs text-foreground py-4 sm:py-6 whitespace-nowrap"
                                >
                                    {{ $t("components.cart.quantity") }}
                                </TableHead>
                                <TableHead
                                    class="text-right font-black uppercase tracking-widest text-[10px] sm:text-xs text-foreground py-4 sm:py-6 whitespace-nowrap"
                                >
                                    {{ $t("components.cart.total") }}
                                </TableHead>
                            </TableRow>
                        </TableHeader>

                        <TableBody class="divide-y-2 divide-border">
                            <template v-if="cartState.cartItems.length > 0">
                                <template
                                    v-for="product in cartState.cartItems"
                                    :key="product.id"
                                >
                                    <TableRow
                                        v-for="item in product.items"
                                        :key="item.id"
                                        class="hover:bg-muted transition-colors"
                                    >
                                        <TableCell
                                            class="py-4 sm:py-6 min-w-[250px] sm:min-w-[300px]"
                                        >
                                            <div
                                                class="flex items-start sm:items-center gap-4 sm:gap-6"
                                            >
                                                <div
                                                    class="w-16 h-16 sm:w-20 sm:h-20 bg-muted border-2 border-border shrink-0"
                                                >
                                                    <img
                                                        :src="
                                                            item?.image_url ||
                                                            product?.image_url
                                                        "
                                                        class="w-full h-full object-contain"
                                                        :alt="product?.name"
                                                    />
                                                </div>
                                                <div
                                                    class="space-y-1 min-w-0 flex-1"
                                                >
                                                    <p
                                                        class="font-bold uppercase tracking-tight text-sm sm:text-lg wrap-break-word text-wrap"
                                                    >
                                                        {{ product?.name }}
                                                    </p>
                                                    <div
                                                        v-if="item?.options"
                                                        class="flex flex-wrap gap-1.5 sm:gap-2"
                                                    >
                                                        <span
                                                            v-for="opt in item.options"
                                                            :key="opt.name"
                                                            class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest bg-muted px-2 py-0.5 border border-border truncate max-w-full"
                                                        >
                                                            {{ opt.value }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </TableCell>

                                        <TableCell
                                            class="text-center py-4 sm:py-6 align-middle"
                                        >
                                            <div
                                                class="flex justify-center min-w-[100px]"
                                            >
                                                <CustomNumberInput
                                                    :modelValue="
                                                        item.cartQuantity
                                                    "
                                                    @update:modelValue="
                                                        (val) =>
                                                            updateItemQuantity({
                                                                productId:
                                                                    product.id,
                                                                itemId: item.id,
                                                                quantity: val,
                                                            })
                                                    "
                                                    :max="item.quantity"
                                                    size="sm"
                                                />
                                            </div>
                                        </TableCell>

                                        <TableCell
                                            class="text-right py-4 sm:py-6 align-middle min-w-[100px]"
                                        >
                                            <div
                                                class="flex flex-col items-end gap-1"
                                            >
                                                <span
                                                    class="font-mono font-bold text-base sm:text-lg whitespace-nowrap"
                                                >
                                                    {{
                                                        (
                                                            item.cartQuantity *
                                                            item.price
                                                        ).toFixed(2)
                                                    }}
                                                    {{ $currency }}
                                                </span>
                                                <button
                                                    @click="
                                                        removeFromCart({
                                                            productId:
                                                                product.id,
                                                            itemId: item.id,
                                                        })
                                                    "
                                                    class="text-[9px] sm:text-[10px] font-black uppercase tracking-widest text-destructive hover:underline whitespace-nowrap"
                                                >
                                                    {{
                                                        $t("common.remove") ||
                                                        "Remove"
                                                    }}
                                                </button>
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                </template>
                            </template>
                            <TableRow v-else>
                                <TableCell
                                    colspan="3"
                                    class="py-16 sm:py-24 text-center"
                                >
                                    <p
                                        class="text-lg sm:text-xl font-bold uppercase tracking-widest text-muted-foreground wrap-break-word text-balance px-4"
                                    >
                                        {{
                                            $t("components.cart.empty") ||
                                            "Your cart is empty"
                                        }}
                                    </p>
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </div>

            <div class="lg:col-span-4">
                <div
                    class="border-4 sm:border-[6px] border-main p-6 sm:p-8 space-y-6 sm:space-y-8 bg-background sticky top-24"
                >
                    <div class="space-y-4">
                        <h2
                            class="text-[10px] sm:text-xs font-black uppercase tracking-[0.2em] sm:tracking-[0.3em] text-muted-foreground border-b-2 border-border pb-2 wrap-break-word"
                        >
                            {{ $t("common.summary") || "Order Summary" }}
                        </h2>

                        <div class="space-y-2">
                            <div
                                class="flex flex-col sm:flex-row justify-between items-start sm:items-center text-lg sm:text-xl font-bold uppercase italic gap-1"
                            >
                                <span class="wrap-break-word">{{
                                    $t("common.total") || "Total"
                                }}</span>
                                <span class="font-mono whitespace-nowrap"
                                    >{{ cartTotalPrice.toFixed(2) }}
                                    {{ $currency }}</span
                                >
                            </div>
                            <p
                                v-html="taxAndShippingInfo"
                                class="text-[9px] sm:text-[10px] font-bold uppercase tracking-widest text-muted-foreground leading-relaxed wrap-break-word text-balance"
                            ></p>
                        </div>
                    </div>

                    <div class="pt-4 space-y-3 sm:space-y-4 flex flex-col">
                        <Button
                            v-if="cartState.cartItems.length > 0"
                            as-child
                            class="view-all w-full h-16 sm:h-20 text-lg sm:text-xl whitespace-normal h-auto py-4 text-center"
                        >
                            <router-link
                                to="/checkout"
                                class="view-all flex flex-wrap justify-center items-center gap-2"
                            >
                                <LockKeyhole :size="20" class="shrink-0" />
                                <span class="wrap-break-word">{{
                                    $t("components.cart.checkout")
                                }}</span>
                            </router-link>
                        </Button>

                        <Button
                            as-child
                            variant="outline"
                            class="view-all w-full whitespace-normal h-auto py-4 text-center"
                        >
                            <router-link
                                to="/collections"
                                class="wrap-break-word"
                            >
                                {{ $t("components.buttons.weiterKaufen") }}
                            </router-link>
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </PageLayout>
</template>

<script setup>
import {
    cartState,
    cartTotalPrice,
    updateItemQuantity,
    removeFromCart,
    validateCart,
} from "@lib/store/shop/index.js";
import { useI18n } from "vue-i18n";
import { computed, onMounted } from "vue";
import { LockKeyhole } from "lucide-vue-next";

// components
import PageLayout from "@components/shop/general/PageLayout.vue";
import CustomNumberInput from "@ui/CustomNumberInput.vue";
import { Button } from "@/components/ui/button";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";

const { t } = useI18n();

onMounted(async () => {
    await validateCart();
});

const taxAndShippingInfo = computed(() => {
    return `${t("components.cart.taxIncluded")} ${t("components.cart.shippingCalc")}`;
});
</script>
