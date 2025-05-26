<template>
  <div class="min-h-full w-full bg-accent">
    <Navigation />
    <main
      class="p-4 pt-16 md:p-8 2xl:p-16 grid gap-8 md:gap-8 2xl:gap-16 bg-accent tracking-normal min-h-screen overflow-x-hidden"
    >
      <router-view />
    </main>
  </div>
  <Cookies />
  <Footer />
</template>
<script setup>
import AOS from "aos";
import "aos/dist/aos.css";
import { onMounted } from "vue";
import Footer from "./Footer.vue";
import Cookies from "./Cookies.vue";
import Navigation from "./Navigation.vue";

onMounted(() => {
  AOS.init();
});
</script>
