<template>
  <tr class="border-b border-dashed last:border-b-0 border-main">
    <td>ID</td>
    <td class="p-3">
      <div class="relative inline-block shrink-0 rounded-2xl me-3">
        <ProductImg
          :product="newProduct"
          @handle:img="(data) => Object.assign(newProduct, data.value)"
        />
      </div>
    </td>
    <td class="p-3">
      <textarea
        v-model="newProduct.name"
        placeholder="Product Name"
        ref="ProductInput"
        type="text"
        id="name"
        class="block w-full p-2 mt-1 font-bold rounded-md focus:outline-none focus:ring focus:ring-accent_dark"
      />
    </td>
    <td class="p-3 text-start">
      <textarea
        v-model="newProduct.description"
        type="text"
        id="description"
        class="block w-full p-2 mt-1 font-bold rounded-md focus:outline-none focus:ring focus:ring-accent_dark"
        placeholder="No description"
      />
    </td>
    <td class="p-3 text-start">
      <ToggleSwitch
        v-model="newProduct.active"
        @update:toggle="(boolean) => (newProduct.active = boolean)"
      />
    </td>
    <td class="pr-0 text-start">
      <div
        class="text-center align-baseline inline-flex px-4 py-3 mr-auto items-center font-semibold text-[.95rem] leading-none rounded-lg w-full"
      >
        <select
          :v-model="newProduct.category_id"
          id="category_id"
          @change="(e) => (newProduct.category_id = e.target.value)"
          class="shadow-md border border-gray text-accent_dark sm:text-sm rounded-lg focus:ring-accent_dark focus:border-accent_dark block w-full p-2.5"
        >
          <option value="" disabled>Select a category</option>
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
    <td class="p-3 pr-0">
      <button
        @click="handleAddProduct"
        type="submit"
        :disabled="isLoadingStoring"
        class="text-white bg-accent_dark hover:bg-accent_light focus:ring-4 focus:ring-cyan-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center w-full"
      >
        <span v-if="isLoadingStoring" class="flex justify-center"
          ><Spinner class="w-8 h-5"
        /></span>
        <span v-else>Save</span>
      </button>
    </td>
  </tr>
</template>

<script setup>
import { reactive, ref } from "vue";
import ProductImg from "./ProductImg.vue";
import { apiQuery } from "@lib/helpers";
import Spinner from "@components/ui/Spinner.vue";
import ToggleSwitch from "@components/admin/ToggleSwitch.vue";

const { data: categoriesData } = apiQuery("all-categories").useGet({});
const { mutate: storeProduct, isLoading: isLoadingStoring } =
apiQuery("products").useStore();

const emit = defineEmits("success:store");
const newProduct = reactive({
  name: "",
  category_id: null,
  description: "",
  image: null,
  active: true,
});
const ProductInput = ref(null);
const handleAddProduct = async () => {
  newProduct.active = newProduct.active ? 1 : 0;
  newProduct.category_id = newProduct.category_id ? newProduct.category_id :
 categoriesData.value?.data?.length
      ? categoriesData.value?.data[0].id
      : null;
  await storeProduct(newProduct, {
    onSuccess: () => {
      newProduct.name = "";
      newProduct.active = true;
      newProduct.category = {
        id: null,
        name: "",
        active: true,
        promoted: false,
        parent_id: null,
      };
      newProduct.description = "";
      newProduct.image = null;
      // emit("success:store");
    },
  });
  ProductInput.value.focus();
};
</script>

<style lang="scss" scoped></style>
