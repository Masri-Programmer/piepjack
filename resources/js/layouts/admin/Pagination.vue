<template>
  <nav class="flex items-center justify-center mt-10 w-full">
    <ul class="inline-flex -space-x-px mt-6">
      <li>
        <button
          @click="changePage(currentPage - 1)"
          :disabled="isFetching || currentPage === 1"
          class="bg-main border border-gray text-accent hover:bg-main hover:text-accent_light ml-0 rounded-l-lg leading-tight py-2 px-3"
        >
          Previous
        </button>
      </li>
      <li v-for="pageNum in totalPages" :key="pageNum">
        <button
          @click="changePage(pageNum)"
          :disabled="isFetching || currentPage === pageNum"
          :class="{
            'bg-accent_light text-main': currentPage === pageNum,
            'bg-main border-gray text-accent hover:bg-main hover:text-accent_light ':
              currentPage !== pageNum,
          }"
          class="border leading-tight py-2 px-3"
        >
          {{ pageNum }}
        </button>
      </li>
      <li>
        <button
          @click="changePage(currentPage + 1)"
          :disabled="isFetching || currentPage === totalPages"
          class="bg-main border border-gray text-accent hover:bg-main hover:text-accent_light rounded-r-lg leading-tight py-2 px-3"
        >
          Next
        </button>
      </li>
    </ul>
  </nav>
</template>

<script setup>
const props = defineProps({
  currentPage: {
    type: Number,
    required: true,
  },
  totalPages: {
    type: Number,
    required: true,
  },
  isFetching: {
    type: Boolean,
    default: false,
  },
});

const emits = defineEmits(["pageChanged"]);

const changePage = (page) => {
  if (page >= 1 && page <= props.totalPages) {
    emits("pageChanged", page);
  }
};
</script>
