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
import '@assets/css/collections.css'
import ProductCard from "@components/shop/product/ProductCard.vue";
import Pagination from "@layouts/shop/Pagination.vue";
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