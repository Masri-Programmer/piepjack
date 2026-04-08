<template>
    <PageLayout
        :title="product?.name || 'Product'"
        :description="product?.description?.substring(0, 160)"
    >
        <Spinner v-if="isLoading" :center="true" class="w-full h-screen" />
        <div
            v-else-if="error && !product"
            class="p-3 my-3 text-xl text-red-500 border-t border-b border-neutral-200 font-bold"
        >
            {{ error }}
        </div>
        <div v-else class="space-y-24 md:space-y-32">
            <section>
                <ProductContent :data="product" />
            </section>

            <section>
                <HomeCarousel
                    :name="product?.category_name"
                    :id="product?.category_id"
                    :title="$t('common.titles.peopleAlsoLiked')"
                />
            </section>

            <section>
                <ProductReview :data="product" />
            </section>
        </div>
    </PageLayout>
</template>

<script setup>
import { useRoute } from "vue-router";
import { computed } from "vue";
import PageLayout from "@components/shop/general/PageLayout.vue";
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
