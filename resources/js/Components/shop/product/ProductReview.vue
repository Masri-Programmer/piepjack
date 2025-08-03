<template>
    <div class="py-4">
        <h2 class="text-2xl font-bold mb-4">
            {{ $t("common.product.reviews") }}
        </h2>

        <div v-if="reviewsIsLoading" class="text-center py-8 text-gray-500">
            <p>{{ $t("validation.messages.loading") }}</p>
        </div>

        <div v-else-if="reviewsErrors" class="text-center py-8 text-red-500">
            <p>{{ $t("validation.messages.error") }}</p>
        </div>

        <div v-else-if="reviews?.data?.length > 0">
            <div class="space-y-6">
                <article
                    v-for="review in reviews.data"
                    :key="review.id"
                    class="flex gap-4 pb-6 border-b border-gray-200 last:border-b-0"
                >
                    <div
                        class="flex-shrink-0 flex items-center justify-center w-12 h-12 bg-slate-200 rounded-full"
                    >
                        <span class="text-lg font-bold text-slate-600">
                            {{ getInitials(review.user) }}
                        </span>
                    </div>

                    <div class="flex-1">
                        <div class="flex justify-between items-center mb-1">
                            <h3 class="font-bold text-main">
                                {{ review.user.first_name }}
                                {{ review.user.last_name }}
                            </h3>
                            <time
                                :datetime="review.created_at"
                                class="text-sm text-gray-500"
                            >
                                {{ formatDate(review.created_at) }}
                            </time>
                        </div>

                        <StarRating :rating="review.rating" class="mb-2" />

                        <h4 class="mb-2 text-lg font-semibold">
                            {{ review.title }}
                        </h4>
                        <p class="leading-relaxed text-gray-600">
                            {{ review.comment }}
                        </p>
                    </div>
                </article>
            </div>

            <Pagination :links="reviews.links" class="mt-6" />
        </div>

        <div v-else class="text-center py-8 text-gray-500">
            <p>{{ $t("common.product.no_reviews") }}</p>
        </div>

        <ProductReviewForm :product-id="productId" class="mt-8" />
    </div>
</template>

<script setup>
import { computed } from "vue";
import { apiQuery } from "@lib/helpers";
import StarRating from "./StarRating.vue";
import Pagination from "./Pagination.vue";
import ProductReviewForm from "./ProductReviewForm.vue";

const props = defineProps({
    data: { type: Object, required: true },
});

const productId = computed(() => props.data?.id);

const {
    data: reviews,
    error: reviewsErrors,
    isLoading: reviewsIsLoading,
} = apiQuery("products-reviews").useGetById(productId);

const formatDate = (dateString) => {
    if (!dateString) return "";
    return new Date(dateString).toLocaleDateString("en-US", {
        year: "numeric",
        month: "long",
        day: "numeric",
    });
};

const getInitials = (user) => {
    if (!user?.first_name || !user?.last_name) return "?";
    return `${user.first_name[0]}${user.last_name[0]}`.toUpperCase();
};
</script>