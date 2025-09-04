<template>
    <div class="w-full min-h-full bg-accent">
        <Navigation />
        <main
            class="grid min-h-screen gap-8 p-4 pt-16 overflow-x-hidden tracking-normal md:p-8 2xl:p-16 md:gap-8 2xl:gap-16 bg-accent"
        >
            <router-view />
        </main>
    </div>
  
    <Cookies />
    <WhatsappIcon />
    <Footer />
</template>
<script setup>
import AOS from "aos";
import "aos/dist/aos.css";
import { onMounted } from "vue";
import Footer from "./Footer.vue";
import Cookies from "./Cookies.vue";
import Navigation from "./Navigation.vue";
import WhatsappIcon from "@components/shop/Icons/WhatsappIcon.vue";

onMounted(() => {
    AOS.init();
});
</script>