<template>
  <div v-if="error" class="error-message">
    {{ error }}
  </div>
  <template v-if="products?.data.length">
    <div class="product-grid">
      <template v-for="product in products.data" :key="product.id">
        <ProductCard :product="product" />
      </template>
    </div>
    <div class="pagination-container">
      <Pagination
        :pagination="products?.meta"
        :links="products?.links"
        @page-change="changePage"
      />
    </div>
  </template>
  <template v-else>
    <h1 v-if="!isLoading" class="not-found-message">
      {{ $t("common.product.notFound") }}
    </h1>
  </template>
</template>

<script setup>
import ProductCard from "../components/product/ProductCard.vue";
import Pagination from "../Layouts/Pagination.vue";
import { useRoute } from "vue-router";
import { computed } from "vue";
import { apiQuery } from "@lib/helpers";

const route = useRoute();
const page = computed(() => parseInt(route.query.page) || 1);
const params = computed(() => ({
  active: null,
  category_id: route.params.id,
  page: page.value,
  per_page: 15,
  search: "",
  sort_direction: "",
  sort_field: "",
}));

const { data: products, error, isLoading } = apiQuery("products").useGet(params);
</script>

<style scoped>
.error-message {
  @apply border-b border-t border-neutral-200 font-bold text-xl my-3 p-3 text-red-500;
}
.product-grid {
  @apply grid gap-x-3 md:gap-x-5 xl:gap-x-7 gap-y-3 xl:gap-y-5 2xl:gap-y-8 bg-accent grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 2xl:grid-cols-4 container;
}
.pagination-container {
  @apply flex justify-center w-full max-h-16;
}
.not-found-message {
  @apply text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-extrabold text-center mb-6 sm:mb-8 md:mb-10 lg:mb-12 grid place-content-center uppercase;
}
</style>
