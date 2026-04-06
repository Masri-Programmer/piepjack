<template>
    <div
        class="mx-auto px-4 md:px-8 py-10 lg:py-16 selection:bg-accent_dark selection:text-[#f2f0a1]"
    >
        <div class="flex flex-col lg:flex-row w-full gap-12 lg:gap-16">
            <div
                class="w-full lg:w-2/3 flex flex-col-reverse md:flex-row gap-4 lg:gap-6"
            >
                <div
                    class="flex flex-row md:flex-col gap-3 overflow-x-auto md:overflow-y-auto w-full md:w-28 shrink-0 pb-2 md:pb-0 scrollbar-hide"
                >
                    <div
                        v-for="(image, index) in imageUrls"
                        :key="index"
                        @click="selectImage(index)"
                        class="shrink-0 cursor-pointer p-0.5 transition-all duration-200 border-2 rounded-none"
                        :class="
                            index === activeIndex
                                ? 'border-accent_dark'
                                : 'border-transparent hover:border-gray-300'
                        "
                    >
                        <ProductImage
                            :src="image"
                            :alt="data?.name"
                            customClass="w-20 h-20 sm:w-24 sm:h-24 md:w-full md:h-28 object-cover rounded-none grayscale-[20%] hover:grayscale-0 transition-all"
                        />
                    </div>
                </div>

                <div
                    class="relative w-full overflow-hidden border-[3px] border-accent_dark bg-accent aspect-4/5 sm:aspect-square md:aspect-[4/5] cursor-pointer group"
                    @click="showMultiple(activeIndex)"
                >
                    <ProductImage
                        :src="imageUrls[activeIndex]"
                        :alt="data?.name"
                        customClass="w-full h-full object-cover rounded-none transition-transform duration-500 group-hover:scale-105"
                    />
                    <div
                        class="absolute bottom-4 right-4 bg-background border-2 border-accent_dark p-2 text-main transition-colors group-hover:bg-accent_dark group-hover:text-primary-foreground"
                    >
                        <ZoomIn class="w-6 h-6" stroke-width="2.5" />
                    </div>
                </div>
            </div>

            <aside class="w-full lg:w-1/3">
                <div class="sticky top-24 flex flex-col gap-8">
                    <div
                        class="space-y-2 border-b-[6px] border-accent_dark pb-6"
                    >
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
                                    <Table
                                        class="rounded-none bg-accent_dark text-main"
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
                                                >
                                                    €3.95
                                                </TableCell>
                                            </TableRow>
                                            <TableRow
                                                class="border-b-2 border-accent_dark hover:bg-accent_light transition-colors"
                                            >
                                                <TableCell
                                                    class="border-r-2 border-accent_dark font-bold uppercase text-xs flex items-center gap-2"
                                                >
                                                    <img
                                                        src="https://mainfacts.com/media/images/coats_of_arms/at.png"
                                                        alt="Austria"
                                                        class="w-4 h-4 object-contain rounded-none"
                                                    />
                                                    {{
                                                        $t(
                                                            "common.product.shippingInfo.austria",
                                                        )
                                                    }}
                                                </TableCell>
                                                <TableCell
                                                    class="border-r-2 border-accent_dark text-xs font-medium text-center"
                                                >
                                                    {{
                                                        $t(
                                                            "common.product.shippingInfo.austriaDelivery",
                                                        )
                                                    }}
                                                </TableCell>
                                                <TableCell
                                                    class="text-xs font-accent_dark text-right"
                                                >
                                                    €4.95
                                                </TableCell>
                                            </TableRow>
                                            <TableRow
                                                class="hover:bg-accent_light transition-colors"
                                            >
                                                <TableCell
                                                    class="border-r-2 border-accent_dark font-bold uppercase text-xs flex items-center gap-2"
                                                >
                                                    <img
                                                        src="https://mainfacts.com/media/images/coats_of_arms/ch.png"
                                                        alt="Switzerland"
                                                        class="w-4 h-4 object-contain rounded-none"
                                                    />
                                                    {{
                                                        $t(
                                                            "common.product.shippingInfo.switzerland",
                                                        )
                                                    }}
                                                </TableCell>
                                                <TableCell
                                                    class="border-r-2 border-accent_dark text-xs font-medium text-center"
                                                >
                                                    {{
                                                        $t(
                                                            "common.product.shippingInfo.switzerlandDelivery",
                                                        )
                                                    }}
                                                </TableCell>
                                                <TableCell
                                                    class="text-xs font-accent_dark text-right"
                                                >
                                                    €9.95
                                                </TableCell>
                                            </TableRow>
                                        </TableBody>
                                    </Table>

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
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { apiQuery } from "@lib/helpers";
import VueEasyLightbox from "vue-easy-lightbox";
import { ZoomIn, Info, Package } from "lucide-vue-next";

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
