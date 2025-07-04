<template>
    <tr class="border-b border-dashed last:border-b-0 border-main">
        <td>ID</td>
        <td class="p-3">
            <div class="relative inline-block shrink-0 rounded-2xl me-3">
                <ProductImg
                    :product="newProductItem"
                    @handle:img="
                        (data) => Object.assign(newProductItem, data.value)
                    "
                />
            </div>
        </td>
        <td class="p-3 font-bold">
            {{ newProductItem.name }}
        </td>
        <template v-for="v in variations?.data" :key="v.id">
            <td
                class="p-3 font-bold rounded-md focus:outline-none focus:ring focus:ring-accent_dark"
                :value="v.id"
            >
                <select
                    v-model="newProductItem.variations[v.id]"
                    :name="v.name"
                    ref="OptionSelect"
                    class="w-full p-2 border rounded"
                >
                    <option disabled value="">Select {{ v.name }}</option>
                    <option
                        v-for="vo in v?.options"
                        :key="vo.id"
                        :value="vo.id"
                    >
                        {{ vo.value }}
                    </option>
                </select>
            </td>
        </template>
        <td class="pr-0 text-start" v-if="isLoadingVariations">
            <span
                class="p-3 border-b border-dashed last:border-b-0 border-main animate-pulse"
            >
                <div class="w-full h-10 rounded bg-gray"></div>
            </span>
        </td>
        <td class="pr-0 text-start" v-if="isLoadingVariations">
            <span
                class="p-3 border-b border-dashed last:border-b-0 border-main animate-pulse"
            >
                <div class="w-full h-10 rounded bg-gray"></div>
            </span>
        </td>
        <td class="p-3 text-start">
            <ToggleSwitch
                v-model="newProductItem.active"
                @update:toggle="(boolean) => (newProductItem.active = boolean)"
            />
        </td>
        <td class="p-3 text-start">
            <input
                v-model="newProductItem.price"
                placeholder="Price"
                type="Number"
                id="price"
                class="block w-full p-2 mt-1 font-bold border-0 rounded-md focus:outline-none focus:ring focus:ring-accent_dark"
            />
        </td>
        <td class="p-3 text-start">
            <input
                v-model="newProductItem.quantity"
                placeholder="Quantity"
                type="Number"
                id="quantity"
                class="block w-full p-2 mt-1 font-bold border-0 rounded-md focus:outline-none focus:ring focus:ring-accent_dark"
            />
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
import { reactive, ref, computed } from "vue";
import ProductImg from "./ProductImg.vue";
import { apiQuery } from "@lib/helpers";
import Spinner from "@components/ui/Spinner.vue";
import ToggleSwitch from "@components/admin/ToggleSwitch.vue";

const props = defineProps({ product: { type: Object, required: true } });

const { mutate: storeProductItem, isLoading: isLoadingStoring } =
    apiQuery("product-items").useStore();
const id = computed(() => props.product?.category?.id);
const { data: variations, isLoading: isLoadingVariations } = apiQuery(
    "category-variations"
).useGetById(id);

const emit = defineEmits("success:store");
const newProductItem = reactive({
    active: true,
    image: null,
    name: props.product.name,
    variations: [],
    price: null,
    quantity: null,
    product_id: props.product.id,
});
const OptionSelect = ref(null);
const handleAddProduct = async () => {
    newProductItem.active = newProductItem.active ? 1 : 0;
    newProductItem.variations = Object.values(newProductItem.variations).filter(
        (id) => id !== null && id !== ""
    );
    newProductItem.category_id = props.product.category?.id ?? null;
    await storeProductItem(newProductItem, {
        onSuccess: () => {
            newProductItem.variations = [];
            newProductItem.image = null;
        },
        onError: () => {
            newProductItem.variations = [];
        },
    });
};
</script>

<style lang="scss" scoped></style>
