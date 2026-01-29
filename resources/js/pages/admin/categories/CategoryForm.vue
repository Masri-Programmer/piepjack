<template>
  <form
    @submit.prevent="handleSubmit"
    class="mt-3 text-sm font-bold text-accent"
  >
    <div class="mb-4">
      <label for="name" class="block">Name</label>
      <input
        v-model="form.name"
        type="text"
        id="name"
        class="block w-full p-2 mt-1 border rounded-md border-gray focus:outline-none focus:ring focus:ring-accent_dark"
      />
    </div>
    <!-- <div class="mb-4">
      <select
        v-model="form.parent_id"
        id="category_id"
        class="shadow-md border border-gray text-accent_dark sm:text-sm rounded-lg focus:ring-accent_dark focus:border-accent_dark block w-full p-2.5"
      >
        <option value="" disabled>Select a category</option>
                        <option
                          v-for="category in data?.data"
                          :key="category.id"
                          :value="category.id"
                        >
                          {{ category.name }}
                        </option>
      </select>
    </div> -->
    <div class="flex gap-4 items-cetner">
      <div class="mb-4">
        <label for="active" class="flex items-center">
          <input
            v-model="form.active"
            type="checkbox"
            id="active"
            class="mr-2"
          />
          Active
        </label>
      </div>
      <div class="mb-4">
        <label for="promoted" class="flex items-center">
          <input
            v-model="form.promoted"
            type="checkbox"
            id="promoted"
            class="mr-2"
          />
          Promoted
        </label>
      </div>
    </div>
    <div class="flex items-center gap-3 mb-3">
      <button
        @click="handleDelete"
        :disabled="deleteLoading"
        class="w-full p-2 text-white transition duration-200 bg-red-500 rounded-md hover:bg-red-600"
      >
        <span v-if="!deleteLoading">Delete Category</span>
        <span v-if="deleteLoading"><Spinner /></span>
      </button>
      <button
        type="submit"
        :disabled="updateLoading"
        class="w-full p-2 text-white transition duration-200 rounded-md bg-accent_dark hover:bg-accent_light"
      >
        <span v-if="!updateLoading">Update Category</span>
        <span v-if="updateLoading"><Spinner /></span>
      </button>
    </div>
  </form>
</template>

<script setup>
import { reactive, onMounted } from "vue";
import { apiQuery } from "@lib/helpers";

const props = defineProps({
  data: { required: true },
});
const form = reactive({
  id: null,
  name: "",
  parent_id: null,
  active: false,
  promoted: false,
});
onMounted(() => {
  const categoryData = props.data.data;
  form.id = categoryData.id;
  form.name = categoryData.name;
  form.image = categoryData.image;
  form.description = categoryData.description;
  form.active = categoryData.active;
  form.promoted = categoryData.promoted;
});

const { mutate: updateCategory, isLoading: updateLoading } =
    apiQuery("categories").useUpdate();
const { mutate: deleteCategory, isLoading: deleteLoading } =
    apiQuery("categories").useDelete();
const handleSubmit = async () => {
  // form.name=form.name === categoryData.name ? '':form.name
  await updateCategory({ id: form.id, data: form });
};
const handleDelete = async () => {
  deleteCategory(id);
  router.push("/categories");
};
</script>
