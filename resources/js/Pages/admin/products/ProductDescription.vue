<template>
  <div class="max-h-32 overflow-auto">
    <div class="flex justify-between items-start">
      <div v-if="desc" v-html="desc"></div>
      <p v-else>No description</p>
      <button
        class="flex justify-center items-center size-8 text-slate-400 hover:text-slate-500 hover:bg-slate-100 rounded focus-visible:outline-none focus-visible:ring focus-visible:ring-indigo-300"
        @click="toggle(true)"
      >
        <SquarePen size="20" stroke-width="1.5" />
      </button>
    </div>
  </div>
  <Modal
    :open="value"
    @close="toggle(false)"
    class="bg-main rounded p-5 container"
  >
    <Editor
      :desc="desc"
      @handle:editor="(val) => $emit('handle:update', val)"
    />
  </Modal>
</template>

<script setup>
import Editor from "./Editor.vue";
import { useToggle } from "@vueuse/core";
import { SquarePen } from "lucide-vue-next";
import Modal from "@components/admin/Modal.vue";
defineEmits(['handle:update']);
 defineProps({
  desc: { type: String },
});
const [value, toggle] = useToggle();
</script>
