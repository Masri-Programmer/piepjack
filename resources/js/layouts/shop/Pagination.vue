<template>
  <nav
    class="flex items-center justify-between border-b border-gray px-4 sm:px-0"
  >
    <!-- <div class="-mt-px flex w-0 flex-1 items-center mb-3 opacity-55">
        <button
          v-if="pagination.links[0].url"
          @click="$emit('page-change', getPageNumber(pagination.links[0].url))"
          class="inline-flex items-center border-b-2 border-transparent pt-4 pr-1 text-sm font-medium text-gray-300"
        >
          <ChevronLeft StrokeWidth="1" size="18" />
        </button>
      </div> -->

    <div class="md:-mt-px md:flex w-full h-full justify-center gap-5">
      <button
        v-for="(link, index) in pagination.links"
        :key="index"
        :disabled="link.active || !link.url"
        @click="handlePageChange(link)"
        :class="[
          'inline-flex items-center border-b-2 p-4 text-sm font-medium',
          link.active
            ? 'border-main text-main'
            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
        ]"
        v-html="link.label"
      ></button>
    </div>

    <!-- <div class="-mt-px flex w-0 flex-1 justify-end mb-3 opacity-55">
        <button
          v-if="pagination.links[pagination.links.length - 1].url"
          @click="
            $emit(
              'page-change',
              getPageNumber(pagination.links[pagination.links.length - 1].url)
            )
          "
          class="inline-flex items-center border-b-2 border-transparent pt-4 pl-1 text-sm font-medium text-gray-500 hover:border-gray-300 hover:text-gray-700"
        >
          <ChevronRight StrokeWidth="1" size="18" />
        </button>
      </div> -->
  </nav>
</template>

<script setup>
import { ChevronRight, ChevronLeft } from "lucide-vue-next";
import { useRoute, useRouter } from "vue-router";
const route = useRoute();
const router = useRouter();
const props = defineProps({
  pagination: {
    type: Object,
    required: true,
  },
  links: {
    type: Object,
    required: true,
  },
});

const handlePageChange = (link) => {
  const urlObj = new URL(link.url);
  const page = urlObj.searchParams.get("page");
  const query = { ...route.query, page };
  router.push({
    path: route.path,
    query,
  });
};
</script>
