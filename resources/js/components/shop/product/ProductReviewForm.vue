<template>
    <div class="w-full selection:bg-main selection:text-accent">
        <header
            v-if="!createSuccess"
            class="mb-8 border-b-[6px] border-border pb-4"
        >
            <h2
                class="text-3xl sm:text-4xl font-bold uppercase tracking-tighter italic text-foreground leading-none"
            >
                Write a Review
            </h2>
        </header>

        <form
            v-if="!createSuccess"
            @submit.prevent="submitReview"
            class="space-y-8 animate-in fade-in duration-500"
        >
            <div class="space-y-3">
                <label
                    class="block text-xs font-bold uppercase tracking-widest text-muted-foreground"
                >
                    {{ $t("common.product.reviews") }}
                </label>
                <div class="flex items-center gap-2">
                    <template v-for="star in 5" :key="star">
                        <svg
                            @click="reviewForm.rating = star"
                            @mouseover="hoverRating = star"
                            @mouseleave="hoverRating = 0"
                            xmlns="http://www.w3.org/2000/svg"
                            width="36"
                            height="36"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            class="cursor-pointer transition-colors duration-150 border-2 p-1 rounded-none"
                            :class="[
                                star <= (hoverRating || reviewForm.rating)
                                    ? 'text-accent bg-main border-main'
                                    : 'text-muted-foreground bg-accent_light border-border hover:border-main',
                            ]"
                        >
                            <path
                                d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"
                            />
                        </svg>
                    </template>
                </div>
                <p
                    v-if="errors.rating"
                    class="text-destructive text-xs font-bold uppercase tracking-wide animate-in slide-in-from-top-1"
                >
                    {{ errors.rating }}
                </p>
            </div>

            <div class="space-y-2">
                <label
                    for="email"
                    class="block text-xs font-bold uppercase tracking-widest text-muted-foreground"
                >
                    {{ $t("common.forms.emailLabel") }}
                </label>
                <input
                    v-model="reviewForm.email"
                    type="email"
                    id="email"
                    :class="[
                        'w-full bg-accent_light border-2 p-4 text-base outline-none rounded-none transition-all',
                        errors.email
                            ? 'border-destructive focus:border-destructive'
                            : 'border-border focus:border-main',
                    ]"
                    :placeholder="$t('common.forms.emailPlaceholder')"
                />
                <p
                    v-if="errors.email"
                    class="text-destructive text-xs font-bold uppercase tracking-wide animate-in slide-in-from-top-1"
                >
                    {{ errors.email }}
                </p>
            </div>

            <div class="space-y-2">
                <label
                    for="title"
                    class="block text-xs font-bold uppercase tracking-widest text-muted-foreground"
                >
                    {{ $t("common.product.title") }}
                </label>
                <input
                    v-model="reviewForm.title"
                    type="text"
                    id="title"
                    :class="[
                        'w-full bg-accent_light border-2 p-4 text-base outline-none rounded-none transition-all',
                        errors.title
                            ? 'border-destructive focus:border-destructive'
                            : 'border-border focus:border-main',
                    ]"
                    :placeholder="$t('common.product.title')"
                />
                <p
                    v-if="errors.title"
                    class="text-destructive text-xs font-bold uppercase tracking-wide animate-in slide-in-from-top-1"
                >
                    {{ errors.title }}
                </p>
            </div>

            <div class="space-y-2">
                <label
                    for="comment"
                    class="block text-xs font-bold uppercase tracking-widest text-muted-foreground"
                >
                    {{ $t("common.product.comment") }}
                </label>
                <textarea
                    v-model="reviewForm.comment"
                    id="comment"
                    rows="5"
                    :class="[
                        'w-full bg-accent_light border-2 p-4 text-base outline-none rounded-none transition-all resize-y min-h-[120px]',
                        errors.comment
                            ? 'border-destructive focus:border-destructive'
                            : 'border-border focus:border-main',
                    ]"
                    :placeholder="$t('common.product.comment')"
                ></textarea>
                <p
                    v-if="errors.comment"
                    class="text-destructive text-xs font-bold uppercase tracking-wide animate-in slide-in-from-top-1"
                >
                    {{ errors.comment }}
                </p>
            </div>

            <Button
                type="submit"
                :disabled="createLoading"
                class="view-all w-full h-16 text-lg font-bold uppercase tracking-widest flex justify-center items-center rounded-none"
            >
                <Spinner v-if="createLoading" size="xs" />
                <span v-else>{{ $t("common.product.submit_review") }}</span>
            </Button>
        </form>

        <div
            v-else
            class="border-[6px] border-main bg-background p-10 sm:p-16 text-center animate-in zoom-in-95 duration-500"
        >
            <div
                class="inline-flex items-center justify-center w-16 h-16 bg-main text-accent mb-6 rounded-none"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="32"
                    height="32"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
            </div>
            <h3
                class="text-2xl sm:text-3xl font-bold uppercase tracking-tighter italic text-foreground mb-4"
            >
                {{ $t("validation.success.title") }}
            </h3>
            <p
                class="text-sm font-bold uppercase tracking-widest text-muted-foreground leading-relaxed"
            >
                {{
                    $t("common.product.review_submitted_message") ||
                    "Thank you for your review! It is pending approval."
                }}
            </p>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref, watch, onMounted } from "vue";
import { useI18n } from "vue-i18n";
import * as Yup from "yup";

import { apiQuery } from "@lib/helpers";
import Spinner from "@components/ui/Spinner.vue";
import { Button } from "@/components/ui/button";

const props = defineProps({
    productId: {
        type: [String, Number],
        required: true,
    },
});

const { t } = useI18n();
const {
    mutate: createReview,
    isLoading: createLoading,
    isSuccess: createSuccess,
    error: createApiError,
} = apiQuery("products-reviews").useStore();

const reviewForm = reactive({
    email: "",
    rating: 0,
    title: "",
    comment: "",
    product_id: props.productId,
});

const errors = reactive({
    email: null,
    rating: null,
    title: null,
    comment: null,
});

const hoverRating = ref(0);

const validationSchema = Yup.object().shape({
    email: Yup.string()
        .email(t("validation.validation.email.invalid"))
        .required(t("validation.validation.email.required")),
    rating: Yup.number()
        .min(1, t("validation.validation.rating"))
        .required(t("validation.validation.rating")),
    title: Yup.string(),
    comment: Yup.string().required(t("validation.validation.comment")),
});

const submitReview = async () => {
    resetErrors();
    try {
        await validationSchema.validate(reviewForm, { abortEarly: false });
        createReview(reviewForm, {
            onSuccess: () => {
                setTimeout(() => {
                    resetForm();
                }, 5000);
            },
        });
    } catch (validationErrors) {
        validationErrors.inner.forEach((error) => {
            errors[error.path] = error.message;
        });
    }
};

const resetErrors = () => {
    for (const key in errors) {
        errors[key] = null;
    }
};

const resetForm = () => {
    reviewForm.email = "";
    reviewForm.rating = 0;
    reviewForm.title = "";
    reviewForm.comment = "";
    resetErrors();
};

const clearErrorOnInputChange = () => {
    Object.keys(reviewForm).forEach((key) => {
        watch(
            () => reviewForm[key],
            () => {
                if (errors[key]) {
                    errors[key] = null;
                }
            },
        );
    });
};

onMounted(() => {
    clearErrorOnInputChange();
});
</script>
