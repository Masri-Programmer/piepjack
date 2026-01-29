<template>
  <PageLayout
    :data="links"
    :title="$t('admin.products.title')"
    @button:click="toggle(!value)"
    :button="{ title: $t('admin.products.addNewProduct'), click: true }"
  >
    <div
      class="flex justify-between items-stretch flex-wrap min-h-[70px] pb-6 text-accent"
    >
      <h3
        class="flex flex-col items-start justify-center m-2 ml-0 font-medium text-xl/tight text-dark"
      >
        <span class="mr-3 font-semibold text-dark">{{
          $t("admin.products.allProducts", { total: data?.meta.total ?? 0 })
        }}</span>
        <span class="mt-1 font-medium text-gray text-lg/normal">{{
          $t("admin.common.exclusiveFrom")
        }}</span>
      </h3>
      <SearchField v-model="searchTerm" />
    </div>
    <div class="flex-auto block text-accent">
      <div class="overflow-x-auto">
        <div
          class="p-3 my-3 text-xl text-red-500 border-t border-b border-neutral-200 ont-bold"
          v-if="error"
        >
          {{ error }}
        </div>
        <tr
          v-if="!data?.data.length && !isLoading && !isError"
          class="p-3 my-3 text-xl text-red-500 border-t border-b border-neutral-200 ont-bold"
        >
          {{
            $t("admin.products.noProductsFound")
          }}
        </tr>
        <table
          class="w-full my-0 align-middle text-dark border-neutral-200"
        >
          <thead class="align-bottom">
            <tr class="font-semibold text-[0.95rem] text-gray">
              <th class="py-3 uppercase text-start">
                {{ $t("admin.products.table.id") }}
              </th>
              <th class="p-3 text-start min-w-[95px] uppercase">
                {{ $t("admin.products.table.img") }}
              </th>
              <th class="p-3 text-start min-w-[175px] uppercase">
                {{ $t("admin.products.table.name") }}
              </th>
              <th class="p-3 text-start min-w-[175px] uppercase">
                {{ $t("admin.products.table.description") }}
              </th>
              <th class="p-3 uppercase text-start">
                {{ $t("admin.products.table.published") }}
              </th>
              <th class="p-3 text-start min-w-[175px] uppercase">
                {{ $t("admin.products.table.category") }}
              </th>
              <th class="p-3 text-start min-w-[175px] uppercase">
                {{ $t("admin.products.table.updatedAt") }}
              </th>
            </tr>
          </thead>
          <tbody>
            <TableSkeleton :rows="15" :columns="7" v-if="isLoading" />

            <ProductRow v-if="value" @success:store="toggle(false)" />
            <template v-if="!isLoading && data?.data">
              <tr
                class="border-b border-dashed last:border-b-0 border-main"
                v-for="product in data?.data"
                :key="product.id"
              >
                <td>{{ product.id }}</td>
                <td class="p-3">
                  <ProductImg
                    :product="product"
                    @handle:img="
                      (data) =>
                        handleUpdate(
                          data.product,
                          data.img,
                          data.value
                        )
                    "
                  />
                </td>
                <td class="p-3">
                  <input
                    v-model="product.name"
                    type="text"
                    id="name"
                    @change="
                      (e) =>
                        handleUpdate(product, 'name', e.target.value)
                    "
                    class="block w-full p-2 mt-1 font-bold rounded-md focus:outline-none focus:ring focus:ring-accent_dark"
                  />
                </td>
                <td class="p-3 text-start">
                  <ProductDescription
                    :desc="product.description"
                    @handle:update="
                      (value) =>
                        handleUpdate(product, 'description', value)
                    "
                  />
                </td>
                <td class="p-3 text-start">
                  <ToggleSwitch
                    v-model="product.active"
                    @update:toggle="
                      (boolean) => handleUpdate(product, 'active', boolean)
                    "
                  />
                </td>
                <td class="pr-0 text-start">
                  <div
                    class="text-center align-baseline inline-flex px-4 py-3 mr-auto items-center font-semibold text-[.95rem] leading-none rounded-lg w-full"
                  >
                    <select
                      :value="product.category?.id"
                      id="category_id"
                      @change="
                        (event) =>
                          handleUpdate(
                            product,
                            'category_id',
                            event.target.value
                          )
                      "
                      class="shadow-md border border-gray text-accent_dark sm:text-sm rounded-lg focus:ring-accent_dark focus:border-accent_dark block w-full p-2.5"
                    >
                      <option value="" disabled>
                        {{ $t("admin.products.selectCategory") }}
                      </option>
                      <option
                        v-for="category in categoriesData?.data"
                        :key="category.id"
                        :value="category.id"
                      >
                        {{ category.name }}
                      </option>
                    </select>
                  </div>
                </td>
                <td class="p-3 pr-2 text-start">
                  <span
                    class="text-xs text-center align-baseline inline-flex p-3 mr-auto items-center font-semibold text-[.95rem] leading-none text-blue-500 bg-blue-500/20 rounded-lg"
                  >
                    {{ formatDate(product.updated_at) }}
                  </span>
                </td>
                <td class="p-3 text-start">
                  <div class="flex items-center gap-2">
                    <button
                      :disabled="deleteLoading"
                      class="ml-auto relative text-main bg-accent hover:text-white hover:bg-red-500 flex items-center h-[30px] w-[30px] text-base font-medium leading-normal text-center align-middle cursor-pointer rounded-2xl transition-colors duration-200 ease-in-out shadow-none border-0 justify-center"
                      @click="() => handleDelete(product.id)"
                    >
                      <Trash2 size="16" stroke-width="1.5" />
                    </button>
                    <button>
                      <router-link
                        class="ml-auto relative text-main bg-accent hover:text-white hover:bg-blue-500 flex items-center h-[30px] w-[30px] text-base font-medium leading-normal text-center align-middle cursor-pointer rounded-2xl transition-colors duration-200 ease-in-out shadow-none border-0 justify-center"
                        :to="`/admin/products/${product.id}/${product.slug}`"
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
import { useI18n } from "vue-i18n";
import { productParams } from "@lib/store/admin";
import { useToggle } from "@vueuse/core";
import ProductRow from "./ProductRow.vue";
import ProductImg from "./ProductImg.vue";
import PageLayout from "@layouts/admin/PageLayout.vue";
import Pagination from "@layouts/admin/Pagination.vue";
import { Trash2, ChevronRight } from "lucide-vue-next";
import ProductDescription from "./ProductDescription.vue";
import TableSkeleton from "@layouts/admin/TableSkeleton.vue";
import SearchField from "@components/admin/SearchField.vue";
import { useDebounceFn } from "@vueuse/core";
import ToggleSwitch from "@components/admin/ToggleSwitch.vue";
import { apiQuery, formatDate } from "@lib/helpers";

const { t } = useI18n();

const searchTerm = computed({
  get: () => productParams.value.search,
  set: (value) => {
    productParams.value.search = value;
    debouncedRefetch();
  },
});

const page = computed({
  get: () => productParams.value.page,
  set: (value) => {
    productParams.value.page = value;
    debouncedRefetch();
  },
});

const { data, error, isError, isLoading, isPreviousData, isFetching, refetch } =
  apiQuery("products").useGet(productParams);
const { mutate: updateProduct, isLoading: updateLoading } =
  apiQuery("products").useUpdate();
const { mutate: deleteProduct, isLoading: deleteLoading } =
  apiQuery("products").useDelete();
const { data: categoriesData } = apiQuery("all-categories").useGet({});

const debouncedRefetch = useDebounceFn(() => {
  refetch();
}, 300);

const handlePageChange = (newPage, action) => {
  if (action === "increment" && !isPreviousData) {
    productParams.value.page = productParams.value.page + 1;
  } else if (action === "decrement") {
    productParams.value.page = Math.max(productParams.value.page - 1, 1);
  } else {
    productParams.value.page = newPage;
    debouncedRefetch();
  }
};

const links = computed(() => [
  { title: t("admin.menu.home"), link: "/" },
  { title: t("admin.menu.products"), current: true, link: "/products" },
]);

const handleDelete = async (id) => {
  deleteProduct(id);
};

const handleUpdate = async (product, property, value) => {
  let form = {};
  form =
    property === "image"
      ? { ...product, ...value }
      : { ...product, [property]: value };
  updateProduct({ id: product.id, data: form });
};

const [value, toggle] = useToggle();
</script>