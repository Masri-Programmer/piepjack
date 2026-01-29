<template>
  <div
    aria-label="Loading..."
    role="status"
    class="flex items-center space-x-2"
    :class="center && 'grid place-content-center h-full'"
  >
    <svg
      :class="[`animate-spin stroke-white`, sizeClass]"
      viewBox="0 0 256 256"
    >
      <line
        x1="128"
        y1="32"
        x2="128"
        y2="64"
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="24"
      />
      <line
        x1="195.9"
        y1="60.1"
        x2="173.3"
        y2="82.7"
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="24"
      />
      <line
        x1="224"
        y1="128"
        x2="192"
        y2="128"
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="24"
      />
      <line
        x1="195.9"
        y1="195.9"
        x2="173.3"
        y2="173.3"
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="24"
      />
      <line
        x1="128"
        y1="224"
        x2="128"
        y2="192"
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="24"
      />
      <line
        x1="60.1"
        y1="195.9"
        x2="82.7"
        y2="173.3"
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="24"
      />
      <line
        x1="32"
        y1="128"
        x2="64"
        y2="128"
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="24"
      />
      <line
        x1="60.1"
        y1="60.1"
        x2="82.7"
        y2="82.7"
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="24"
      />
    </svg>
  </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
  center: { type: Boolean, default: false },
  size: { type: String, default: "md" }, // small, medium, large
});

const sizeClass = computed(() => {
  return {
    xs: "h-6 w-6",
    sm: "h-8 w-8",
    md: "h-12 w-12",
    lg: "h-16 w-16",
    xl: "h-20 w-20",
  }[props.size] || "h-12 w-12"; // Default to medium size
});
</script>
