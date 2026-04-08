<template>
    <div class="w-full min-h-full bg-background">
        <Navigation />
        <main class="min-h-screen bg-background">
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
