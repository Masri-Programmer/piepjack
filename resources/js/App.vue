<template><router-view /></template>

<script setup>
import { useDark } from "@vueuse/core";
import { useShopGlobalState } from "@lib/store/shop/shop";

// Initialize theme globally
useDark();

// Initialize global state at the root level
useShopGlobalState();
</script>
