<template>
  <form
    @submit.prevent="handleSubmit"
    class="text-accent font-bold text-sm mt-3"
  >
    <div class="mb-4">
      <label for="name" class="block">Name</label>
      <input
        v-model="form.name"
        type="text"
        id="name"
        class="mt-1 block w-full p-2 border border-gray rounded-md focus:outline-none focus:ring focus:ring-accent_dark"
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
    <div class="flex items-cetner gap-4">
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
        class="w-full bg-red-500 text-white p-2 rounded-md hover:bg-red-600 transition duration-200"
      >
        <span v-if="!deleteLoading">Delete Category</span>
        <span v-if="deleteLoading"><Spinner /></span>
      </button>
      <button
        type="submit"
        :disabled="updateLoading"
        class="w-full bg-accent_dark text-white p-2 rounded-md hover:bg-accent_light transition duration-200"
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
