<template>
  <tr class="border-b border-dashed last:border-b-0 border-main">
    <td>ID</td>
    <td class="p-3">
      <div class="flex items-center">
        <div class="flex flex-col justify-start">
          <span
            class="mb-1 font-semibold transition-colors duration-200 ease-in-out text-lg/normal text-secondary-inverse hover:text-blue-500"
          >
            <input
              id="name"
              type="text"
              ref="categoryInput"
              v-model="newCategory.name"
              placeholder="Category Name"
              @change="(e) => (newCategory.name = e.target.value)"
              class="mt-1 block w-full p-2 border border-gray rounded-md focus:outline-none focus:ring focus:ring-accent_dark"
            />
          </span>
        </div>
      </div>
      <!-- </router-link> -->
    </td>
    <td class="p-3 text-start">
      <ToggleSwitch
        v-model="newCategory.active"
        @update:toggle="(boolean) => (newCategory.active = boolean)"
      />
    </td>
    <td class="pr-0 text-start">
      <ToggleSwitch
        v-model="newCategory.promoted"
        @update:toggle="(boolean) => (newCategory.promoted = boolean)"
      />
    </td>
    <td class="p-3 text-start">
      <button
        @click="handleAddCategory"
        type="submit"
        :disabled="isLoading"
        class="text-white bg-accent_dark hover:bg-accent_light focus:ring-4 focus:ring-cyan-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center w-full"
      >
        <span v-if="isLoading" class="flex justify-center"
          ><Spinner class="w-8 h-5"
        /></span>
        <span v-else>Save</span>
      </button>
    </td>
  </tr>
</template>

<script setup>
import ToggleSwitch from "@components/admin/ToggleSwitch.vue";
import Spinner from "@components/ui/Spinner.vue";
import { apiQuery } from "@lib/helpers";
import { reactive, ref } from "vue";

const { mutate: storeCategory, isLoading } = apiQuery("categories").useStore();

const emit = defineEmits("success:store");
const newCategory = reactive({
  name: "",
  active: true,
  promoted: false,
});
const categoryInput = ref(null);

const handleAddCategory = async () => {
  await storeCategory(newCategory, {
    onSuccess: () => {
      newCategory.name = "";
      newCategory.active = true;
      newCategory.promoted = false;
      // emit("success:store");
    },
  });
  categoryInput.value.focus();
};
</script>

<style lang="scss" scoped></style>
