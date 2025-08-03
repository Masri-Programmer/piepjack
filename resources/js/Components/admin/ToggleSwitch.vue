<template>
  <div
    @click="toggle"
    class="flex items-center justify-center cursor-pointer cm-toggle-wrapper"
  >
    <div :class="activeClass" class="rounded-full w-8 h-4 p-0.5">
      <div
        :class="circleClass"
        class="rounded-full w-3 h-3 bg-main transform mx-auto duration-300 ease-in-out"
      ></div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
const props = defineProps({
  modelValue: {
    type: Boolean,
    required: true,
  },
});

const emits = defineEmits(["update:toggle"]);

const toggle = () => {
  emits("update:toggle", !props.modelValue);
};

const activeClass = computed(() =>
  props.modelValue ? "bg-green-500" : "bg-red-500"
);

const circleClass = computed(() =>
  props.modelValue ? "translate-x-2" : "-translate-x-2"
);
</script>
