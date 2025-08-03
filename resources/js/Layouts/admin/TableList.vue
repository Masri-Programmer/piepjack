<template>
  <div class="flex-auto block text-accent">
    <div class="overflow-x-auto">
      <div
        class="border-b border-t border-neutral-200 font-bold text-xl my-3 p-3 text-red-500"
        v-if="error"
      >
        {{ error }}
      </div>
      <table class="w-full my-0 align-middle text-dark border-neutral-200">
        <thead class="align-bottom">
          <tr class="font-semibold text-[0.95rem] text-gray">
            <th
              v-for="(column, index) in columns"
              :key="index"
              class="pb-3 px-1 text-start uppercase"
            >
              {{ column }}
            </th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-if="!rows?.length && !isLoading && !isError"
            class="font-bold text-xl my-6"
          >
            No Data Found
          </tr>
          <tr v-if="isLoading" class="font-bold text-xl my-6">
            Loading...
          </tr>
          <template v-if="!isLoading && rows?.length">
            <tr
              class="border-b border-dashed last:border-b-0 border-main"
              v-for="(row, index) in rows"
              :key="index"
            >
              <td
                v-for="(column, colIndex) in columns"
                :key="colIndex"
                class="p-3 text-start"
              >
                <span>{{ row[column] }}</span>
              </td>
              <td class="p-3 text-start">
                <div class="flex items-center gap-2">
                  <button
                    :disabled="deleteLoading"
                    class="ml-auto relative text-main bg-accent hover:text-white hover:bg-red-500 flex items-center h-[30px] w-[30px] text-base font-medium leading-normal text-center align-middle cursor-pointer rounded-2xl transition-colors duration-200 ease-in-out shadow-none border-0 justify-center"
                    @click="() => handleDelete(row.id)"
                  >
                    <Trash2 size="16" stroke-width="1.5" />
                  </button>
                  <button>
                    <router-link
                      class="ml-auto relative text-main bg-accent hover:text-white hover:bg-blue-500 flex items-center h-[30px] w-[30px] text-base font-medium leading-normal text-center align-middle cursor-pointer rounded-2xl transition-colors duration-200 ease-in-out shadow-none border-0 justify-center"
                      :to="`/admin/orders/${row.id}`"
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
        :totalPages="totalPages"
        :isFetching="isFetching"
        @pageChanged="handlePageChange"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, watchEffect } from "vue";
import Pagination from "./Pagination.vue";
import { Trash2, ChevronRight } from "lucide-vue-next";

defineProps({
  rows: {
    type: Array,
    required: true,
  },
  columns: {
    type: Array,
    required: true,
  },
  isLoading: {
    type: Boolean,
    default: false,
  },
  error: {
    type: String,
    default: "",
  },
  isError: {
    type: Boolean,
    default: false,
  },
  isFetching: {
    type: Boolean,
    default: false,
  },
  page: {
    type: Number,
    required: true,
  },
  totalPages: {
    type: Number,
    required: true,
  },
  deleteLoading: {
    type: Boolean,
    default: false,
  },
});

const handleDelete = (id) => {
  // handle delete logic
};

const handlePageChange = (page) => {
  // handle page change logic
};

//  <DynamicTable
//     :rows="orders"
//     :columns="['id', 'status', 'total_price', 'created_at', 'updated_at']"
//     :isLoading="isLoading"
//     :error="error"
//     :isError="isError"
//     :isFetching="isFetching"
//     :page="page"
//     :totalPages="totalPages"
//     :deleteLoading="deleteLoading"
//   />
</script>
