<template>
  <div class="min-h-full">
    <NavSidebar />
    <main
      class="grid gap-4 md:gap-8 2xl:gap-16 bg-accent tracking-normal h-screen ps-12 md:ps-20 overflow-hidden"
    >
      <div class="p-3 overflow-auto">
        <router-view />
      </div>
    </main>
  </div>
</template>

<script setup>
import NavSidebar from "./NavSidebar.vue";
</script>
