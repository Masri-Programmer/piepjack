<template>
    <PageLayout
        title="Collections"
        description="Explore our latest collections and find your perfect fit."
    >
        <div v-if="error" class="error-message p-4 mb-8 bg-destructive/5 text-destructive border border-destructive/10">
            {{ error }}
        </div>
        
        <template v-if="products?.data.length">
            <div class="space-y-24 md:space-y-32">
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 sm:gap-6">
                    <template v-for="product in products.data" :key="product.id">
                        <ProductCard :product="product" />
                    </template>
                </div>
                
                <div class="flex justify-center mt-12">
                    <Pagination
                        :pagination="products?.meta"
                        :links="products?.links"
                        @page-change="changePage"
                    />
                </div>
            </div>
        </template>
        
        <template v-else>
            <div v-if="!isLoading" class="text-center py-24">
                <h1 class="text-2xl font-bold uppercase tracking-widest text-muted-foreground">
                    {{ $t("common.product.notFound") }}
                </h1>
            </div>
        </template>
    </PageLayout>
</template>

<script setup>
import ProductCard from "@components/shop/product/ProductCard.vue";
import Pagination from "@layouts/shop/Pagination.vue";
import PageLayout from "@components/shop/general/PageLayout.vue";
import { useRoute } from "vue-router";
import { computed } from "vue";
import { apiQuery } from "@lib/helpers";

const route = useRoute();
const page = computed(() => parseInt(route.query.page) || 1);
const params = computed(() => ({
    active: null,
    category_id: route.params.id,
    page: page.value,
    per_page: 15,
    search: "",
    sort_direction: "",
    sort_field: "",
}));

const {
    data: products,
    error,
    isLoading,
} = apiQuery("products").useGet(params);
</script>
