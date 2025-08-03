<template>
  <div class="flex-auto block text-accent">
    <div class="overflow-x-auto">
      <table class="w-full my-0 align-middle text-dark border-neutral-200">
        <thead class="align-bottom">
          <tr class="font-semibold text-[0.95rem] text-gray">
            <th class="py-3 uppercase text-start">ID</th>
            <th class="p-3 text-start min-w-[95px] uppercase">IMG</th>
            <th class="p-3 text-start min-w-[175px] uppercase">NAME</th>
            <th
              class="p-3 text-start min-w-[135px] uppercase"
              v-if="!data?.items && !variations"
            >
              options
            </th>

            <template v-else-if="data.items.length">
              <th
                class="p-3 text-start min-w-[135px] uppercase"
                v-for="option in data.items?.[0]?.options"
              >
                {{ option.name }}
              </th>
            </template>
            <template v-else-if="variations?.data">
              <th
                class="p-3 text-start min-w-[135px] uppercase"
                v-for="option in variations.data"
              >
                {{ option.name }}
              </th>
            </template>
            <th class="p-3 uppercase text-start">PUBLISHED</th>
            <th class="p-3 text-start uppercase min-w-[95px]">price</th>
            <th class="p-3 text-start uppercase min-w-[95px]">quantity</th>
          </tr>
        </thead>
        <tbody>
          <TableSkeleton :rows="15" :columns="8" v-if="isLoading" />
          <ProductItemRow
            v-if="value"
            :product="data"
            @success:store="toggle(false)"
          />
          <template v-if="!isLoading && data?.items">
            <tr
              class="border-b border-dashed last:border-b-0 border-main"
              v-for="(product, i) in data?.items"
            >
              <td>{{ product.id }}</td>
              <td class="p-3">
                <ProductImg
                  :product="product"
                  @handle:img="
                    (data) => handleUpdate(data.product, data.img, data.value)
                  "
                />
              </td>
              <td class="p-3">
                <div
                  class="block w-full p-2 mt-1 font-bold rounded-md focus:outline-none focus:ring focus:ring-accent_dark"
                >
                  {{ data.name }}
                </div>
              </td>

              <td
                class="p-3"
                v-for="(optionValue, index) in data?.items[i].options
                  .slice()
                  .sort((a, b) => a.variation_id - b.variation_id)"
                :key="optionValue.variation_option_id"
              >
                {{ product.options[index].value }}
                <select
                  class="flex items-center justify-between w-full gap-1 px-2 py-1 pb-1 mr-4 font-bold rounded-md focus:outline-none focus:ring focus:ring-accent_dark"
                  v-model="product.options[index].value"
                  @change="
                    (event) =>
                      handleUpdate(
                        product,
                        'variations',
                        findOption(variations?.data[index], event.target.value)
                      )
                  "
                >
                  <option
                    v-for="option in variations?.data[index].options"
                    :key="option.id"
                    :value="option.value"
                  >
                    {{ option.value }}
                  </option>
                </select>
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
                <input
                  v-model="product.price"
                  placeholder="Price"
                  type="Number"
                  id="price"
                  @change="
                    (e) => handleUpdate(product, 'price', e.target.value)
                  "
                  class="block w-full p-2 mt-1 font-bold border-0 rounded-md focus:outline-none focus:ring focus:ring-accent_dark"
                />
              </td>
              <td class="p-3 pr-2 text-start">
                <input
                  v-model="product.quantity"
                  placeholder="Quantity"
                  type="Number"
                  id="quantity"
                  @change="
                    (e) => handleUpdate(product, 'quantity', e.target.value)
                  "
                  class="block w-full p-2 mt-1 font-bold border-0 rounded-md focus:outline-none focus:ring focus:ring-accent_dark"
                />
              </td>
              <td class="p-3 text-start">
                <button
                  :disabled="deleteLoading"
                  class="ml-auto relative text-main bg-accent hover:text-white hover:bg-red-500 flex items-center h-[30px] w-[30px] text-base font-medium leading-normal text-center align-middle cursor-pointer rounded-2xl transition-colors duration-200 ease-in-out shadow-none border-0 justify-center"
                  @click="() => handleDelete(product.id)"
                >
                  <Trash2 size="16" stroke-width="1.5" />
                </button>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
      <!-- <Pagination
              :currentPage="page"
              :totalPages="data?.meta.last_page ?? 1"
              :isFetching="isFetching"
              @pageChanged="handlePageChange"
            /> -->
    </div>
  </div>
</template>

<script setup>
import { watch , computed} from "vue";
import { Trash2 } from "lucide-vue-next";
import { useToggle } from "@vueuse/core";
import ProductImg from "./ProductImg.vue";
import ProductItemRow from "./ProductItemRow.vue";
import TableSkeleton from "@layouts/admin/TableSkeleton.vue";
import ToggleSwitch from "@components/admin/ToggleSwitch.vue";
import { apiQuery } from "@lib/helpers";

const props = defineProps({
  data: { type: Object },
  isLoading: { type: Boolean },
  storeValue: { type: Boolean },
});
const [value, toggle] = useToggle();

const {
  mutate: updateItem,
  isLoading: updateLoading,
  isSuccess: isSuccessUpdating,
} = apiQuery("product-items").useUpdate();
const handleUpdate = async (item, property, value) => {
  const updatedOptions = item.options.map((option) => {
    if (option.variation_id === value.variation_id) {
      return { ...value };
    }
    return option;
  });

  const form =
    property === "image"
      ? {
          ...item,
          ...value,
          options: updatedOptions,
          variations: updatedOptions.map(
            (option) => option.variation_option_id
          ),
        }
      : {
          ...item,
          options: updatedOptions,
          [property]: value,
          variations: updatedOptions.map(
            (option) => option.variation_option_id
          ),
        };
  updateItem({ id: item.id, data: form });
};
const { mutate: deleteItem, isLoading: deleteLoading } =
  apiQuery("product-items").useDelete();
const handleDelete = async (id) => {
  deleteItem(id);
};
const categoryId = computed(() => props.data.category?.id);

const { data: variations, isLoading: isLoadingVariations } = apiQuery(
  "category-variations"
).useGetById(categoryId);

watch(
  () => props.storeValue,
  (newValue) => {
    if (newValue) {
      toggle(newValue);
    }
  }
);

function findOption(data, value) {
  const option = data.options.find((option) => option.value === value);
  return {
    variation_id: data.id,
    name: data.name,
    value: option.value,
    variation_option_id: option.id,
  };
}
</script>
