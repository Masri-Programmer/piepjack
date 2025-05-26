<template>
  <h5 class="font-bold text-accent text-xl">Variants</h5>
  <div class="flex items-start gap-5 text-accent my-3 flex-wrap">
    <template v-if="data?.data" class="my-3">
      <div
        v-for="v in data.data"
        :key="v.id"
        class="flex flex-row items-start space-x-2"
      >
        <Badge :colorClass="'bg-slate-400'">
          <!-- @handle:delete="deleteVariant(v.id)" -->
          {{ v.name }}
        </Badge>
        <div v-if="v.options" class="grid gap-2 m-0">
          <Badge
            v-for="o in v.options"
            :key="o.id"
            :delete="true"
            :colorClass="'bg-indigo-400'"
            :closeButtonClass="'bg-indigo-400'"
            @handle:delete="deleteVariantOption(v.id)"
          >
            {{ o.value }}
          </Badge>
        </div>
      </div>
    </template>

    <template v-else> No Variants Added </template>
  </div>
  <div class="flex items-center text-accent">
    <div>
      <!-- <button
        type="button"
        @click="toggleVariant(!variant)"
        class="bg-accent_dark text-white p-2 rounded-md hover:bg-accent_light transition duration-200 me-3"
      >
        <span>Add Variant</span>
      </button> -->
    </div>
    <div>
      <button
        type="button"
        @click="toggleOption(!option)"
        class="bg-accent_dark text-white p-2 rounded-md hover:bg-accent_light transition duration-200"
      >
        <span>Add Option</span>
      </button>
    </div>
  </div>
  <div class="flex items-center gap-4 my-3">
    <div class="flex items-center gap-3 my-3 text-accent" v-if="variant">
      <label for="variant" class="font-bold">New Variant</label>
      <input
        v-model="newVariant.name"
        type="text"
        placeholder="Variant"
        name="variant"
        @change="handleStoreVariant"
        id="variant"
        class="mt-1 block p-2 border border-gray rounded-md focus:outline-none focus:ring focus:ring-accent_dark"
      />
    </div>
    <div class="flex items-center gap-3 my-3 text-accent" v-if="option">
      <label for="variant" class="font-bold">New Option</label>
      <select
        v-model="newOption.variation_id"
        name="variants"
        id="variants"
        class="mt-1 block p-2 border border-gray rounded-md focus:outline-none focus:ring focus:ring-accent_dark"
      >
        <option value="" disabled>Select a variant</option>
        <option v-for="v in data?.data" :key="v.id" :value="v.id">
          {{ v.name }}
        </option>
      </select>
      <input
        v-model="newOption.value"
        type="text"
        name="option"
        placeholder="Option"
        @change="handleStoreOption"
        id="option"
        class="mt-1 block p-2 border border-gray rounded-md focus:outline-none focus:ring focus:ring-accent_dark"
      />
    </div>
  </div>
</template>

<script setup>
import Badge from "@components/admin/Badge.vue";
import { apiQuery } from "@lib/helpers";
import { useToggle } from "@vueuse/core";
import { useRoute } from "vue-router";
import { reactive } from "vue";

const route = useRoute();
const id = route.params.id;
const newVariant = reactive({
  category_id: id,
  name: "",
});
const newOption = reactive({
  variation_id: null,
  value: "",
});
const { mutate: storeVariant, isLoading: storeLoading } =
  apiQuery("variations").useStore();
const { mutate: storeOption, isLoading: storeOptionLoading } =
  apiQuery("variations-options").useStore();
const { mutate: deleteVariant, isLoading: deleteLoading } =
    apiQuery("variations").useDelete();
const { mutate: deleteVariantOption, isLoading: deleteOptionLoading } =
  apiQuery("variation-options").useDelete();
const { data, isLoading: isLoading } = apiQuery("category-variations").useGetById(id);

const handleStoreVariant = async () => {
  await storeVariant(newVariant, {
    onSuccess: () => {
      newVariant.name = "";
      newVariant.variation_id = null;
    },
  });
};
const handleStoreOption = async () => {
  await storeOption(newOption, {
    onSuccess: () => {
      newOption.value = "";
    },
  });
};

const [variant, toggleVariant] = useToggle();
const [option, toggleOption] = useToggle();
</script>

<style lang="scss" scoped></style>
