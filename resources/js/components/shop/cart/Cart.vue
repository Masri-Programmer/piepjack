<template>
    <div>
        <!-- Overlay Background -->
        <div v-show="cartState.open" class="cart-overlay">
            <transition name="fade">
                <div
                    v-show="cartState.open"
                    class="overflow-hidden cart-overlay-background"
                ></div>
            </transition>

            <!-- Cart Content -->
            <section class="cart-content">
                <transition name="slide" :duration="550">
                    <div v-show="cartState.open" ref="Cart" class="cart-panel">
                        <!-- Cart Header -->
                        <div class="cart-header">
                            <h2 class="cart-title">
                                {{ $t("components.cart.title") }}
                            </h2>
                            <button @click="$emit('close')">
                                <span class="sr-only">{{
                                    $t("components.cart.close")
                                }}</span>
                                <X strokeWidth="1" />
                            </button>
                        </div>

                        <!-- Cart Content -->
                        <div class="cart-body">
                            <transition name="fade">
                                <div v-if="cartTotalPrice() >= 100">
                                    <div class="cart-eligible">
                                        <CircleCheckBig size="20" />
                                        <div class="cart-eligible-details">
                                            <h3 class="cart-eligible-title">
                                                {{
                                                    $t(
                                                        "components.cart.guteWahl",
                                                    )
                                                }}
                                            </h3>
                                            <p>
                                                {{
                                                    $t(
                                                        "components.cart.yourItems",
                                                    )
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                    <!-- Free Shipping Bar -->
                                    <div class="cart-shipping">
                                        <div class="cart-shipping-info">
                                            <span
                                                ><strong>{{
                                                    $t(
                                                        "components.cart.freeShipping",
                                                    )
                                                }}</strong></span
                                            >
                                            <div class="cart-shipping-icon">
                                                <hr
                                                    class="cart-shipping-divider"
                                                />
                                                <span
                                                    class="cart-shipping-circle"
                                                >
                                                    <Circle
                                                        size="18"
                                                        fill="currentColor"
                                                    />
                                                </span>
                                                <Truck />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </transition>
                            <img
                                loading="lazy"
                                :src="emptyCart"
                                alt="empty_cart"
                                class="cart-empty"
                                v-if="!cartState.cartItems.length"
                            />
                            <!-- Product List -->
                            <template v-for="product in cartState.cartItems">
                                <template v-for="item in product.items">
                                    <ProductSmallCard
                                        :product="product"
                                        :item="item"
                                        :options="{
                                            addToCart: true,
                                        }"
                                    />
                                </template>
                            </template>
                            <button
                                v-if="cartState.cartItems.length"
                                class="cart-clear-button"
                                @click="confirmClearCart"
                            >
                                <Trash2 size="20" />
                            </button>

                            <!-- Complementary Products -->
                        </div>

                        <!-- Cart Notice -->
                        <p class="cart-notice">
                            <Info strokeWidth="1" size="16" />
                            {{ $t("components.cart.notReserved") }}
                        </p>

                        <!-- Cart Footer -->
                        <div class="cart-footer">
                            <div class="cart-total">
                                <div class="cart-total-label">
                                    <h6>
                                        <strong>{{
                                            $t("components.cart.total")
                                        }}</strong>
                                    </h6>
                                    <p class="cart-total-tax">
                                        {{ $t("components.cart.inclTax") }}
                                    </p>
                                </div>
                                <div>
                                    {{ cartTotalPrice() }} {{ $currency }}
                                </div>
                            </div>
                            <router-link
                                to="/checkout"
                                v-if="cartState.cartItems.length > 0"
                                @click="
                                    () => {
                                        if (cartState.open)
                                            cartState.open = false;
                                    }
                                "
                                class="cart-checkout-button view-all jello-horizontal"
                            >
                                <LockKeyhole size="18" />
                                {{ $t("components.buttons.sicherZahlen") }}
                            </router-link>
                            <router-link
                                v-else
                                to="/collections"
                                @click="
                                    () => {
                                        if (cartState.open)
                                            cartState.open = false;
                                    }
                                "
                                class="cart-continue-shopping view-all"
                            >
                                {{ $t("components.buttons.weiterKaufen") }}
                            </router-link>

                            <!-- Payment Methods -->
                            <p class="cart-payment-methods">
                                <PaypalIcon />
                                <!-- <GooglePayIcon /> -->
                                <!-- <KlarnaIcon /> -->
                                <VisaIcon />
                                <SofortIcons />
                            </p>
                        </div>
                    </div>
                </transition>
            </section>
        </div>
    </div>
</template>

<script setup>
import {
    X,
    Info,
    Circle,
    Trash2,
    LockKeyhole,
    CircleCheckBig,
} from "lucide-vue-next";
import {
    cartState,
    updateItemKey,
    cartTotalPrice,
    updateProductKey,
} from "@lib/store/shop/index.js";
import axios from "axios";
import { useI18n } from "vue-i18n";
import Truck from "../Icons/Truck.vue";
import PaypalIcon from "../Icons/Paypal.vue";
import VisaIcon from "../Icons/VisaIcon.vue";
import { useArea } from "@lib/useArea.js";
import { onClickOutside } from "@vueuse/core";
import { useToast } from "vue-toastification";
import { ref, onMounted, watch } from "vue";
import KlarnaIcon from "../Icons/KlarnaIcon.vue";
import SofortIcons from "../Icons/SofortIcons.vue";
import GooglePayIcon from "../Icons/GooglePayIcon.vue";
import emptyCart from "@img/svg/empty_cart.svg";
import ProductSmallCard from "../product/ProductSmallCard.vue";
import "@assets/css/cart/cart.css";

const { getApiUrl } = useArea();
const Cart = ref(null);
const emit = defineEmits(["close"]);
onClickOutside(Cart, () => {
    if (cartState.value.open) {
        cartState.value.open = false;
    }
});

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
                    //removeProductFromCart( cartProduct.id); // Optional: remove the invalid product
                    continue; // Skip to the next cart product
                }
                // Update product details
                Object.keys(validProduct).forEach((key) => {
                    if (
                        Object.prototype.hasOwnProperty.call(
                            cartProduct,
                            key,
                        ) &&
                        key !== "items"
                    ) {
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

                            // Update cart item with valid details
                            Object.keys(validItem).forEach((key) => {
                                if (
                                    Object.prototype.hasOwnProperty.call(
                                        cartItem,
                                        key,
                                    )
                                ) {
                                    updateItemKey({
                                        productId: cartProduct.id,
                                        itemId: cartItem.id,
                                        key,
                                        value: validItem[key],
                                    });
                                }
                            });
                            return cartItem; // Return valid cart item
                        })
                        .filter(Boolean); // Remove null items
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

watch(
    () => cartState.value.open,
    (isOpen) => {
        if (isOpen) {
            document.body.style.overflow = "hidden";
        } else {
            document.body.style.overflow = ""; // Or 'auto'
        }
    },
);
</script>
