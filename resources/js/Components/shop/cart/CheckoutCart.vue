<template>
  <div class="checkout-cart-container">
    <template v-for="product in cartState.cartItems">
      <template v-for="item in product.items">
        <ProductSmallCard :product="product" :item="item" :label="'no-label'" />
      </template>
    </template>
  </div>

  <div class="checkout-cart-summary">
    <div class="checkout-cart-subtotal">
      <p>
        {{ $t("pages.checkout.subtotal", { itemCount: cartTotalQuantity() }) }}
      </p>
      <p>{{ cartTotalPrice() }} {{ $currency }}</p>
    </div>
    <div class="checkout-cart-shipping">
      <p>{{ $t("pages.checkout.shipping") }}</p>
      <p v-if="cartTotalPrice() >= 100 || freeShipping">
        {{ $t("pages.checkout.free") }}
      </p>
      <p v-else>5.90 {{ $currency }}</p>
    </div>

    <!-- Promo Code Section -->
    <div class="checkout-cart-promo">
      <label for="promoCode" class="checkout-cart-promo-label">
        {{ $t("pages.checkout.promoCode") }}
      </label>
      <div class="checkout-cart-promo-input-wrapper">
        <input
          type="text"
          id="promoCode"
          v-model="promoCode"
          class="checkout-cart-promo-input"
          :placeholder="$t('pages.checkout.enterPromoCode')"
        />
        <button
          @click="applyPromoCode"
          class="checkout-cart-promo-button view-all text-xs"
        >
          {{ $t("pages.checkout.apply") }}
        </button>
      </div>
      <p v-if="promoApplied" class="checkout-cart-promo-success">
        {{ $t("pages.checkout.promoApplied") }}
      </p>
      <p v-if="promoError" class="checkout-cart-promo-error">
        {{ $t("pages.checkout.invalidPromo") }}
      </p>
    </div>

    <div class="checkout-cart-total">
      <div class="checkout-cart-total-value">
        <p class="text-xl font-bold">
          <strong>{{ $t("pages.checkout.total") }}</strong>
        </p>
        <p>
          {{ calculateFinalPrice() }}
          {{ $currency }}
        </p>
      </div>
      <p class="checkout-cart-tax">
        {{ $t("pages.checkout.includingTaxes", { taxAmount: "19%" }) }}
      </p>
    </div>
  </div>
</template>

<script setup>
import ProductSmallCard from "../product/ProductSmallCard.vue";
import { reactive, computed, ref, onBeforeMount } from "vue";
import "@assets/css/checkout/checkoutCart.css";
import { loadStripe } from "@stripe/stripe-js";
import {
  cartState,
  cartTotalPrice,
  cartTotalQuantity,
} from "@lib/store/shop/index.js";

const coupon = reactive(null);
const promoCode = ref("");
const promoDiscount = ref(0);
const promoApplied = ref(false);
const promoError = ref(false);
const freeShipping = ref(false);

const applyPromoCode = () => {
  promoApplied.value = false;
  promoError.value = false;
  promoDiscount.value = 0;
  freeShipping.value = false;

  if (promoCode.value && promoCode.value.trim() !== "") {
    if (promoCode.value.trim().toLowerCase() === "pickup") {
      cartState.value.promoCode = "pickup";
      promoApplied.value = true;
      freeShipping.value = true;
    } else {
      promoApplied.value = false;
      promoDiscount.value = 10;
    }
  } else {
    promoError.value = true;
  }
};

const calculateFinalPrice = () => {
  let price = cartTotalPrice();

  if (price < 100 && !freeShipping.value) {
    price += 5.9;
  }

  if (promoApplied.value && promoDiscount.value > 0) {
    price = Math.max(0, price - promoDiscount.value);
  }

  return price.toFixed(2);
};

onBeforeMount(() => {
  cartState.value.promoCode = "";
  const stripeLoaded = ref(false);
  const stripePromise = loadStripe(
    "pk_test_51Q6bWxP4hoAjrgc6miCYAr7rLP9JnfLtuI8n4dAD6jph6nSEiPtw8oraXNzWmGd1rITIsTIJdcJ77DuGQGExXTJj00FvJsHgE0"
  );
  stripePromise.then(() => {
    stripeLoaded.value = true;
  });
});
</script>