<template>
    <div class="flex flex-col items-center w-full gap-8">
        <div
            v-if="error"
            class="w-full p-4 text-center border bg-destructive/5 text-destructive border-destructive/10"
        >
            <p class="font-medium">{{ $t("validation.error.getProducts") }}</p>
        </div>

        <div
            v-else-if="isLoading"
            class="grid w-full grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:gap-6"
        >
            <div
                v-for="i in params.per_page"
                :key="i"
                class="flex flex-col space-y-4"
            >
                <Skeleton class="w-full aspect-3/4" />
                <div class="space-y-2">
                    <Skeleton class="w-[80%] h-4 mx-auto" />
                    <Skeleton class="w-[50%] h-4 mx-auto" />
                </div>
            </div>
        </div>

        <div
            v-else
            class="grid w-full grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:gap-6"
        >
            <div
                v-for="(product, index) in products?.data || []"
                :key="product.id"
                data-aos="fade-up"
                :data-aos-delay="100 * index"
            >
                <ProductCard :product="product" />
            </div>
        </div>

        <div class="mt-4" data-aos="fade-up" :data-aos-delay="300">
            <Button
                as-child
                class="view-all"
            >
                <router-link to="/collections">
                    {{ $t("components.buttons.viewAll") }}
                </router-link>
            </Button>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import ProductCard from "../product/ProductCard.vue";
import { apiQuery } from "@lib/helpers";

// shadcn-vue primitives (adjust path based on your `@/components/ui` config)
import { Button } from "@/components/ui/button";
import { Skeleton } from "@/components/ui/skeleton";

const params = ref({
    active: null,
    category_id: null,
    page: 1,
    per_page: 8,
    search: "",
    sort_direction: "",
    sort_field: "",
});

const {
    data: products,
    error,
    isLoading,
} = apiQuery("products").useGet(params);
</script>
