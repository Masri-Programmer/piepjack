<template>
  <div
    :class="[
      'center relative inline-block select-none whitespace-nowrap rounded-lg py-2 px-3.5 align-baseline text-sm font-bold leading-none text-white',
      colorClass,
    ]"
    data-dismissible="chip"
  >
    <div class="mr-5 mt-px"><slot></slot></div>
    <div
      :class="[
        'absolute top-1 right-1 mx-px mt-[0.5px] w-max rounded-md transition-colors',
        closeButtonClass,
      ]"
      data-dismissible-target="chip"
    >
      <div v-if="delete" role="button" class="h-5 w-5 p-1 grid place-content-center" @click="$emit('handle:delete')">
        <X size="16" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { X } from "lucide-vue-next";
import { defineProps, computed } from "vue";

const props = defineProps({
  colorClass: {
    type: String,
    default: "bg-linear-to-tr from-slate-600 to-slate-400",
  },
  closeButtonClass: {
    type: String,
    default: "bg-slate-400 hover:bg-slate-900",
  },
  delete: {
    type: Boolean,
    default: false,
  },
});
</script>
