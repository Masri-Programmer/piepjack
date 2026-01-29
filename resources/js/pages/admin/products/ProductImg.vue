<template>
    <div
        class="relative inline-block shrink-0 rounded-2xl me-3 w-[100px] h-[100px] shadow-xl"
    >
        <input
            type="file"
            accept="image/*"
            @change="(e) => handleImageUpload(e, product)"
            class="hidden"
            ref="imageInput"
        />
        <img
            v-if="!uploadLoading"
            :src="product.image || product.image_url || defaultImage"
            class="inline-block object-contain w-full h-full cursor-pointer shrink-0 rounded-2xl"
            alt="Image"
            loading="lazy"
            @click="triggerFileInput"
        />
        <Spinner
            v-if="uploadLoading"
            class="inline-block object-contain w-full h-full shrink-0 rounded-2xl bg-lime-500"
        />
    </div>
</template>

<script setup>
import { ref } from "vue";
import Spinner from "@components/ui/Spinner.vue";
import { useUploadImgMutation } from "@lib/form";
const emit = defineEmits("handle:img");
const props = defineProps({
    product: { type: Object },
});
const defaultImage = "https://placehold.co/600x400.png?text=Product+Image";
const imageInput = ref(null);
const { mutate: upload, isLoading: uploadLoading } = useUploadImgMutation();

const handleImageUpload = (event, product) => {
    const file = event.target.files[0];
    upload(file, {
        onSuccess: (data) => {
            product.image = data.image;
            emit("handle:img", {
                product,
                img: "image",
                value: {
                    image: data.image_url,
                    image_mime: data.image_mime,
                    image_size: data.image_size,
                },
            });
        },
    });
};

const triggerFileInput = (event) => {
    event.preventDefault();
    if (imageInput.value) {
        imageInput.value.click();
    }
};
</script>

<style lang="scss" scoped></style>
