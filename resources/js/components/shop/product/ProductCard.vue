<template>
    <router-link
        v-if="product && !isComingSoon"
        :to="'/product/' + product.id + '/' + product.slug"
        class="block h-full group cursor-pointer"
        @mouseenter="mouseEnter"
        @mouseleave="mouseLeave"
    >
        <Card
            class="relative flex flex-col h-full overflow-hidden transition-all duration-300 border-transparent hover:border-border hover:shadow-md rounded-none bg-card"
        >
            <Badge
                v-if="isNewProduct"
                class="absolute z-10 transition-transform duration-300 top-3 left-3 group-hover:scale-105 rounded-none bg-primary text-primary-foreground"
                variant="default"
            >
                {{ $t("common.product.new") }}
            </Badge>

            <AspectRatio
                :ratio="3 / 4"
                class="w-full overflow-hidden bg-muted rounded-none"
            >
                <ProductImage
                    :src="product.image_url"
                    :alt="product.name"
                    customClass="object-cover w-full h-full transition-transform duration-500 group-hover:scale-105 rounded-none"
                />
            </AspectRatio>

            <CardContent
                class="flex flex-col items-center justify-start grow p-3 sm:p-4 w-full border-t"
            >
                <div
                    class="flex items-center justify-center w-full h-10 sm:h-12 mb-1"
                >
                    <p
                        class="w-full text-sm font-semibold uppercase line-clamp-2 sm:text-base text-card-foreground text-center"
                        :title="product.name"
                    >
                        {{ product.name }}
                    </p>
                </div>

                <div
                    class="flex items-center justify-center w-full h-5 sm:h-6 mb-1"
                >
                    <div
                        v-if="product.reviews_count > 0"
                        class="flex items-center gap-1"
                    >
                        <StarRating
                            :rating="Math.round(product.average_rating)"
                            size="14"
                        />
                        <span
                            class="text-[10px] sm:text-xs text-muted-foreground"
                        >
                            ({{ product.reviews_count }})
                        </span>
                    </div>
                </div>

                <div
                    class="flex items-center justify-center w-full h-5 sm:h-6 mt-auto"
                >
                    <p
                        v-if="product.price"
                        class="text-xs uppercase sm:text-sm text-muted-foreground font-mustica"
                    >
                        {{ $t("common.product.ab") }}
                        {{ product.formatted_price }}
                    </p>
                </div>
            </CardContent>
        </Card>
    </router-link>

    <div v-else-if="isComingSoon" class="block h-full opacity-60">
        <Card
            class="relative flex flex-col h-full overflow-hidden transition-all duration-300 border-transparent rounded-none bg-card"
        >
            <Badge
                class="absolute z-10 top-3 left-3 rounded-none bg-muted text-muted-foreground"
                variant="outline"
            >
                {{ $t("common.product.comingSoon") }}
            </Badge>

            <AspectRatio
                :ratio="3 / 4"
                class="w-full overflow-hidden bg-muted/50 rounded-none flex items-center justify-center"
            >
                <div class="w-full h-full bg-muted/20"></div>
            </AspectRatio>

            <CardContent
                class="flex flex-col items-center justify-start grow p-3 sm:p-4 w-full border-t"
            >
                <div
                    class="flex items-center justify-center w-full h-10 sm:h-12 mb-1"
                >
                    <div class="w-3/4 h-4 bg-muted rounded-sm"></div>
                </div>

                <div
                    class="flex items-center justify-center w-full h-5 sm:h-6 mb-1"
                >
                    <div class="w-1/2 h-3 bg-muted rounded-sm"></div>
                </div>

                <div
                    class="flex items-center justify-center w-full h-5 sm:h-6 mt-auto"
                >
                    <div class="w-1/3 h-4 bg-muted rounded-sm"></div>
                </div>
            </CardContent>
        </Card>
    </div>
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
    isComingSoon: {
        type: Boolean,
        default: false,
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
