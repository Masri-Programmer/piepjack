<template>
  <PageLayout
    :data="links"
    title="Orders"
  >
    <!-- card header -->
    <div
      class="flex justify-between items-stretch flex-wrap min-h-[70px] pb-6 text-accent"
    >
      <h3
        class="flex flex-col items-start justify-center m-2 ml-0 font-medium text-xl/tight text-dark"
      >
        <span class="mr-3 font-semibold text-dark"
          >All Orders ({{ data?.meta.total }})</span
        >
        <span class="mt-1 font-medium text-gray text-lg/normal"
          >Exlusive from Piepjackclothing</span
        >
      </h3>
      <SearchField v-model="searchTerm" :placeholder="'Order #'" />
    </div>
    <!-- end card header -->

    <!-- card body  -->
    <div class="flex-auto block text-accent">
      <div class="overflow-x-auto">
        <table class="w-full my-0 align-middle text-dark border-neutral-200">
          <thead class="align-bottom">
            <tr class="font-semibold text-[0.95rem] text-gray">
              <th class="pb-3 px-1 text-start uppercase">ID</th>
              <th class="pb-3 px-1 text-start uppercase">NUMBER</th>
              <th class="pb-3 px-1 text-start uppercase min-w-[120px]">
                STATUS
              </th>
              <th class="pb-3 px-1 text-start uppercase">TOTAL</th>
              <th class="pb-3 px-1 text-start min-w-[150px] uppercase">
                CREATED AT
              </th>
              <th class="pb-3 px-1 text-start min-w-[150px] uppercase">
                UPDATE AT
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-if="!data?.data.length && !isLoading && !isError"
              class="font-bold text-xl my-6"
            >
              No Orders Found
            </tr>
            <ProductsShowSkeleton v-if="isLoading" />
            <tr
              class="border-b border-neutral-200 ont-bold text-xl my-6"
              v-if="error"
            >
              {{
                error
              }}
            </tr>
            <template v-if="!isLoading && data?.data">
              <tr
                class="border-b border-dashed last:border-b-0 border-main"
                v-for="order in data?.data"
              >
                <td>{{ order.id }}</td>
                <td>{{ order.order_number }}</td>
                <td class="p-3">
                  <div class="flex items-center">
                    <div class="flex flex-col justify-start w-full">
                      <span
                        class="mb-1 font-semibold transition-colors duration-200 ease-in-out text-lg/normal text-secondary-inverse hover:text-blue-500"
                      >
                        <select
                          v-model="order.status"
                          id="status"
                          :class="[
                            'font-bold',
                            order.status === 'paid'
                              ? 'text-green-500'
                              : order.status === 'pending'
                              ? 'text-yellow-700'
                              : 'text-red-500',
                          ]"
                          @change="
                            (e) => handleUpdate(order, 'status', e.target.value)
                          "
                          class="mt-1 block w-full p-2 border border-gray rounded-md focus:outline-none focus:ring focus:ring-accent_dark"
                        >
                          <option
                            v-for="status in data?.meta.statuses"
                            :key="status"
                            :class="[
                              'font-bold',
                              status === 'paid'
                                ? 'text-green-500'
                                : status === 'pending'
                                ? 'text-yellow-700'
                                : 'text-red-500',
                            ]"
                            :value="status"
                          >
                            {{ status }}
                          </option>
                        </select>
                      </span>
                    </div>
                  </div>
                  <!-- </router-link> -->
                </td>
                <td class="p-3 text-start">
                  <div class="relative">
                    <input
                      type="number"
                      class="border rounded p-2 font-bold pl-6 pr-2"
                      v-model="order.total_price"
                      step="1"
                      min="0"
                      @change="
                        (e) =>
                          handleUpdate(
                            order,
                            'total_price',
                            parseFloat(e.target.value)
                          )
                      "
                      placeholder="Enter total price"
                    />
                    <span
                      class="absolute left-2 top-1/2 transform -translate-y-1/2 text-xl"
                      >€</span
                    >
                  </div>
                </td>
                <td class="p-3 text-start">
                  <span
                    class="text-xs text-center align-baseline inline-flex p-3 mr-auto items-center font-semibold text-[.95rem] leading-none text-blue-500 bg-blue-500/20 rounded-lg"
                    >{{ formatDate(order.created_at) }}</span
                  >
                </td>
                <td class="p-3 text-start">
                  <span
                    class="text-xs text-center align-baseline inline-flex p-3 mr-auto items-center font-semibold text-[.95rem] leading-none text-blue-500 bg-blue-500/20 rounded-lg"
                    >{{ formatDate(order.updated_at) }}</span
                  >
                </td>
                <td class="p-3 text-start">
                  <div class="flex items-center gap-2">
                    <button
                      :disabled="deleteLoading"
                      class="ml-auto relative text-main bg-accent hover:text-white hover:bg-red-500 flex items-center h-[30px] w-[30px] text-base font-medium leading-normal text-center align-middle cursor-pointer rounded-2xl transition-colors duration-200 ease-in-out shadow-none border-0 justify-center"
                      @click="() => handleDelete(order.id)"
                    >
                      <Trash2 size="16" stroke-width="1.5" />
                    </button>
                    <button>
                      <router-link
                        class="ml-auto relative text-main bg-accent hover:text-white hover:bg-blue-500 flex items-center h-[30px] w-[30px] text-base font-medium leading-normal text-center align-middle cursor-pointer rounded-2xl transition-colors duration-200 ease-in-out shadow-none border-0 justify-center"
                        :to="`/orders/${order.id}`"
                      >
                        <span
                          class="flex items-center justify-center p-0 m-0 leading-none shrink-0"
                        >
                          <ChevronRight size="16" stroke-width="1.5" />
                        </span>
                      </router-link>
                    </button>
                  </div>
                </td>
              </tr>
            </template>
          </tbody>
        </table>
        <Pagination
          :currentPage="page"
          :totalPages="data?.meta.last_page ?? 1"
          :isFetching="isFetching"
          @pageChanged="handlePageChange"
        />
      </div>
    </div>
  </PageLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import { ordersParams } from "@lib/store/admin";
import { useToggle } from "@vueuse/core";
import { useDebounceFn } from "@vueuse/core";
import { apiQuery, formatDate } from "@lib/helpers";
import PageLayout from "@layouts/admin/PageLayout.vue";
import Pagination from "@layouts/admin/Pagination.vue";
import { Trash2, ChevronRight } from "lucide-vue-next";
import SearchField from "@components/admin/SearchField.vue";
import ProductsShowSkeleton from "../products/ProductsShowSkeleton.vue";

const searchTerm = computed({
  get: () => ordersParams.value.search,
  set: (value) => {
    ordersParams.value.search = value;
    debouncedRefetch();
  },
});

const page = computed({
  get: () => ordersParams.value.page,
  set: (value) => {
    ordersParams.value.page = value;
    debouncedRefetch();
  },
});

const { data, error, isError, isLoading, isPreviousData, isFetching, refetch } =
  apiQuery("orders").useGet(ordersParams);
const {
  mutate: updateOrder,
  isLoading: updateLoading,
  isSuccess: updateSuccess,
  error: updateErrorMessage,
} = apiQuery("orders").useUpdate();
const {
  mutate: deleteOrder,
  isLoading: deleteLoading,
  isSuccess: deleteSuccess,
  error: deleteErrorMessage,
} = apiQuery("orders").useDelete();

const handleUpdate = async (order, property, value) => {
  const form = { ...order, [property]: value };
  updateOrder({ id: order.id, data: form });
};
const handleDelete = async (id) => {
  deleteOrder(id);
};

const handlePageChange = (newPage, action) => {
  if (action === "increment" && !isPreviousData) {
    ordersParams.value.page = ordersParams.value.page + 1;
  } else if (action === "decrement") {
    ordersParams.value.page = Math.max(ordersParams.value.page - 1, 1);
  } else {
    ordersParams.value.page = newPage;
    debouncedRefetch();
  }
};

const links = ref([
  { title: "Home", link: "/" },
  { title: "Products", current: true, link: "/products" },
]);

const [value, toggle] = useToggle();
const debouncedRefetch = useDebounceFn(() => {
  refetch();
}, 300);
</script>
