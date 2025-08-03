<template>
  <div v-if="isLoading" class="h-full"><Spinner :center="true" /></div>
  <div
    v-else-if="error"
    class="h-full grid place-content-center text-red-500 text-2xl"
  >
    {{ error }}
  </div>
  <div v-else>
    <Breadcrumbs :data="data" />
    <div class="flex justify-between items-center mb-3">
      <h1 class="text-3xl font-extrabold capitalize text-pretty whitespace-normal w-3/4 break-words">{{ title }}</h1>
      <button @click.stop="$emit('button:click')" v-if="button">
        <router-link
          :to="'/admin/'+button.link"
          v-if="!button.click"
          class="bg-main px-4 py-3 text-sm text-accent rounded-lg font-bold hover:bg-zinc-100"
        >
          {{ button.title }}
        </router-link>
        <div
          v-else
          class="bg-main px-4 py-3 text-sm text-accent rounded-lg font-bold hover:bg-zinc-100"
        >
          {{ button.title }}
        </div>
      </button>
    </div>
    <div class="flex flex-wrap -mx-3 mb-5">
      <div class="w-full max-w-full mb-6 mx-auto">
        <div
          class="relative flex flex-col break-words min-w-0 bg-clip-border rounded-lg bg-main m-5"
        >
          <div
            class="p-6 relative flex flex-col min-w-0 break-words border border-dashed bg-clip-border rounded-2xl border-stone-200 bg-light/30 overflow-auto"
          >
            <div
              v-if="empty?.b"
              class="border-b border-t border-neutral-200 ont-bold text-xl my-3 p-3 text-red-500"
            >
              {{ empty.t }}
            </div>
            <slot></slot>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import Breadcrumbs from "@components/admin/Breadcrumbs.vue";
import Spinner from "@components/ui/Spinner.vue";
import { object } from "yup";
const props = defineProps({
  data: { type: Array },
  title: { type: String },
  button: { type: Object },
  empty: { type: Object },
  isLoading: { type: Boolean, default: false },
  error: {},
});
</script>

<style lang="scss" scoped></style>
