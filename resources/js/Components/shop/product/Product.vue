<template>
  <Spinner v-if="isLoading" :center="true" class="w-full h-screen" />
  <div
    v-else-if="error && !product"
    class="border-b border-t border-neutral-200 ont-bold text-xl my-3 p-3 text-red-500"
  >
    {{ error }}
  </div>
  <template v-else>
    <div class="w-full flex flex-col gap-12 md:flex-row container">
      <ProductContent :data="product" />
    </div>
    <!-- <hr class="text-gray my-6 -mx-4 md:-mx-8 2xl:-mx-16" /> -->
    <div class="container">
      <HomeCarousel
        :name="product?.category_name"
        :id="product?.category_id"
        :title="$t('common.titles.peopleAlsoLiked')"
      />
    </div>
  </template>
</template>

<script setup>
import axios from "axios";
import { useRoute } from "vue-router";
import { onMounted, watch, ref } from "vue";
import Spinner from "@components/ui/Spinner.vue";
import { useArea } from '@lib/useArea.js'
import ProductContent from "./ProductContent.vue";
import HomeCarousel from "@components/shop/home/HomeCarousel.vue";
const {  getApiUrl } = useArea();

const route = useRoute();
const product = ref(null);
const error = ref(null);
const isLoading = ref(true);
const fetchProduct = async (id) => {
  isLoading.value = true;
  try {
    const response = await axios.get(`${getApiUrl()}/products/${id}`);
    product.value = response.data.data;
    error.value = null;
  } catch (err) {
    error.value = err.response?.message || "Failed to get product by id";
    product.value = null;
  } finally {
    isLoading.value = false;
  }
};
onMounted(() => fetchProduct(route.params.id));
watch(
  () => route.params.id,
  (newId) => {
    if (newId) fetchProduct(newId);
  }
);
</script>
