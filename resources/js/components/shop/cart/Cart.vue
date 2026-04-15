<template>
    <Sheet v-model:open="cartState.open">
        <SheetContent
            class="flex flex-col w-full p-0 sm:max-w-md bg-accent_dark"
        >
            <SheetHeader class="p-4 border-b">
                <SheetTitle class="text-xl text-left">{{
                    $t("components.cart.title")
                }}</SheetTitle>
            </SheetHeader>

            <div class="flex-1 p-4 overflow-y-auto space-y-6">
                <div
                    v-if="cartTotalPrice >= 100"
                    class="flex flex-col gap-3 p-4 border bg-muted/50 text-card-foreground"
                >
                    <div class="flex items-start gap-3">
                        <CircleCheckBig class="text-primary mt-0.5" size="20" />
                        <div>
                            <h3 class="text-sm font-semibold">
                                {{ $t("components.cart.guteWahl") }}
                            </h3>
                            <p class="text-xs text-muted-foreground">
                                {{ $t("components.cart.yourItems") }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-2 text-sm">
                        <span class="font-medium">{{
                            $t("components.cart.freeShipping")
                        }}</span>
                        <div class="flex items-center gap-2 text-primary">
                            <Separator class="w-8 sm:w-12 bg-primary/30" />
                            <Circle
                                size="12"
                                fill="currentColor"
                                class="text-primary"
                            />
                            <Truck size="18" />
                        </div>
                    </div>
                </div>

                <div
                    v-if="!cartState.cartItems.length"
                    class="flex flex-col items-center justify-center py-10 opacity-70"
                >
                    <img
                        loading="lazy"
                        :src="emptyCart"
                        alt="empty_cart"
                        class="w-40 h-40 mb-6"
                    />
                </div>

                <div class="space-y-4">
                    <template
                        v-for="product in cartState.cartItems"
                        :key="product.id"
                    >
                        <template v-for="item in product.items" :key="item.id">
                            <ProductSmallCard
                                :product="product"
                                :item="item"
                                :options="{ addToCart: true }"
                            />
                        </template>
                    </template>
                </div>

                <div v-if="cartState.cartItems.length" class="flex justify-end">
                    <Button
                        variant="ghost"
                        size="icon"
                        class="text-destructive hover:text-destructive hover:bg-destructive/10"
                        @click="confirmClearCart"
                    >
                        <Trash2 size="18" />
                    </Button>
                </div>

                <p class="flex items-start gap-2 text-xs text-muted-foreground">
                    <Info class="shrink-0 mt-0.5" size="16" />
                    {{ $t("components.cart.notReserved") }}
                </p>
            </div>

            <SheetFooter
                class="flex flex-col gap-4 p-4 mt-auto border-t sm:flex-col"
            >
                <div class="flex items-end justify-between w-full">
                    <div class="flex flex-col">
                        <h6 class="text-lg font-bold">
                            {{ $t("components.cart.total") }}
                        </h6>
                        <p class="text-xs text-muted-foreground">
                            {{ $t("components.cart.inclTax") }}
                        </p>
                    </div>
                    <div class="text-xl font-bold">
                        {{ cartTotalPrice }} {{ $currency }}
                    </div>
                </div>

                <div class="w-full">
                    <router-link
                        v-if="cartState.cartItems.length > 0"
                        to="/checkout"
                        @click="cartState.open = false"
                        class="block w-full"
                    >
                        <Button class="view-all" size="lg">
                            <LockKeyhole size="18" />
                            {{ $t("components.buttons.sicherZahlen") }}
                        </Button>
                    </router-link>

                    <router-link
                        v-else
                        to="/collections"
                        @click="cartState.open = false"
                        class="block w-full"
                    >
                        <Button variant="outline" class="view-all" size="lg">
                            {{ $t("components.buttons.weiterKaufen") }}
                        </Button>
                    </router-link>
                </div>

                <div
                    class="flex items-center justify-center gap-3 pt-2 text-muted-foreground opacity-80"
                >
                    <PaypalIcon class="w-auto h-5" />
                    <VisaIcon class="w-auto h-5" />
                    <SofortIcons class="w-auto h-5" />
                </div>
            </SheetFooter>
        </SheetContent>
    </Sheet>
</template>

<script setup>
import { ref, onMounted } from "vue";
import axios from "axios";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { useArea } from "@lib/useArea.js";
import {
    cartState,
    updateItemKey,
    cartTotalPrice,
    updateProductKey,
} from "@lib/store/shop/index.js";

import {
    Sheet,
    SheetContent,
    SheetHeader,
    SheetTitle,
    SheetFooter,
} from "@/components/ui/sheet";
import { Button } from "@/components/ui/button";
import { Separator } from "@/components/ui/separator";

// Icons and Assets
import {
    Info,
    Circle,
    Trash2,
    LockKeyhole,
    CircleCheckBig,
} from "lucide-vue-next";
import Truck from "../Icons/Truck.vue";
import PaypalIcon from "../Icons/Paypal.vue";
import VisaIcon from "../Icons/VisaIcon.vue";
import KlarnaIcon from "../Icons/KlarnaIcon.vue";
import SofortIcons from "../Icons/SofortIcons.vue";
import GooglePayIcon from "../Icons/GooglePayIcon.vue";
import emptyCart from "@img/svg/empty_cart.svg";
import ProductSmallCard from "../product/ProductSmallCard.vue";

const { getApiUrl } = useArea();
const emit = defineEmits(["close"]);
const toast = useToast();
const { t } = useI18n();

const confirmClearCart = () => {
    if (window.confirm(t("components.cart.clearCart"))) {
        cartState.value.cartItems = [];
    }
};

// Validating Cart Items
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

                // Update product details from validProduct
                Object.keys(validProduct).forEach((key) => {
                    if (key !== "items") {
                        updateProductKey({
                            productId: cartProduct.id,
                            key,
                            value: validProduct[key],
                        });
                    }
                });

                // Validate and update items within the product
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

                            // Update cart item with valid details from validItem
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
</script>
