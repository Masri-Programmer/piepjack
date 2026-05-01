<template><router-view /></template>

<script setup>
import { isDark } from "@lib/store/theme";
import { useShopGlobalState } from "@lib/store/shop/shop";

// Initialize global state at the root level
useShopGlobalState();
</script>
