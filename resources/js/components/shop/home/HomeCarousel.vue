<template>
    <div class="w-full" v-if="products?.data?.length">
        <h1
            class="mb-6 text-2xl font-extrabold text-center md:text-3xl md:mb-12 text-foreground"
        >
            {{ title }}
        </h1>

        <Carousel
            v-bind="settings"
            :breakpoints="breakpoints"
            class="vue-carousel"
        >
            <Slide
                v-for="(item, index) in displayItems"
                :key="item.id || 'placeholder-' + index"
                class="px-2 pb-4"
            >
                <div class="carousel__item w-full h-full text-left">
                    <ProductCard
                        :product="item.isPlaceholder ? null : item"
                        :isComingSoon="item.isPlaceholder"
                    />
                </div>
            </Slide>

            <template #addons>
                <Navigation />
            </template>
        </Carousel>

        <div class="flex flex-col items-center justify-center mt-8">
            <router-link :to="'/collections/' + id + '/' + slug">
                <Button class="view-all">
                    {{ $t("components.buttons.viewAll") }}
                </Button>
            </router-link>
        </div>
    </div>
</template>
<script setup>
import { Carousel, Navigation, Slide } from "vue3-carousel";
import ProductCard from "../product/ProductCard.vue";
import { Button } from "@/js/components/ui/button";
import "@assets/css/carousel/homeCarousel.css";
import "vue3-carousel/dist/carousel.css";
import { ref, computed } from "vue";
import { apiQuery } from "@lib/helpers";
const props = defineProps({
    id: { type: Number, required: true },
    name: { type: String, required: true },
    slug: { type: String, required: true },
    title: { type: String, required: true },
});
const params = ref({
    active: null,
    category_id: props.id,
    page: 1,
    per_page: 25,
    search: "",
    sort_direction: "",
    sort_field: "",
});
const {
    data: products,
    error,
    isLoading,
} = apiQuery("products").useGet(params);

const displayItems = computed(() => {
    const items = [...(products.value?.data || [])];
    const minItems = 4;

    if (items.length < minItems) {
        const diff = minItems - items.length;
        for (let i = 0; i < diff; i++) {
            items.push({ isPlaceholder: true });
        }
    }

    return items;
});

const settings = {
    itemsToShow: 1.2,
    itemsToScroll: 1,
    snapAlign: "start",
};

const breakpoints = {
    200: {
        itemsToShow: 1.2,
        itemsToScroll: 1,
        snapAlign: "start",
    },
    // 640px and up
    640: {
        itemsToShow: 2.2,
        itemsToScroll: 1,
        snapAlign: "start",
    },
    // 768px and up
    768: {
        itemsToShow: 3,
        itemsToScroll: 1,
        snapAlign: "start",
    },
    // 1024px and up
    1024: {
        itemsToShow: 4,
        itemsToScroll: 1,
        snapAlign: "start",
    },
};
</script>
