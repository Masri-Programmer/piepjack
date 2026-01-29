<template>
    <div class="flex flex-col md:flex-row w-full gap-8">
        <div
            class="grid w-full gap-4 md:w-2/3 lg:w-3/4 grid-cols-1 md:grid-cols-[3fr_1fr]"
        >
            <div
                class="relative w-full overflow-hidden rounded-lg shadow-xl aspect-square bg-gray-100"
                @click="showMultiple(activeIndex)"
            >
                <img
                    :src="imageUrls[activeIndex]"
                    alt="Product large view"
                    class="w-full h-full object-cover transition-transform duration-300 cursor-pointer"
                />
                <div
                    class="absolute bottom-4 right-4 text-gray-700 cursor-pointer"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                        class="w-8 h-8"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M10.5 3.75a6.75 6.75 0 1 0 0 13.5 6.75 6.75 0 0 0 0-13.5ZM2.25 10.5a8.25 8.25 0 1 1 14.59 5.28l4.695 4.695a1.5 1.5 0 1 1-2.121 2.121l-4.695-4.695A8.25 8.25 0 0 1 2.25 10.5Z"
                            clip-rule="evenodd"
                        />
                    </svg>
                </div>
            </div>

            <div
                class="flex flex-row md:flex-col gap-4 overflow-x-auto md:overflow-y-auto w-full md:w-auto"
            >
                <div
                    v-for="(image, index) in imageUrls"
                    :key="index"
                    @click="selectImage(index)"
                    class="flex-shrink-0 cursor-pointer p-1 rounded-lg transition-all duration-200"
                    :class="{
                        'border-2 border-black shadow-lg':
                            index === activeIndex,
                    }"
                >
                    <img
                        :src="image"
                        alt="Product thumbnail"
                        class="w-24 h-24 sm:w-28 sm:h-28 object-cover rounded"
                    />
                </div>
            </div>
        </div>

        <aside class="w-full md:w-1/3 lg:w-1/4">
            <div class="sticky flex flex-col gap-6 top-12">
                <h6 class="text-base font-extrabold">
                    {{ data?.name }}
                </h6>
                <template v-if="variants?.data && data?.items.length">
                    <ProductVariations
                        :variants="variants?.data"
                        :items="data?.items"
                        :product="data"
                    />
                    <ProductContentAddOns />
                </template>
                <div v-else class="text-sm text-orange-300 underline uppercase">
                    {{ $t("common.product.productOutofStock") }}
                </div>
                <Accordion
                    id="description"
                    :label="$t('common.product.desc')"
                    :content="data?.description"
                    iconName="Info"
                />
                <Accordion
                    id="shipping_info"
                    :label="$t('common.product.shipping')"
                    iconName="Package"
                >
                    <template #content>
                        <Table
                            :headers="[
                                $t('common.product.shippingInfo.country'),
                                $t('common.product.shippingInfo.deliveryTime'),
                                $t('common.product.shippingInfo.shippingCost'),
                                $t('common.product.shippingInfo.freeShipping'),
                            ]"
                            :rows="[
                                [
                                    {
                                        image: 'https://mainfacts.com/media/images/coats_of_arms/de.png',
                                        alt: 'Germany Flag',
                                        text: $t(
                                            'common.product.shippingInfo.germany',
                                        ),
                                    },
                                    $t(
                                        'common.product.shippingInfo.germanyDelivery',
                                    ),
                                    '€3.95',
                                    `${$t(
                                        'common.product.shippingInfo.freeShipping',
                                    )} on orders over €70`,
                                ],
                                [
                                    {
                                        image: 'https://mainfacts.com/media/images/coats_of_arms/at.png',
                                        alt: 'Austria Flag',
                                        text: $t(
                                            'common.product.shippingInfo.austria',
                                        ),
                                    },
                                    $t(
                                        'common.product.shippingInfo.austriaDelivery',
                                    ),
                                    '€4.95',
                                    `${$t(
                                        'common.product.shippingInfo.freeShipping',
                                    )} on orders over €70`,
                                ],
                                [
                                    {
                                        image: 'https://mainfacts.com/media/images/coats_of_arms/ch.png',
                                        alt: 'Switzerland Flag',
                                        text: $t(
                                            'common.product.shippingInfo.switzerland',
                                        ),
                                    },
                                    $t(
                                        'common.product.shippingInfo.switzerlandDelivery',
                                    ),
                                    '€9.95',
                                    '-',
                                ],
                            ]"
                            :returnsInfo="{
                                text: $t(
                                    'common.product.shippingInfo.returnsInfo',
                                ),
                                link: {
                                    url: '#',
                                    text: $t(
                                        'common.product.shippingInfo.returnsPortal',
                                    ),
                                },
                            }"
                        />
                    </template>
                </Accordion>
            </div>
        </aside>
    </div>

    <vue-easy-lightbox
        :visible="visibleRef"
        :imgs="imgsRef"
        :index="indexRef"
        @hide="onHide"
    />
</template>

<script setup>
import { ref, computed } from "vue";
import Accordion from "@ui/Accordion.vue";
import ProductReview from "./ProductReview.vue";
import { apiQuery } from "@lib/helpers";
import VueEasyLightbox from "vue-easy-lightbox";
import ProductVariations from "./ProductVariations.vue";
import ProductContentAddOns from "./ProductContentAddOns.vue";
import Table from "@ui/Table.vue";
const props = defineProps({
    data: { type: Object, required: true },
});
const categoryId = computed(() => props.data?.category_id);
const imageUrls = computed(
    () =>
        props.data?.items
            ?.filter((item) => item.image_url)
            .map((item) => item.image_url) || [],
);

const {
    data: variants,
    error,
    isLoading,
} = apiQuery("category-variations").useGetById(categoryId);

const visibleRef = ref(false);
const indexRef = ref(0);
const imgsRef = ref([]);
const activeIndex = ref(0);

const selectImage = (index) => {
    activeIndex.value = index;
};

const onShow = () => {
    visibleRef.value = true;
};
const showMultiple = (index) => {
    imgsRef.value = imageUrls.value;
    indexRef.value = index;
    onShow();
};
const onHide = () => (visibleRef.value = false);
</script>
