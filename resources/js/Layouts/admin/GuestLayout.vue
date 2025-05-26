<template>
  <div
    class="min-h-full flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8"
  >
    <div class="max-w-md w-full space-y-8">
      <div>
        <h2 class="text-3xl text-main font-extrabold mb-3">
          {{ title }}
        </h2>
      </div>
      <slot></slot>
    </div>
  </div>
</template>

<script setup>
const { title } = defineProps({
  title: String,
});
</script>
