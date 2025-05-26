<template>
  <div class="home-products-container">
    <template v-if="error">
      <p class="home-products-error">
        {{ $t("validation.error.getProducts") }}
      </p>
    </template>
    <template v-else>
      <ul class="home-products-list">
        <li
          class="home-products-item"
          v-for="(product, index) in products?.data || []"
          :key="product.id"
          data-aos="fade-up"
          :data-aos-delay="100 * index"
        >
          <ProductCard :product="product" />
        </li>
      </ul>
    </template>
  </div>
  <div class="home-products-container" data-aos="fade-up" :data-aos-delay="300">
    <router-link to="/collections" class="text-xs view-all">
      {{ $t("components.buttons.viewAll") }}
    </router-link>
  </div>
</template>

<script setup>
import ProductCard from "../product/ProductCard.vue";
import { ref } from "vue";
import { apiQuery } from "@lib/helpers";

const params = ref({
  active: null,
  category_id: null,
  page: 1,
  per_page: 8,
  search: "",
  sort_direction: "",
  sort_field: "",
});

const {
  data: products,
  error,
  isLoading,
} = apiQuery("products").useGet(params);
</script>
