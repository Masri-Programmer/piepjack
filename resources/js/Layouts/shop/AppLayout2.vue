<template>
  <div class="min-h-full">
    <main
      class="p-4 md:p-8 2xl:p-16 grid gap-4 md:gap-8 2xl:gap-16 bg-accent tracking-normal min-h-screen"
    >
      <router-view />
    </main>
  </div>
  <Cookies />
</template>

<script setup>
import Cookies from "./Cookies.vue";
</script>
