<template>
  <PageLayout :data="links" title="Users">
    <!-- card header -->
    <div
      class="flex justify-between items-stretch flex-wrap min-h-[70px] pb-6 text-accent"
    >
      <h3
        class="flex flex-col items-start justify-center m-2 ml-0 font-medium text-xl/tight text-dark"
      >
        <span class="mr-3 font-semibold text-dark"
          >All Users ({{ data?.meta.total }})</span
        >
        <span class="mt-1 font-medium text-gray text-lg/normal"
          >Exlusive from Piepjackclothing</span
        >
      </h3>
      <SearchField v-model="searchTerm" :placeholder="'Email'" />
    </div>
    <!-- end card header -->

    <!-- card body  -->
    <div class="flex-auto block text-accent">
      <div class="overflow-x-auto">
        <table class="w-full my-0 align-middle text-dark buser-neutral-200">
          <thead class="align-bottom">
            <tr class="font-semibold text-[0.95rem] text-gray">
              <th class="pb-3 px-1 text-start uppercase">ID</th>
              <th class="pb-3 px-1 text-start uppercase">EMAIL</th>
              <th class="pb-3 px-1 text-start min-w-[50px] uppercase">
                ACTIVE
              </th>
              <th class="pb-3 px-1 text-start min-w-[150px] uppercase">
                CREATED AT
              </th>
              <th class="pb-3 px-1 text-start min-w-[150px] uppercase">
                UPDATED AT
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-if="!data?.data.length && !isLoading && !isError"
              class="font-bold text-xl my-6"
            >
              No Data Found
            </tr>
            <ProductsShowSkeleton v-if="isLoading" />
            <tr
              class="buser-b buser-neutral-200 ont-bold text-xl my-6"
              v-if="error"
            >
              {{
                error
              }}
            </tr>
            <template v-if="!isLoading && data?.data">
              <tr
                class="buser-b buser-dashed last:buser-b-0 buser-main"
                v-for="user in data?.data"
              >
                <td>{{ user.id }}</td>
                <td class="p-3 text-start">{{ user.email }}</td>
                <td class="p-3">
                  <ToggleSwitch v-model="user.active" />
                  <!-- @update:toggle="
                      (boolean) => handleUpdate(product, 'active', boolean)
                    " -->
                </td>
                <td class="p-3 text-start">
                  <span
                    class="text-xs text-center align-baseline inline-flex p-3 mr-auto items-center font-semibold text-[.95rem] leading-none text-blue-500 bg-blue-500/20 rounded-lg"
                    >{{ formatDate(user.created_at) }}</span
                  >
                </td>
                <td class="p-3 text-start">
                  <span
                    class="text-xs text-center align-baseline inline-flex p-3 mr-auto items-center font-semibold text-[.95rem] leading-none text-blue-500 bg-blue-500/20 rounded-lg"
                    >{{ formatDate(user.updated_at) }}</span
                  >
                </td>
                <td class="p-3 text-start">
                  <div class="flex items-center gap-2">
                    <button
                      :disabled="deleteLoading"
                      class="ml-auto relative text-main bg-accent hover:text-white hover:bg-red-500 flex items-center h-[30px] w-[30px] text-base font-medium leading-normal text-center align-middle cursor-pointer rounded-2xl transition-colors duration-200 ease-in-out shadow-none buser-0 justify-center"
                      @click="() => handleDelete(user.id)"
                    >
                      <Trash2 size="16" stroke-width="1.5" />
                    </button>
                    <button>
                      <router-link
                        class="ml-auto relative text-main bg-accent hover:text-white hover:bg-blue-500 flex items-center h-[30px] w-[30px] text-base font-medium leading-normal text-center align-middle cursor-pointer rounded-2xl transition-colors duration-200 ease-in-out shadow-none buser-0 justify-center"
                        :to="`/admin/users/${user.id}`"
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
import { usersParams } from "@lib/store/admin";
import { ref, computed } from "vue";
import { useToggle } from "@vueuse/core";
import { useDebounceFn } from "@vueuse/core";
import PageLayout from "@layouts/admin/PageLayout.vue";
import Pagination from "@layouts/admin/Pagination.vue";
import { apiQuery, formatDate } from "@lib/helpers";
import { Trash2, ChevronRight } from "lucide-vue-next";
import SearchField from "@components/admin/SearchField.vue";
import ToggleSwitch from "@components/admin/ToggleSwitch.vue";
import ProductsShowSkeleton from "../products/ProductsShowSkeleton.vue";

const searchTerm = computed({
  get: () => usersParams.value.search,
  set: (value) => {
    usersParams.value.search = value;
    debouncedRefetch();
  },
});

const page = computed({
  get: () => usersParams.value.page,
  set: (value) => {
    usersParams.value.page = value;
    debouncedRefetch();
  },
});

const { data, error, isError, isLoading, isPreviousData, isFetching, refetch } =
  apiQuery("users").useGet(usersParams);

const {
  mutate: deleteUser,
  isLoading: deleteLoading,
  isSuccess: deleteSuccess,
  error: deleteErrorMessage,
} = apiQuery("users").useDelete();

const handleDelete = async (id) => {
  if (confirm("Are you sure you want to delete this user?")) deleteUser(id);
};

const handlePageChange = (newPage, action) => {
  if (action === "increment" && !isPreviousData) {
    usersParams.value.page = usersParams.value.page + 1;
  } else if (action === "decrement") {
    usersParams.value.page = Math.max(usersParams.value.page - 1, 1);
  } else {
    usersParams.value.page = newPage;
    debouncedRefetch();
  }
};

const links = ref([
  { title: "Home", link: "/" },
  { title: "Users", current: true, link: "/users" },
]);

const [value, toggle] = useToggle();
const debouncedRefetch = useDebounceFn(() => {
  refetch();
}, 300);
</script>
