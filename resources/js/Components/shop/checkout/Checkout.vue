<template>
  <div class="checkout-container">
    <div class="checkout-form-container">
      <div class="checkout-form-content">
        <router-link to="/">
          <img :src="logo" alt="logo" class="checkout-logo" />
        </router-link>
        <Breadcrumbs :data="links" class="checkout-breadcrumbs" />
        <ContactForm class="checkout-contact-form" />
      </div>
    </div>
    <div class="checkout-cart-container">
      <CheckoutCart />
    </div>
  </div>

  <div class="checkout-footer">
    <hr />
    <div class="checkout-footer-links">
      <router-link to="/widerrufsrecht" class="link-button">
        <span>{{ $t("layout.footer.widerrufsrecht") }}</span>
      </router-link>
      <router-link to="/versand" class="link-button">
        <span>{{ $t("layout.footer.versand") }}</span>
      </router-link>
      <router-link to="/datenschutzerklaerung" class="link-button">
        <span>{{ $t("layout.footer.datenschutzerklaerung") }}</span>
      </router-link>
      <router-link to="/agb" class="link-button">
        <span>{{ $t("layout.footer.agb") }}</span>
      </router-link>
      <router-link to="/impressum" class="link-button">
        <span>{{ $t("layout.footer.impressum") }}</span>
      </router-link>
    </div>
  </div>
</template>

<script setup>
import "@assets/css/checkout/checkout.css";
import logo from "@img/logo-new.png";
import ContactForm from "./ContactForm.vue";
import Breadcrumbs from "../../Breadcrumbs.vue";
import CheckoutCart from "../cart/CheckoutCart.vue";
import { ref } from "vue";

const links = ref([
  { title: "Warenkorb", link: "/cart" },
  {
    title: "Information",
    current: true,
    link: "/checkout",
  },
  {
    title: "Zahlung",
    link: "",
  },
]);
</script>
