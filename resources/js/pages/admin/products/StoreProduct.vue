<template>
  <PageLayout :data="links" title="Add Product">
    <div class="space-y-6">
      <form @submit.prevent="submitForm">
        <div class="grid grid-cols-6 gap-6">
          <div class="col-span-6 sm:col-span-3">
            <label
              for="name"
              class="text-sm font-medium text-accent_dark block mb-2"
              >Product Name</label
            >
            <input
              type="text"
              v-model="form.name"
              id="name"
              class="shadow-md border border-gray text-accent_dark sm:text-sm rounded-lg focus:ring-accent_dark focus:border-accent_dark block w-full p-2.5"
              :class="{ 'border-red-500': errors.name }"
              placeholder="Name”"
            />
            <p v-if="errors.name" class="text-red-500 text-sm">
              {{ errors.name }}
            </p>
          </div>

          <div class="col-span-6 sm:col-span-3">
            <label
              for="category_id"
              class="text-sm font-medium text-accent_dark block mb-2"
              >Category</label
            >
            <select
              v-model="form.category_id"
              id="category_id"
              class="shadow-md border border-gray text-accent_dark sm:text-sm rounded-lg focus:ring-accent_dark focus:border-accent_dark block w-full p-2.5"
              :class="{ 'border-red-500': errors.category_id }"
            >
              <option value="" disabled>Select a category</option>
              <option
                v-for="category in data"
                :key="category.id"
                :value="category.id"
              >
                {{ category.name }}
              </option>
            </select>
            <p v-if="errors.category_id" class="text-red-500 text-sm">
              {{ errors.category_id }}
            </p>
          </div>

          <div class="col-span-full">
            <label
              for="description"
              class="text-sm font-medium text-accent_dark block mb-2"
              >Product Details</label
            >
            <textarea
              v-model="form.description"
              id="description"
              rows="6"
              class="border border-gray text-accent_dark sm:text-sm rounded-lg focus:ring-accent_dark focus:border-accent_dark block w-full p-4"
              :class="{ 'border-red-500': errors.description }"
              placeholder="Details"
            ></textarea>
            <p v-if="errors.description" class="text-red-500 text-sm">
              {{ errors.description }}
            </p>
          </div>

          <div class="col-span-full">
            <label
              for="image"
              class="text-sm font-medium text-accent_dark block mb-2"
              >Image</label
            >
            <input
              type="file"
              @change="handleImageUpload"
              class="block w-full text-sm text-accent_dark file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-accent_dark file:text-white hover:file:bg-accent focus:ring-accent_dark focus:border-accent_dark"
            />
            <p v-if="errors.image" class="text-red-500 text-sm">
              {{ errors.image }}
            </p>
          </div>

          <div class="col-span-full flex">
            <input
              type="checkbox"
              v-model="form.active"
              id="active"
              class="mr-2"
            />
            <label
              for="active"
              class="text-sm font-medium text-accent_dark mb-2"
              >Active</label
            >
            <p v-if="errors.active" class="text-red-500 text-sm">
              {{ errors.active }}
            </p>
          </div>
        </div>
        <div class="p-6 ps-0 pb-0 border-t border-gray-200 rounded-b">
          <button
            class="text-white bg-accent_dark hover:bg-accent_light focus:ring-4 focus:ring-accent_light font-medium rounded-lg text-sm px-5 py-2.5 text-center"
            type="submit"
          >
            Save all
          </button>
        </div>
      </form>
    </div>
  </PageLayout>
</template>

<script setup>
import { ref } from "vue";
import * as yup from "yup";
import { useRouter } from "vue-router";
import PageLayout from "@layouts/admin/PageLayout.vue";
import { apiQuery } from "@lib/helpers";
const { data } = apiQuery("all-categories").useGet({});
const form = ref({
  name: "",
  category_id: "",
  description: "",
  image: null,
  active: false,
});

const errors = ref({
  name: null,
  category_id: null,
  description: null,
  image: null,
  active: null,
});

const validationSchema = yup.object().shape({
  name: yup.string().required("Product name is required").max(1024),
  category_id: yup
    .number()
    .nullable()
    .typeError("Category must be a valid number"),
  description: yup.string().nullable(),
  image: yup
    .mixed()
    .nullable()
    .test("fileType", "Only image files are allowed", (value) => {
      return value ? ["image/jpeg", "image/png"].includes(value.type) : true;
    }),
  active: yup.boolean().required("Active status is required"),
});
const {
  mutate: storeProduct,
  isSuccess: productSuccess,
  error: productErrorMessage,
} = apiQuery("products").useStore();

const router = useRouter();
const submitForm = async () => {
  try {
    await validationSchema.validate(form.value, { abortEarly: false });
    form.value.active = form.value.active ? 1 : 0;
    storeProduct(form.value);
  } catch (validationError) {
    errors.value = {};
    validationError.inner.forEach((err) => {
      errors.value[err.path] = err.message;
      productErrorMessage;
    });
  }
};

const handleImageUpload = (e) => {
  const file = e.target.files[0];
  form.value.image = file;
};
const links = ref([
  {
    title: "Home",
    link: "/",
  },
  {
    title: "Products",
    current: true,
    link: "/products",
  },
  {
    title: "Add Product",
    current: true,
    link: "",
  },
]);
</script>

<style lang="scss" scoped></style>
