<template>
    <div class="grid grid-cols-1 lg:grid-cols-2 w-full gap-12 lg:gap-16">
        <div class="flex flex-col gap-4 lg:gap-6 min-w-0">
            <div class="relative w-full min-w-0">
                <Carousel
                    id="gallery"
                    v-bind="galleryConfig"
                    v-model="activeIndex"
                    class="product-carousel"
                >
                    <Slide v-for="(image, index) in imageUrls" :key="index">
                        <div
                            class="relative w-full overflow-hidden bg-accent aspect-4/5 sm:aspect-square md:aspect-[4/5] cursor-pointer group"
                            @click="showMultiple(index)"
                        >
                            <ProductImage
                                :src="image"
                                :alt="data?.name"
                                customClass="w-full h-full object-cover rounded-none transition-transform duration-500 group-hover:scale-105"
                            />
                            <div
                                class="absolute bottom-4 right-4 bg-background border-2 border-accent_dark p-2 text-main transition-colors group-hover:bg-accent_dark group-hover:text-primary-foreground"
                            >
                                <ZoomIn class="w-6 h-6" stroke-width="2.5" />
                            </div>
                        </div>
                    </Slide>
                </Carousel>
            </div>

            <div v-if="imageUrls.length > 1" class="relative w-full min-w-0">
                <Carousel
                    id="thumbnails"
                    v-bind="thumbnailsConfig"
                    v-model="activeIndex"
                    class="thumbnail-carousel"
                >
                    <Slide v-for="(image, index) in imageUrls" :key="index">
                        <template #default="{ currentIndex, isActive }">
                            <div
                                :class="[
                                    'thumbnail relative w-full aspect-square bg-accent cursor-pointer transition-all duration-300 border-2 rounded-none p-0.5',
                                    isActive
                                        ? 'border-accent_dark opacity-100'
                                        : 'border-transparent opacity-60 hover:opacity-100 hover:border-gray-300',
                                ]"
                                @click="slideTo(currentIndex)"
                            >
                                <ProductImage
                                    :src="image"
                                    :alt="`${data?.name} thumbnail`"
                                    customClass="w-full h-full object-cover lg:object-contain rounded-none"
                                />
                            </div>
                        </template>
                    </Slide>

                    <template #addons>
                        <Navigation />
                    </template>
                </Carousel>
            </div>
        </div>

        <aside class="min-w-0">
            <div class="sticky top-24 flex flex-col gap-8">
                <div class="space-y-2 border-b-[6px] border-accent_dark pb-6">
                    <h1
                        class="text-4xl md:text-5xl lg:text-6xl font-accent_dark uppercase tracking-tighter leading-none italic break-words"
                    >
                        {{ data?.name }}
                    </h1>
                </div>

                <div class="space-y-6">
                    <template v-if="data?.items && data?.items.length">
                        <ProductVariations
                            :items="data?.items"
                            :product="data"
                        />
                        <ProductContentAddOns />
                    </template>
                    <div
                        v-else
                        class="bg-red-600 text-primary-foreground border-4 border-accent_dark p-4 text-center"
                    >
                        <span
                            class="text-sm font-accent_dark uppercase tracking-widest"
                        >
                            {{ $t("common.product.productOutofStock") }}
                        </span>
                    </div>
                </div>

                <div class="mt-4 border-t-2 border-accent_dark">
                    <Accordion type="single" collapsible class="w-full">
                        <AccordionItem
                            value="description"
                            class="border-b-2 border-accent_dark"
                        >
                            <AccordionTrigger
                                class="text-sm font-accent_dark uppercase tracking-widest hover:no-underline hover:bg-accent_dark hover:text-[#f2f0a1] px-3 py-4 transition-all rounded-none group [&[data-state=open]]:bg-accent_dark [&[data-state=open]]:text-[#f2f0a1]"
                            >
                                <span class="flex items-center gap-3">
                                    <Info
                                        class="w-5 h-5 group-hover:scale-110 transition-transform"
                                    />
                                    {{ $t("common.product.desc") }}
                                </span>
                            </AccordionTrigger>
                            <AccordionContent
                                class="text-base text-gray-800 font-medium leading-relaxed px-4 py-6 bg-accent_dark text-main border-t-2 border-accent_dark"
                            >
                                <div v-html="data?.description"></div>
                            </AccordionContent>
                        </AccordionItem>

                        <AccordionItem
                            value="shipping"
                            class="border-b-2 border-accent_dark"
                        >
                            <AccordionTrigger
                                class="text-sm font-accent_dark uppercase tracking-widest hover:no-underline hover:bg-accent_dark hover:text-[#f2f0a1] px-3 py-4 transition-all rounded-none group [&[data-state=open]]:bg-accent_dark [&[data-state=open]]:text-[#f2f0a1]"
                            >
                                <span class="flex items-center gap-3">
                                    <Package
                                        class="w-5 h-5 group-hover:scale-110 transition-transform"
                                    />
                                    {{ $t("common.product.shipping") }}
                                </span>
                            </AccordionTrigger>
                            <AccordionContent
                                class="p-0 border-t-2 border-accent_dark"
                            >
                                <div class="w-full overflow-x-auto">
                                    <Table
                                        class="rounded-none bg-accent_dark text-main min-w-[350px]"
                                    >
                                        <TableHeader
                                            class="border-b-2 border-accent_dark"
                                        >
                                            <TableRow
                                                class="hover:bg-transparent"
                                            >
                                                <TableHead
                                                    class="text-[10px] font-accent_dark uppercase tracking-widest border-r-2 border-accent_dark py-3"
                                                >
                                                    {{
                                                        $t(
                                                            "common.product.shippingInfo.country",
                                                        )
                                                    }}
                                                </TableHead>
                                                <TableHead
                                                    class="text-[10px] font-accent_dark uppercase tracking-widest border-r-2 border-accent_dark py-3 text-center"
                                                >
                                                    {{
                                                        $t(
                                                            "common.product.shippingInfo.deliveryTime",
                                                        )
                                                    }}
                                                </TableHead>
                                                <TableHead
                                                    class="text-[10px] font-accent_dark uppercase tracking-widest py-3 text-right"
                                                >
                                                    {{
                                                        $t(
                                                            "common.product.shippingInfo.shippingCost",
                                                        )
                                                    }}
                                                </TableHead>
                                            </TableRow>
                                        </TableHeader>
                                        <TableBody>
                                            <TableRow
                                                class="border-b-2 border-accent_dark hover:bg-accent_light transition-colors"
                                            >
                                                <TableCell
                                                    class="border-r-2 border-accent_dark font-bold uppercase text-xs flex items-center gap-2"
                                                >
                                                    <img
                                                        src="https://mainfacts.com/media/images/coats_of_arms/de.png"
                                                        alt="Germany"
                                                        class="w-4 h-4 object-contain rounded-none"
                                                    />
                                                    {{
                                                        $t(
                                                            "common.product.shippingInfo.germany",
                                                        )
                                                    }}
                                                </TableCell>
                                                <TableCell
                                                    class="border-r-2 border-accent_dark text-xs font-medium text-center"
                                                >
                                                    {{
                                                        $t(
                                                            "common.product.shippingInfo.germanyDelivery",
                                                        )
                                                    }}
                                                </TableCell>
                                                <TableCell
                                                    class="text-xs font-accent_dark text-right"
                                                    >€3.95</TableCell
                                                >
                                            </TableRow>
                                        </TableBody>
                                    </Table>
                                </div>

                                <div
                                    class="bg-primary text-primary-foreground p-4 flex flex-col gap-2"
                                >
                                    <span class="text-xs font-bold">{{
                                        $t(
                                            "common.product.shippingInfo.returnsInfo",
                                        )
                                    }}</span>
                                    <a
                                        href="#"
                                        class="text-xs font-accent_dark uppercase text-[#f2f0a1] hover:underline underline-offset-4 w-fit"
                                    >
                                        →
                                        {{
                                            $t(
                                                "common.product.shippingInfo.returnsPortal",
                                            )
                                        }}
                                    </a>
                                </div>
                            </AccordionContent>
                        </AccordionItem>
                    </Accordion>
                </div>
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

<style scoped>
@reference "@assets/css/app.css";

/* Shared navigation styling for both carousels */
.product-carousel :deep(.carousel__prev),
.product-carousel :deep(.carousel__next),
.thumbnail-carousel :deep(.carousel__prev),
.thumbnail-carousel :deep(.carousel__next) {
    @apply bg-background border-2 border-accent_dark text-main hover:bg-accent_dark hover:text-primary-foreground transition-colors mx-2;
    border-radius: 0;
}

/* Adjust size for thumbnail navigation to not obscure images */
.thumbnail-carousel :deep(.carousel__prev),
.thumbnail-carousel :deep(.carousel__next) {
    transform: scale(0.8) translateY(-50%);
}

.product-carousel :deep(.carousel__slide),
.thumbnail-carousel :deep(.carousel__slide) {
    @apply p-0;
}

.product-carousel :deep(.carousel__viewport),
.thumbnail-carousel :deep(.carousel__viewport) {
    @apply rounded-none;
}
</style>

<script setup>
import { ref, computed } from "vue";
import { apiQuery } from "@lib/helpers";
import VueEasyLightbox from "vue-easy-lightbox";
import { ZoomIn, Info, Package } from "lucide-vue-next";

// Carousel
import { Carousel, Slide, Navigation } from "vue3-carousel";
import "vue3-carousel/dist/carousel.css";

// Shadcn UI
import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from "@/components/ui/accordion";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";

// Custom Components
import ProductVariations from "./ProductVariations.vue";
import ProductContentAddOns from "./ProductContentAddOns.vue";
import ProductImage from "@components/ui/ProductImage.vue";

const props = defineProps({
    data: { type: Object, required: true },
});

const categoryId = computed(() => props.data?.category_id);

const imageUrls = computed(() => {
    if (props.data?.images && props.data.images.length > 0) {
        return props.data.images;
    }
    return props.data?.image_url ? [props.data.image_url] : [];
});

// Carousel Configs & State
const activeIndex = ref(0);

const slideTo = (nextSlide) => {
    activeIndex.value = nextSlide;
};

// Main gallery parameters
const galleryConfig = {
    itemsToShow: 1,
    wrapAround: true,
    slideEffect: "fade",
    mouseDrag: true,
    touchDrag: true,
};

// Responsive thumbnails config
const thumbnailsConfig = {
    itemsToShow: 4, // Mobile default (4 items visible)
    wrapAround: true,
    touchDrag: true,
    mouseDrag: true,
    gap: 12,
    breakpoints: {
        // Adjust values for larger screens
        640: {
            itemsToShow: 5,
        },
        1024: {
            itemsToShow: 6,
        },
    },
};

// Lightbox state
const visibleRef = ref(false);
const indexRef = ref(0);
const imgsRef = ref([]);

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

<style scoped>
@reference "@assets/css/app.css";

/* Hide default SVG icons */
:deep(.carousel__icon) {
    display: none;
}

/* Base Arrow Styling */
:deep(.carousel__prev),
:deep(.carousel__next) {
    opacity: 1;
    z-index: 10;
    height: 2rem;
    padding: 2rem;
    display: block;
    font-size: 2rem;
    border: 0 !important;
    background-color: transparent; /* Ensures no background overrides the pseudo-element */
    background-size: 1em;
    border-radius: 50%;
    background-repeat: no-repeat;
    background-position: center center;
}

/* Circular background and custom SVG styling via pseudo-elements */
:deep(.carousel__prev::before),
:deep(.carousel__next::before) {
    top: 50%;
    left: 50%;
    opacity: 0;
    content: "";
    width: 3rem;
    z-index: -1;
    height: 3rem;
    position: absolute;
    border-radius: 50%;
    background-color: white;
    transform: translate(-50%, -50%);
    background-position: center;
    background-repeat: no-repeat;
    transition:
        transform 0.5s ease-in-out,
        opacity 0.3s ease-in-out;
    box-shadow: 2px 4px 15px 13px rgba(0, 0, 0, 0.17);
    -webkit-box-shadow: 2px 4px 15px 13px rgba(0, 0, 0, 0.17);
}

/* Base64 Custom SVG Backgrounds */
:deep(.carousel__prev::before) {
    background-image: url(data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9ImN1cnJlbnRDb2xvciIgc3Ryb2tlLXdpZHRoPSIxIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiIGNsYXNzPSJsdWNpZGUgbHVjaWRlLWNoZXZyb24tbGVmdCI+PHBhdGggZD0ibTE1IDE4LTYtNiA2LTYiLz48L3N2Zz4=);
}

:deep(.carousel__next::before) {
    background-image: url(data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9ImN1cnJlbnRDb2xvciIgc3Ryb2tlLXdpZHRoPSIxIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiIGNsYXNzPSJsdWNpZGUgbHVjaWRlLWNoZXZyb24tcmlnaHQiPjxwYXRoIGQ9Im05IDE4IDYtNi02LTYiLz48L3N2Zz4=);
}

:deep(.carousel__prev::after),
:deep(.carousel__next::after) {
    opacity: 1;
}

/* Hover States for the Carousel Container */
:deep(.carousel:hover .carousel__prev::before),
:deep(.carousel:hover .carousel__next::before),
:deep(.carousel:hover .carousel__prev),
:deep(.carousel:hover .carousel__next) {
    opacity: 1;
}

/* Arrow Hover Animations */
:deep(.carousel__next:hover::before) {
    animation: move-bg 0.4s ease-in-out 1;
    background-position: center;
}

:deep(.carousel__prev:hover::before) {
    animation: move-bg-left 0.4s ease-in-out 1;
    background-position: center;
}

/* Keyframes */
@keyframes move-bg {
    0% {
        background-position: 0 center;
    }
    50% {
        background-position: 200% center;
    }
    51% {
        background-position: -200% center;
    }
    100% {
        background-position: 50% center;
    }
}

@keyframes move-bg-left {
    0% {
        background-position: 0 center;
    }
    50% {
        background-position: -200% center;
    }
    51% {
        background-position: 200% center;
    }
    100% {
        background-position: 50% center;
    }
}

/* General Carousel Adjustments */
:deep(.carousel__slide) {
    align-items: start;
    padding: 0; /* Resetting padding to keep image layout intact */
}

:deep(.carousel__viewport) {
    @apply rounded-none;
}

:deep(.carousel__next--disabled),
:deep(.carousel__prev--disabled) {
    display: none;
}

/* Mobile Responsiveness */
@media (max-width: 768px) {
    :deep(.carousel__prev),
    :deep(.carousel__next) {
        padding: 0.5rem;
    }
    :deep(.carousel__prev::before),
    :deep(.carousel__next::before) {
        top: 0%;
    }

    /* Shrink the thumbnail arrows slightly on mobile to prevent blocking images */
    .thumbnail-carousel :deep(.carousel__prev),
    .thumbnail-carousel :deep(.carousel__next) {
        transform: scale(0.7);
    }
}
/* Locate this section in your styles and update it */
.product-carousel :deep(.carousel__prev),
.product-carousel :deep(.carousel__next),
.thumbnail-carousel :deep(.carousel__prev),
.thumbnail-carousel :deep(.carousel__next) {
    /* I removed 'bg-background' and 'hover:bg-accent_dark' from the @apply list */
    @apply border-2 border-accent_dark text-main hover:text-primary-foreground transition-colors mx-2;

    /* Explicitly set the background to transparent so nothing shows behind the arrow */
    background-color: transparent !important;
    border-radius: 0;
}
</style>
