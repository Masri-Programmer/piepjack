<template>
    <router-link
        :to="'/product/' + product.id + '/' + product.slug"
        class="relative flex flex-col items-center w-full overflow-hidden text-xs text-center uppercase cursor-pointer group"
        @mouseenter="mouseEnter"
        @mouseleave="mouseLeave"
        v-if="product"
    >
        <!-- max-w-[180px] sm:max-w-[220px] md:max-w-[250px] -->
        <div
            v-if="isNewProduct"
            class="absolute px-2 py-1 text-xs font-bold transition-transform duration-300 shadow-md top-6 left-2 bg-main text-accent_dark z-9 group-hover:scale-110"
        >
            <p>{{ $t("common.product.new") }}</p>
        </div>
        <div class="relative w-full overflow-hidden aspect-3/4">
            <img
                :src="product.image_url || NoImg"
                :alt="product.name"
                width="1667"
                height="2500"
                loading="lazy"
                class="object-cover w-full h-full transition-transform duration-300 group-hover:scale-110"
            />
        </div>
        <div
            class="absolute bottom-0 flex flex-col items-center w-full h-16 p-2 px-2 mt-2 bg-accent/50 md:p-0 md:relative md:h-auto"
        >
            <p class="max-w-full text-sm font-semibold sm:text-base">
                {{ product.name }}
            </p>
            <p v-if="product.price" class="text-xs text-gray-600 sm:text-sm">
                {{ $t("common.product.ab") }} {{ product.price }}
                {{ $currency }}
            </p>
        </div>
    </router-link>
</template>

<script setup>
import { ref } from "vue";
import NoImg from "@img/no-image.jpg";
import { ShoppingCart } from "lucide-vue-next";
import { useNow } from "@vueuse/core";
import { computed } from "vue";
const props = defineProps({
    product: {
        type: Object,
        default: () => ({
            id: 1,
            title: "Title",
            slug: "Slug",
            description: "Qui ut doloribus eos quisquam fuga sit.",
            image_url: "",
            image_mime: "image/jpeg",
            image_size: 1279,
            price: "89.91",
        }),
    },
});
const imgHover = ref(false);
const mouseEnter = () => (imgHover.value = true);
const mouseLeave = () => (imgHover.value = false);
const now = useNow();

const isNewProduct = computed(() => {
    const createdAt = new Date(props.product.created_at);
    const daysDifference = (now.value - createdAt) / (1000 * 60 * 60 * 24);
    return daysDifference <= 7;
});
</script>
