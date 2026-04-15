<template>
    <PageLayout
        :title="$t('components.cart.title')"
        :description="$t('components.cart.shippingFree')"
    >
        <template #header>
            <div class="space-y-4 border-b-4 border-border pb-6">
                <h1
                    class="text-4xl md:text-6xl font-bold uppercase tracking-tighter italic"
                >
                    {{ $t("components.cart.title") }}
                </h1>
                <div
                    class="bg-accent_light border-2 border-border p-4 rounded-none w-fit"
                >
                    <span class="text-sm font-bold uppercase tracking-widest">
                        <strong
                            v-html="$t('components.cart.shippingFree')"
                        ></strong>
                    </span>
                </div>
            </div>
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16">
            <div class="lg:col-span-8">
                <div class="w-full overflow-x-auto">
                    <Table class="rounded-none border-2 border-border">
                        <TableHeader class="bg-muted/50">
                            <TableRow
                                class="border-b-2 border-border hover:bg-transparent"
                            >
                                <TableHead
                                    class="font-black uppercase tracking-widest text-xs text-foreground py-6"
                                >
                                    {{ $t("components.cart.products") }}
                                </TableHead>
                                <TableHead
                                    class="text-center font-black uppercase tracking-widest text-xs text-foreground py-6"
                                >
                                    {{ $t("components.cart.quantity") }}
                                </TableHead>
                                <TableHead
                                    class="text-right font-black uppercase tracking-widest text-xs text-foreground py-6"
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
                                        <TableCell class="py-6">
                                            <div
                                                class="flex items-center gap-6"
                                            >
                                                <div
                                                    class="w-20 h-20 bg-muted border-2 border-border flex-shrink-0"
                                                >
                                                    <img
                                                        :src="
                                                            item?.image_url ||
                                                            product?.image_url
                                                        "
                                                        class="w-full h-full object-cover"
                                                        :alt="product?.name"
                                                    />
                                                </div>
                                                <div class="space-y-1">
                                                    <p
                                                        class="font-bold uppercase tracking-tight text-lg"
                                                    >
                                                        {{ product?.name }}
                                                    </p>
                                                    <div
                                                        v-if="item?.options"
                                                        class="flex flex-wrap gap-2"
                                                    >
                                                        <span
                                                            v-for="opt in item.options"
                                                            :key="opt.name"
                                                            class="text-[10px] font-black uppercase tracking-widest bg-muted px-2 py-0.5 border border-border"
                                                        >
                                                            {{ opt.value }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </TableCell>

                                        <TableCell class="text-center py-6">
                                            <div class="flex justify-center">
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

                                        <TableCell class="text-right py-6">
                                            <div
                                                class="flex flex-col items-end gap-1"
                                            >
                                                <span
                                                    class="font-mono font-bold text-lg"
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
                                                    class="text-[10px] font-black uppercase tracking-widest text-destructive hover:underline"
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
                                    class="py-24 text-center"
                                >
                                    <p
                                        class="text-xl font-bold uppercase tracking-widest text-muted-foreground"
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
                    class="border-[6px] border-main p-8 space-y-8 bg-background sticky top-24"
                >
                    <div class="space-y-4">
                        <h2
                            class="text-xs font-black uppercase tracking-[0.3em] text-muted-foreground border-b-2 border-border pb-2"
                        >
                            {{ $t("common.summary") || "Order Summary" }}
                        </h2>

                        <div class="space-y-2">
                            <div
                                class="flex justify-between items-center text-xl font-bold uppercase italic"
                            >
                                <span>{{ $t("common.total") || "Total" }}</span>
                                <span class="font-mono"
                                    >{{ cartTotalPrice.toFixed(2) }}
                                    {{ $currency }}</span
                                >
                            </div>
                            <p
                                v-html="taxAndShippingInfo"
                                class="text-[10px] font-bold uppercase tracking-widest text-muted-foreground leading-relaxed"
                            ></p>
                        </div>
                    </div>

                    <div class="pt-4 space-y-4">
                        <Button
                            v-if="cartState.cartItems.length > 0"
                            as-child
                            class="view-all w-full h-20 text-xl"
                        >
                            <router-link to="/checkout" class="view-all">
                                <LockKeyhole :size="24" />
                                {{ $t("components.cart.checkout") }}
                            </router-link>
                        </Button>

                        <Button as-child variant="outline" class="view-all">
                            <router-link to="/collections">
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
