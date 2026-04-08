<template>
    <router-link
        v-if="product"
        :to="'/product/' + product.id + '/' + product.slug"
        class="block h-full group cursor-pointer"
        @mouseenter="mouseEnter"
        @mouseleave="mouseLeave"
    >
        <Card
            class="relative flex flex-col overflow-hidden transition-all duration-300 border-transparent hover:border-border hover:shadow-md"
        >
            <Badge
                v-if="isNewProduct"
                class="absolute z-10 transition-transform duration-300 top-3 left-3 group-hover:scale-105"
                variant="default"
            >
                {{ $t("common.product.new") }}
            </Badge>

            <AspectRatio :ratio="3 / 4" class="overflow-hidden bg-muted">
                <ProductImage
                    :src="product.image_url"
                    :alt="product.name"
                    customClass="object-cover w-full h-full transition-transform duration-500 group-hover:scale-105"
                />
            </AspectRatio>

            <CardContent
                class="flex flex-col items-center justify-center grow p-3 mt-2 text-center sm:p-4"
            >
                <p
                    class="w-full text-sm font-semibold uppercase truncate sm:text-base text-card-foreground"
                >
                    {{ product.name }}
                </p>

                <div v-if="product.reviews_count > 0" class="flex items-center mt-1 gap-1">
                    <StarRating :rating="Math.round(product.average_rating)" size="14" />
                    <span class="text-[10px] text-muted-foreground">({{ product.reviews_count }})</span>
                </div>

                <p
                    v-if="product.price"
                    class="mt-1 text-xs uppercase sm:text-sm text-muted-foreground"
                >
                    {{ $t("common.product.ab") }} {{ product.formatted_price }}
                </p>
            </CardContent>
        </Card>
    </router-link>
</template>

<script setup>
import { ref, computed } from "vue";
import { useNow } from "@vueuse/core";
import ProductImage from "@ui/ProductImage.vue";
import StarRating from "./StarRating.vue";
import { Card, CardContent } from "@ui/card";
import { Badge } from "@ui/badge";
import { AspectRatio } from "@ui/aspect-ratio";

const props = defineProps({
    product: {
        type: Object,
        default: () => ({
            id: 1,
            name: "Title",
            slug: "Slug",
            description: "Qui ut doloribus eos quisquam fuga sit.",
            image_url: "",
            image_mime: "image/jpeg",
            image_size: 1279,
            price: "89.91",
            formatted_price: "89.91 €",
            created_at: new Date().toISOString(),
            average_rating: 0,
            reviews_count: 0,
        }),
    },
});

const imgHover = ref(false);
const mouseEnter = () => (imgHover.value = true);
const mouseLeave = () => (imgHover.value = false);

const now = useNow();

const isNewProduct = computed(() => {
    if (!props.product?.created_at) return false;
    const createdAt = new Date(props.product.created_at);
    const daysDifference = (now.value - createdAt) / (1000 * 60 * 60 * 24);
    return daysDifference <= 7;
});
</script>
