<template>
    <Spinner v-if="isLoading" :center="true" class="w-full h-screen" />
    <div
        v-else-if="error && !product"
        class="p-3 my-3 text-xl text-red-500 border-t border-b border-neutral-200 ont-bold"
    >
        {{ error }}
    </div>
    <template v-else>
        <div class="container flex flex-col w-full gap-12 md:flex-row">
            <ProductContent :data="product" />
        </div>
        <!-- <hr class="my-6 -mx-4 text-gray md:-mx-8 2xl:-mx-16" /> -->
        <div class="container">
            <HomeCarousel
                :name="product?.category_name"
                :id="product?.category_id"
                :title="$t('common.titles.peopleAlsoLiked')"
            />
            <ProductReview :data="product" />
        </div>
    </template>
</template>

<script setup>
import { useRoute } from "vue-router";
import { computed } from "vue";
import Spinner from "@components/ui/Spinner.vue";
import ProductContent from "./ProductContent.vue";
import HomeCarousel from "@components/shop/home/HomeCarousel.vue";
import ProductReview from "./ProductReview.vue";
import { apiQuery } from "@lib/helpers";

const route = useRoute();
const productId = computed(() => route.params.id);

const {
    data: productData,
    error,
    isLoading,
} = apiQuery("products").useGetById(productId);

const product = computed(() => productData.value?.data);
</script>
