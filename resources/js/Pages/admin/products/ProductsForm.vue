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
            <p class="text-red-500" v-if="errors.name">{{ errors.name }}</p>
        </div>

        <div class="mb-4">
            <label
                for="image"
                class="block mb-2 text-sm font-medium text-accent_dark"
                >Image</label
            >
            <input
                type="file"
                @change="handleImageUpload"
                class="block w-full text-sm text-accent_dark file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-accent_dark file:text-white hover:file:bg-accent focus:ring-accent_dark focus:border-accent_dark"
            />
            <p
                v-if="typeof form.image === 'string'"
                class="mt-3 text-sm text-accent_light"
            >
                {{ form.image }}
            </p>
            <p v-if="errors.image" class="text-sm text-red-500">
                {{ errors.image }}
            </p>
        </div>

        <div class="mb-4">
            <label for="description" class="block">Description</label>
            <textarea
                v-model="form.description"
                id="description"
                rows="3"
                class="block w-full p-2 mt-1 border rounded-md border-gray focus:outline-none focus:ring focus:ring-accent_dark"
            ></textarea>
            <p class="text-red-500" v-if="errors.description">
                {{ errors.description }}
            </p>
        </div>

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

        <div class="flex items-center gap-3">
            <button
                @click="handleDelete"
                :disabled="deleteLoading"
                class="w-full p-2 text-white transition duration-200 bg-red-500 rounded-md hover:bg-red-600"
            >
                <span v-if="!deleteLoading">Delete Product</span>
                <span v-if="deleteLoading"><Spinner /></span>
            </button>
            <button
                type="submit"
                :disabled="updateLoading"
                class="w-full p-2 text-white transition duration-200 rounded-md bg-accent_dark hover:bg-accent_light"
            >
                <span v-if="!updateLoading">Update Product</span>
                <span v-if="updateLoading"><Spinner /></span>
            </button>
        </div>
    </form>
</template>

<script setup>
import { reactive, onMounted, computed } from "vue";
import { useRoute } from "vue-router";
import { object, string, boolean } from "yup";
import Spinner from "@components/ui/Spinner.vue";
import { useRouter } from "vue-router";
import { apiQuery } from "@lib/helpers";

const route = useRoute();
const router = useRouter();
const id = computed(() => route.params.id);

const { data, error, isLoading } = apiQuery("products").useGetById(id);

const form = reactive({
    id: null,
    name: "",
    image: "",
    description: "",
    active: false,
});
const errors = reactive({
    id: null,
    name: null,
    image: null,
    description: null,
    active: false,
});
const validationSchema = object({
    name: string().optional(),
    description: string().optional(),
    active: boolean().optional(),
});
onMounted(() => {
    const productData = data.value.data.data;
    form.id = productData.id;
    form.name = productData.name;
    form.image = productData.image;
    form.description = productData.description;
    form.active = productData.active;
});
const {
    mutate: updateProduct,
    isLoading: updateLoading,
    isSuccess: updateSuccess,
    error: updateErrorMessage,
} = apiQuery("products").useUpdate();
const {
    mutate: deleteProduct,
    isLoading: deleteLoading,
    isSuccess: deleteSuccess,
    error: deleteErrorMessage,
} = apiQuery("products").useDelete();
const handleSubmit = async () => {
    try {
        await validationSchema.validate(form, { abortEarly: false });
        updateProduct({ id: id, data: form });
        // router.push("/products");
    } catch (err) {
        err.inner.forEach((error) => {
            errors[error.path] = error.message;
        });
    }
};

const handleImageUpload = (e) => {
    const file = e.target.files[0];
    form.image = file;
};
const handleDelete = async () => {
    deleteProduct(id);
    router.push("/products");
};
</script>

<style>
/* Add any additional styling here */
</style>
