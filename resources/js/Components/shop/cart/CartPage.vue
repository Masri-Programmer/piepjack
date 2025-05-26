<template>
<div class="cart-page">
  <h1 class="cart-page-title">
    {{ $t("components.cart.title") }}
  </h1>
  <div class="cart-page-shipping-info">
    <span class="cart-page-shipping-text">
      <strong v-html="$t('components.cart.shippingFree')"></strong>
    </span>
  </div>
  <div class="cart-page-container">
    <table class="cart-page-table">
      <thead class="cart-page-table-header">
        <tr class="cart-page-table-row">
          <th class="cart-page-table-cell">
            <p class="cart-page-table-header-text">
              {{ $t("components.cart.products") }}
            </p>
          </th>
          <th class="cart-page-table-cell">
            <p class="cart-page-table-header-text">
              {{ $t("components.cart.quantity") }}
            </p>
          </th>
          <th class="cart-page-table-cell cart-page-table-total">
            <p class="cart-page-table-header-text">
              {{ $t("components.cart.total") }}
            </p>
          </th>
        </tr>
      </thead>
      <tbody>
        <template v-for="product in cartState.cartItems">
          <template v-for="item in product.items">
            <tr class="cart-page-item-row">
              <CartgProduct :product="product" :item="item" />
            </tr>
          </template>
        </template>
      </tbody>
    </table>
    <div class="cart-page-summary">
      <div class="cart-page-summary-content">
        <p>
          {{ $t("components.cart.totalAmount", { total: cartTotalPrice() }) }}
          {{ $currency }}
        </p>
        <p v-html="taxAndShippingInfo" class="cart-page-summary-info"></p>
        <router-link
          to="/checkout"
          v-if="cartState.cartItems.length > 0"
          class="cart-page-checkout-button view-all jello-horizontal"
        >
          <LockKeyhole size="18" />
          {{ $t("components.cart.checkout") }}
        </router-link>
        <router-link
          v-else
          to="/collections"
          @click="
            () => {
              if (props.open) emit('close');
            }
          "
          class="cart-page-continue-shopping view-all"
        >
          {{ $t("components.buttons.weiterKaufen") }}
        </router-link>
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
import "@assets/css/cart/cartPage.css";
import { useArea } from '@lib/useArea.js'
const {  getApiUrl } = useArea();

const toast = useToast();
const { t } = useI18n();

// Validating Cart Items
onMounted(async () => {
  if (cartState.value.cartItems.length) {
    for (const cartProduct of cartState.value.cartItems) {
      try {
        const response = await axios.get(
          `${getApiUrl()}/products/${cartProduct.id}`
        );
        const validProduct = response.data.data;

        if (!validProduct) {
          toast.warning(t("cart.product_invalid", { id: cartProduct.id }));
          // removeProductFromCart(cartProduct.id); // Optional: remove the invalid product
          continue; // Skip to the next cart product
        }
        // Update product details
        Object.keys(validProduct).forEach((key) => {
          if (
            Object.prototype.hasOwnProperty.call(cartProduct, key) &&
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
                (item) => item.id === cartItem.id
              );

              if (!validItem) {
                toast.warning(t("components.cart.item_invalid", { id: cartItem.id }));
                return null;
              }

              // Update cart item with valid details
              Object.keys(validItem).forEach((key) => {
                if (Object.prototype.hasOwnProperty.call(cartItem, key)) {
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
          error.message
        );
      }
    }
  }
});

const taxAndShippingInfo = computed(() => {
  return `${t("components.cart.taxIncluded")} ${t("components.cart.shippingCalc")}`;
});
</script>
