<template>
  <div
    v-if="!cookiesAccepted"
    class="cookie-notification"
    aria-label="Cookie notification"
  >
    <div class="cookie-text">
      {{ $t("common.cookieNotification.message") }}
      <router-link to="/datenschutzerklarung" class="cookie-link">
        {{ $t("common.cookieNotification.learnMore") }}
      </router-link>
    </div>
    <button @click="acceptCookies" class="cookie-button">
      {{ $t("common.cookieNotification.understood") }}
    </button>
  </div>
</template>

<script setup>
import '@assets/css/cookies.css'
import { useStorage } from "@vueuse/core";

const cookiesAccepted = useStorage("cookiesAccepted", false);

const acceptCookies = () => {
  cookiesAccepted.value = true;
};
</script>
