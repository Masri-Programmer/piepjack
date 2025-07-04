<template>
  <PageLayout
    :data="links"
    :empty="{
      b: !product?.data?.items.length && !isLoading,
      t: 'No Product Items Found',
    }"
    :isLoading="isLoading"
    :error="error"
    :title="product?.data?.name"
    @button:click="toggle(!value)"
    :button="{ title: 'Add New Product Item', click: true }"
  >
    <div
      class="flex flex-col justify-between items-stretch flex-wrap min-h-[70px] pb-6 text-accent"
    >
      <h3
        class="flex flex-col items-start justify-center m-2 ml-0 font-medium text-xl/tight text-dark"
      >
        <span class="mr-3 font-semibold text-dark"
          >All Product Items ({{ product.data?.items.length ?? 0 }})</span
        >
        <span class="mt-1 font-medium text-gray text-lg/normal"
          >Exlusive from Piepjackclothing</span
        >
      </h3>
      <ProductDetails :data="product?.data" :storeValue="value" />
    </div>
  </PageLayout>
</template>

<script setup>
import { useRoute } from "vue-router";
import { useToggle } from "@vueuse/core";
import { ref , computed} from "vue";
import PageLayout from "@layouts/admin/PageLayout.vue";
import ProductDetails from "./ProductDetails.vue";
import { apiQuery } from "@lib/helpers";

const route = useRoute();
const id = computed(() => route.params.id);
const slug = route.params.slug;
const {
  data: product,
  error,
  isLoading,
  refetch,
} = apiQuery("products").useGetById(id);
const [value, toggle] = useToggle();

const links = ref([
  {
    title: "Home",
    link: "/admin/dashboard",
  },
  {
    title: "Products",
    link: "/admin/products",
  },
  {
    title: slug,
    current: true,
    link: "",
  },
]);
</script>
