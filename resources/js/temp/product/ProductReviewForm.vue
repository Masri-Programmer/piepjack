<template>
        <!-- Review Form -->
        <form
            v-if="!createSuccess"
            @submit.prevent="submitReview"
            class="contact-form"
        >
            <!-- Email Section -->
            <div class="contact-form-section">
                <label for="email" class="contact-form-label">{{
        $t("common.forms.emailLabel")
      }}</label>
                <input
                    v-model="reviewForm.email"
                    type="email"
                    id="email"
                    :class="[
                        'contact-form-input',
                        errors.email ? 'contact-form-error' : '',
                    ]"
                    :placeholder="$t('common.forms.emailPlaceholder')"
                />
                <transition name="fade">
                    <p v-if="errors.email" class="contact-form-error-message">
                        {{ errors.email }}
                    </p>
                </transition>
            </div>

            <!-- Rating Section -->
            <div class="contact-form-section">
                <label class="contact-form-label">{{
                    $t("common.product.reviews")
                }}</label>
                <div class="flex items-center gap-1">
                    <template v-for="star in 5" :key="star">
                        <svg
                            @click="reviewForm.rating = star"
                            @mouseover="hoverRating = star"
                            @mouseleave="hoverRating = 0"
                            xmlns="http://www.w3.org/2000/svg"
                            width="28"
                            height="28"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            class="cursor-pointer text-gray-300"
                            :class="{
                                '!text-yellow-400': star <= (hoverRating || reviewForm.rating)
                            }"
                        >
                            <path
                                d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"
                            />
                        </svg>
                    </template>
                </div>
                <transition name="fade">
                    <p v-if="errors.rating" class="contact-form-error-message">
                        {{ errors.rating }}
                    </p>
                </transition>
            </div>

            <!-- Title Section -->
            <div class="contact-form-section">
                <!-- <label for="title" class="contact-form-label">{{
                    $t("common.product.title")
                }}</label> -->
                <input
                    v-model="reviewForm.title"
                    type="text"
                    id="title"
                    :class="[
                        'contact-form-input',
                        errors.title ? 'contact-form-error' : '',
                    ]"
                    :placeholder="$t('common.product.title')"
                />
                <transition name="fade">
                    <p v-if="errors.title" class="contact-form-error-message">
                        {{ errors.title }}
                    </p>
                </transition>
            </div>

            <!-- Comment Section -->
            <div class="contact-form-section">
                <label for="comment" class="contact-form-label">{{
                    $t("common.product.comment")
                }}</label>
                <textarea
                    v-model="reviewForm.comment"
                    id="comment"
                    rows="4"
                    :class="[
                        'contact-form-input',
                        errors.comment ? 'contact-form-error' : '',
                    ]"
                    :placeholder="$t('common.product.comment')"
                ></textarea>
                <transition name="fade">
                    <p v-if="errors.comment" class="contact-form-error-message">
                        {{ errors.comment }}
                    </p>
                </transition>
            </div>

            <div class="contact-form-footer">
                <button
                    type="submit"
                    class="view-all"
                    :disabled="createLoading"
                >
                    <div v-if="createLoading" class="contact-form-loading">
                        <Spinner class="h-5 w-5" />
                    </div>
                    <span v-else>{{ $t("common.product.submit_review") }}</span>
                </button>
            </div>
        </form>
</template>

<script setup>
import { reactive, ref, watch, onMounted } from "vue";
import { useI18n } from "vue-i18n";
import * as Yup from "yup";
import { apiQuery } from "@lib/helpers";
import Spinner from "@components/ui/Spinner.vue";
import "@assets/css/checkout/contactFrom.css";

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
    console.log(reviewForm);
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
            }
        );
    });
};

onMounted(() => {
    clearErrorOnInputChange();
});
</script>