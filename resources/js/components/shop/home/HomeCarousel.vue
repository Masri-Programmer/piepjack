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
                v-for="product in products.data"
                :key="product.id"
                class="px-2 pb-4"
            >
                <div class="carousel__item w-full h-full text-left">
                    <ProductCard :product="product" />
                </div>
            </Slide>

            <template #addons>
                <Navigation />
            </template>
        </Carousel>

        <div class="flex flex-col items-center justify-center mt-8">
            <router-link :to="'/collections/' + id + '/' + slug">
                <Button variant="outline" size="lg" class="text-xs view-all">
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
import { ref } from "vue";
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

const settings = {
    itemsToShow: 2,
    itemsToScroll: 1,
    snapAlign: "center",
};

const breakpoints = {
    480: {
        itemsToShow: 2,
        itemsToScroll: 3,
    },
    768: {
        itemsToShow: 3,
        itemsToScroll: 2,
    },

    1024: {
        itemsToShow: 4,
        itemsToScroll: 2,
    },
    1204: {
        itemsToShow: 4,
        itemsToScroll: 2,
    },
    1600: {
        itemsToShow: 4,
        itemsToScroll: 2,
    },
};
</script>
