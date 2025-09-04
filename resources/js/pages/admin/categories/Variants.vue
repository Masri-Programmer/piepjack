<template>
  <h5 class="text-xl font-bold text-accent">{{ $t("admin.variants.title") }}</h5>
  <div class="flex flex-wrap items-start gap-5 my-3 text-accent">
    <template v-if="data?.data" class="my-3">
      <div
        v-for="v in data.data"
        :key="v.id"
        class="flex flex-row items-start space-x-2"
      >
        <Badge :colorClass="'bg-slate-400'">
          {{ v.name }}
        </Badge>
        <div v-if="v.options" class="grid gap-2 m-0">
          <Badge
            v-for="o in v.options"
            :key="o.id"
            :delete="true"
            :colorClass="'bg-indigo-400'"
            :closeButtonClass="'bg-indigo-400'"
            @handle:delete="deleteVariantOption(o.id)"
          >
            {{ o.value }}
          </Badge>
        </div>
      </div>
    </template>

    <template v-else> {{ $t("admin.variants.noVariantsAdded") }} </template>
  </div>
  <div class="flex items-center text-accent">
    <div>
      </div>
    <div>
      <button
        type="button"
        @click="toggleOption(!option)"
        class="p-2 text-white transition duration-200 rounded-md bg-accent_dark hover:bg-accent_light"
      >
        <span>{{ $t("admin.variants.addOption") }}</span>
      </button>
    </div>
  </div>
  <div class="flex items-center gap-4 my-3">
    <div class="flex items-center gap-3 my-3 text-accent" v-if="variant">
      <label for="variant" class="font-bold">{{
        $t("admin.variants.newVariantLabel")
      }}</label>
      <input
        v-model="newVariant.name"
        type="text"
        :placeholder="$t('admin.variants.variantPlaceholder')"
        name="variant"
        @change="handleStoreVariant"
        id="variant"
        class="block p-2 mt-1 border rounded-md border-gray focus:outline-none focus:ring focus:ring-accent_dark"
      />
    </div>
    <div class="flex items-center gap-3 my-3 text-accent" v-if="option">
      <label for="variant" class="font-bold">{{
        $t("admin.variants.newOptionLabel")
      }}</label>
      <select
        v-model="newOption.variation_id"
        name="variants"
        id="variants"
        class="block p-2 mt-1 border rounded-md border-gray focus:outline-none focus:ring focus:ring-accent_dark"
      >
        <option value="" disabled>{{ $t("admin.variants.selectVariant") }}</option>
        <option v-for="v in data?.data" :key="v.id" :value="v.id">
          {{ v.name }}
        </option>
      </select>
      <input
        v-model="newOption.value"
        type="text"
        name="option"
        :placeholder="$t('admin.variants.optionPlaceholder')"
        @change="handleStoreOption"
        id="option"
        class="block p-2 mt-1 border rounded-md border-gray focus:outline-none focus:ring focus:ring-accent_dark"
      />
    </div>
  </div>
</template>

<script setup>
import Badge from "@components/admin/Badge.vue";
import { apiQuery } from "@lib/helpers";
import { useToggle } from "@vueuse/core";
import { useRoute } from "vue-router";
import { computed, reactive } from "vue";
// No need to import useI18n as it's globally available in this setup

const route = useRoute();
const id = computed(() => route.params.id);
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
  apiQuery("variation-options").useStore();
const { mutate: deleteVariant, isLoading: deleteLoading } =
  apiQuery("variations").useDelete();
const { mutate: deleteVariantOption, isLoading: deleteOptionLoading } =
  apiQuery("variation-options").useDelete();
const { data, isLoading: isLoading } =
  apiQuery("category-variations").useGetById(id);

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