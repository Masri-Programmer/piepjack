<template>
  <PageLayout
    :data="links"
    :title="'Order # ' + data?.id"
    :isLoading="isLoading"
    :error="error"
  >
    <div class="pb-4">
      <h3 class="font-semibold text-accent_dark">Customer Details</h3>
      <p class="text-accent_dark">
        {{ data?.customer_detail.first_name }}
        {{ data?.customer_detail.last_name }}
      </p>
      <p class="text-accent_dark">
        {{ data?.customer_detail.address_line_one }}
      </p>
      <p class="text-accent_dark">
        {{ data?.customer_detail.address_line_two }}
      </p>
      <p class="text-accent_dark">
        Email: {{ data?.customer_detail.customer.email }}
      </p>
    </div>

    <div class="border-t py-4">
      <h3 class="font-semibold text-accent_dark">Order Details</h3>
      <p class="text-accent_dark">Total Price: €{{ data?.total_price }}</p>
      <p class="text-accent_dark">
        Status: <span :class="statusClass(data)">{{ data?.status }}</span>
      </p>
      <p class="text-accent_dark">
        Created At: {{ formatDate(data?.created_at) }}
      </p>
      <p class="text-accent_dark">
        Updated At: {{ formatDate(data?.updated_at) }}
      </p>
    </div>

    <div class="border-t py-4">
      <h3 class="font-semibold text-accent_dark">Products</h3>
      <div v-for="(product, index) in data?.products" :key="index" class="py-4">
        <ProductItem :product="product" />
      </div>
    </div>
  </PageLayout>
</template>

<script setup>
import { ref } from "vue";
import { useRoute } from "vue-router";
import ProductItem from "./ProductItem.vue";
import PageLayout from "@layouts/admin/PageLayout.vue";
import { formatDate, apiQuery } from "@lib/helpers";

const route = useRoute();
const id = route.params.id;
const slug = route.params.id;
const { data, error, isLoading } = apiQuery("orders").useGetById(id);

const statusClass = (order) => {
  return order.status === "paid" ? "text-green-600" : "text-red-600";
};

const links = ref([
  {
    title: "Home",
    link: "/",
  },
  {
    title: "Orders",
    link: "/Orders",
  },
  {
    title: slug,
    current: true,
    link: "",
  },
]);
</script>
