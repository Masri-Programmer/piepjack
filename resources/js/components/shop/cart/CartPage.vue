<template>
    <div class="max-w-7xl mx-auto px-4 py-10 space-y-8">
        <header class="space-y-4">
            <h1 class="text-3xl font-accent_dark uppercase tracking-tighter">
                {{ $t("components.cart.title") }}
            </h1>
            <div class="bg-accent_light border border-muted p-4 rounded-none">
                <span class="text-sm tracking-tight">
                    <strong
                        v-html="$t('components.cart.shippingFree')"
                    ></strong>
                </span>
            </div>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <div class="lg:col-span-2">
                <Table class="rounded-none border-b border-muted">
                    <TableHeader class="rounded-none bg-accent_light">
                        <TableRow class="rounded-none border-b border-gray-100">
                            <TableHead
                                class="rounded-none font-medium text-main"
                            >
                                {{ $t("components.cart.products") }}
                            </TableHead>
                            <TableHead
                                class="rounded-none text-center text-main"
                            >
                                {{ $t("components.cart.quantity") }}
                            </TableHead>
                            <TableHead
                                class="rounded-none text-right font-bold text-main"
                            >
                                {{ $t("components.cart.total") }}
                            </TableHead>
                        </TableRow>
                    </TableHeader>

                    <TableBody>
                        <template
                            v-for="product in cartState.cartItems"
                            :key="product.id"
                        >
                            <TableRow
                                v-for="item in product.items"
                                :key="item.id"
                                class="hover:bg-accent_light transition-colors"
                            >
                                <TableCell class="rounded-none font-medium">
                                    <div class="flex items-center gap-4">
                                        <img
                                            :src="product?.image"
                                            class="w-16 h-16 object-cover rounded-none"
                                        />
                                        <span>{{ product?.name }}</span>
                                    </div>
                                </TableCell>

                                <TableCell class="rounded-none text-center">
                                    {{ item?.quantity }}
                                </TableCell>

                                <TableCell
                                    class="rounded-none text-right font-bold"
                                >
                                    {{ item?.price }} {{ $currency }}
                                </TableCell>
                            </TableRow>
                        </template>
                    </TableBody>
                </Table>
            </div>

            <div class="lg:col-span-1">
                <div
                    class="border border-accent_dark p-6 space-y-6 rounded-none shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]"
                >
                    <div class="space-y-2">
                        <h2
                            class="text-xs font-bold uppercase tracking-widest text-muted-foreground"
                        >
                            Summary
                        </h2>
                        <p class="text-2xl font-accent_dark uppercase">
                            {{
                                $t("components.cart.totalAmount", {
                                    total: cartTotalPrice,
                                })
                            }}
                            {{ $currency }}
                        </p>
                        <p
                            v-html="taxAndShippingInfo"
                            class="text-xs text-gray-600 leading-relaxed"
                        ></p>
                    </div>

                    <div class="pt-4">
                        <Button
                            v-if="cartState.cartItems.length > 0"
                            as-child
                            class="w-full rounded-none bg-primary text-primary-foreground hover:bg-gray-800 py-8 text-lg font-bold uppercase tracking-tighter"
                        >
                            <router-link
                                to="/checkout"
                                class="flex items-center justify-center gap-3"
                            >
                                <LockKeyhole :size="20" />
                                {{ $t("components.cart.checkout") }}
                            </router-link>
                        </Button>

                        <Button
                            v-else
                            as-child
                            variant="outline"
                            class="w-full rounded-none border-accent_dark hover:bg-accent_dark hover:text-primary-foreground py-8 text-lg font-bold uppercase tracking-tighter"
                        >
                            <router-link
                                to="/collections"
                                @click="
                                    () => {
                                        if (props.open) emit('close');
                                    }
                                "
                            >
                                {{ $t("components.buttons.weiterKaufen") }}
                            </router-link>
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import {
    cartState,
    cartTotalPrice,
    updateProductKey,
    updateItemKey,
} from "@lib/store/shop/index.js";
import axios from "axios";
import { useI18n } from "vue-i18n";
import CartgProduct from "./CartProduct.vue";
import { useToast } from "vue-toastification";
import { computed, onMounted } from "vue";
import { LockKeyhole } from "lucide-vue-next";

// Shadcn UI components
import { TableCell, TableRow } from "@/components/ui/table";
import { Button } from "@/components/ui/button";
import {
    Table,
    TableBody,
    TableHead,
    TableHeader,
} from "@/components/ui/table";

import { useArea } from "@lib/useArea.js";
const { getApiUrl } = useArea();

const toast = useToast();
const { t } = useI18n();

// Props/Emits for the modal close logic
const props = defineProps({ open: Boolean });
const emit = defineEmits(["close"]);

// Validation logic remains unchanged to ensure data integrity
onMounted(async () => {
    if (cartState.value.cartItems.length) {
        for (const cartProduct of cartState.value.cartItems) {
            try {
                const response = await axios.get(
                    `${getApiUrl()}/products/${cartProduct.id}`,
                );
                const validProduct = response.data.data;

                if (!validProduct) {
                    toast.warning(
                        t("cart.product_invalid", { id: cartProduct.id }),
                    );
                    continue;
                }

                Object.keys(validProduct).forEach((key) => {
                    if (key !== "items") {
                        updateProductKey({
                            productId: cartProduct.id,
                            key,
                            value: validProduct[key],
                        });
                    }
                });

                if (cartProduct.items) {
                    cartProduct.items = cartProduct.items
                        .map((cartItem) => {
                            const validItem = validProduct.items.find(
                                (item) => item.id === cartItem.id,
                            );
                            if (!validItem) {
                                toast.warning(
                                    t("components.cart.item_invalid", {
                                        id: cartItem.id,
                                    }),
                                );
                                return null;
                            }
                            Object.keys(validItem).forEach((key) => {
                                updateItemKey({
                                    productId: cartProduct.id,
                                    itemId: cartItem.id,
                                    key,
                                    value: validItem[key],
                                });
                            });
                            return cartItem;
                        })
                        .filter(Boolean);
                }
            } catch (error) {
                console.error(
                    `Error validating product ${cartProduct.id}:`,
                    error.message,
                );
            }
        }
    }
});

const taxAndShippingInfo = computed(() => {
    return `${t("components.cart.taxIncluded")} ${t("components.cart.shippingCalc")}`;
});
</script>
