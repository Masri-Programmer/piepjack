<template>
  <div  class="accordion-container">
    <!-- <Icon :name="iconName" strokeWidth="1px" size="18" class="opacity-65" /> -->
    <label class="accordion-label" :for="id">{{ label }}</label>
    <input class="accordion-input peer" type="checkbox" :name="id" :id="id" />
    <svg
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
      stroke-width="1"
      stroke="currentColor"
      class="accordion-icon"
    >
      <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6" />
    </svg>
    <div v-if="content" class="accordion-content -transparent" v-html="content"></div>
    <div class="accordion-content -transparent">
      <slot name="content" ></slot>
    </div>
  </div>
</template>

<script setup>
// import Icon  from "@lib/components/Icon.vue";
import "@assets/css/accordion.css";

const props = defineProps({
  id: String,
  label: String,
  content: String,
  iconName: [Object, String],
});

</script>