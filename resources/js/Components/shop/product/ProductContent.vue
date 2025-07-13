<template>
    <main class="grid w-full grid-cols-2 gap-4 md:w-2/3 lg:w-3/4">
        <template v-for="(item, index) in data?.items">
            <Lens
                v-if="item.image_url"
                :hovering="hoverStates[index]"
                @mouseenter="setHovering(index, true)"
                @mouseleave="setHovering(index, false)"
            >
                <img
                    loading="lazy"
                    :src="item.image_url"
                    alt="piepjack.clothing"
                    @click="() => showMultiple(index)"
                    width="1667"
                    height="2500"
                    class="slide-in-bck-top"
                    fetchpriority="high"
                    sizes="(max-width: 699px) calc(100vw - 40px), (max-width: 999px) calc(100vw - 64px), min(560px, 441px - 96px)"
                />
            </Lens>
        </template>
        <vue-easy-lightbox
            :visible="visibleRef"
            :imgs="imgsRef"
            :index="indexRef"
            @hide="onHide"
        />
    </main>

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
                                        'common.product.shippingInfo.germany'
                                    ),
                                },
                                $t(
                                    'common.product.shippingInfo.germanyDelivery'
                                ),
                                '€3.95',
                                `${$t(
                                    'common.product.shippingInfo.freeShipping'
                                )} on orders over €100`,
                            ],
                            [
                                {
                                    image: 'https://mainfacts.com/media/images/coats_of_arms/at.png',
                                    alt: 'Austria Flag',
                                    text: $t(
                                        'common.product.shippingInfo.austria'
                                    ),
                                },
                                $t(
                                    'common.product.shippingInfo.austriaDelivery'
                                ),
                                '€4.95',
                                `${$t(
                                    'common.product.shippingInfo.freeShipping'
                                )} on orders over €100`,
                            ],
                            [
                                {
                                    image: 'https://mainfacts.com/media/images/coats_of_arms/ch.png',
                                    alt: 'Switzerland Flag',
                                    text: $t(
                                        'common.product.shippingInfo.switzerland'
                                    ),
                                },
                                $t(
                                    'common.product.shippingInfo.switzerlandDelivery'
                                ),
                                '€9.95',
                                '-',
                            ],
                        ]"
                        :returnsInfo="{
                            text: $t('common.product.shippingInfo.returnsInfo'),
                            link: {
                                url: '#',
                                text: $t(
                                    'common.product.shippingInfo.returnsPortal'
                                ),
                            },
                        }"
                    />
                </template>
            </Accordion>
        </div>
    </aside>
</template>

<script setup>
import { ref, computed } from "vue";
import Lens from "@ui/Lens.vue";
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

const {
    data: variants,
    error,
    isLoading,
} = apiQuery("category-variations").useGetById(categoryId);

const visibleRef = ref(false);
const indexRef = ref(0);
const imgsRef = ref([]);

const onShow = () => {
    visibleRef.value = true;
};
const showSingle = (src) => {
    imgsRef.value = src;
    // or
    // imgsRef.value  = {
    //   title: 'this is a placeholder',
    //   src: 'https://picsum.photos/350/150'
    // }
    onShow();
};
const showMultiple = (index) => {
    imgsRef.value = props.data?.items
        .filter((item) => item.image_url)
        .map((item) => ({ title: "Image", src: item.image_url }));
    indexRef.value = index;
    onShow();
};
const onHide = () => (visibleRef.value = false);
const hoverStates = ref({});

function setHovering(index, value) {
    hoverStates.value[index] = value;
}
</script>
