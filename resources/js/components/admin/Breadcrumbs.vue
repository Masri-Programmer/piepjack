<template>
  <div class="breadcrumb-container">
    <div class="mb-3 py-2 breadcrumb flex flex-wrap items-center">
      <template v-for="(crumb, index) in data" :key="index">
        <div class="breadcrumb-item">
          <router-link
            :to="crumb.link"
            class="hover:text-gray uppercase"
            :class="crumb.current ? 'text-gray underline' : 'text-main'"
          >
            {{ crumb.title }}
          </router-link>
        </div>
        <ChevronRight v-if="index < data.length - 1" size="20" class="mx-2" />
      </template>
    </div>
  </div>
</template>

<script setup>
import { ChevronRight } from "lucide-vue-next";
const props = defineProps({ data: { type: Array } });
</script>
