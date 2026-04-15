<template>
    <Card
        class="relative flex flex-row items-stretch border-border bg-card transition-colors hover:bg-accent-shadcn rounded-none group w-full overflow-hidden"
    >
        <div
            class="relative shrink-0 w-24 sm:w-28 bg-muted border-r border-border aspect-[3/4]"
        >
            <span
                v-if="item?.cartQuantity"
                class="absolute top-2 left-2 z-10 flex items-center justify-center w-6 h-6 text-xs font-bold text-primary-foreground bg-primary rounded-none shadow-sm"
            >
                {{ item.cartQuantity }}
            </span>

            <ProductImage
                :src="item?.image_url"
                :fallback="product?.image_url"
                :alt="product.name"
                customClass="absolute inset-0 object-cover w-full h-full rounded-none"
            />
        </div>

        <CardContent
            class="flex flex-col grow justify-between p-4 w-full min-w-0 !pb-4"
        >
            <div class="flex justify-between items-start gap-4 mb-1">
                <h6
                    class="text-sm sm:text-base font-bold uppercase tracking-tight text-card-foreground line-clamp-2 min-w-0 break-words"
                >
                    {{ product.name }}
                    <span
                        class="text-xs text-muted-foreground font-normal normal-case block sm:inline mt-1 sm:mt-0"
                    >
                        (#{{ product.id }})
                    </span>
                </h6>

                <p
                    v-if="item?.formatted_price"
                    class="text-sm sm:text-base font-bold text-foreground whitespace-nowrap shrink-0"
                >
                    {{ item.formatted_price }}
                </p>
            </div>

            <p
                v-if="item?.id"
                class="text-xs text-muted-foreground font-mono uppercase mb-4 truncate"
            >
                ID: {{ item.id }}
            </p>

            <div
                v-if="item?.options?.length"
                class="flex flex-wrap gap-x-6 gap-y-2 mb-4"
            >
                <div
                    v-for="(option, index) in item.options"
                    :key="index"
                    class="flex items-center gap-1 text-xs truncate"
                >
                    <span class="font-bold uppercase text-foreground"
                        >{{ option.name }}:</span
                    >
                    <span class="text-muted-foreground">{{
                        option.value
                    }}</span>
                </div>
            </div>

            <div class="mt-auto flex justify-start sm:justify-end w-full">
                <CustomNumberInput
                    v-if="options.addToCart"
                    :modelValue="item.cartQuantity"
                    :addToCart="options.addToCart"
                    :max="item.quantity"
                    :product="product"
                    :item="item"
                />
            </div>
        </CardContent>
    </Card>
</template>

<script setup>
import { Card, CardContent } from "@/components/ui/card";
import ProductImage from "@components/ui/ProductImage.vue";
import CustomNumberInput from "@ui/CustomNumberInput.vue";

const props = defineProps({
    product: { type: Object, required: true },
    item: { type: Object, required: true },
    options: { type: Object, required: false, default: () => ({}) },
});
</script>
