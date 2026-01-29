<template>
    <p class="text-gray-300 text-xs">{{ selected?.price }} €</p>
    <div class="flex flex-row gap-4 flex-wrap">
        <ProductSlider
            :variants="variants"
            :selectedColor="selectedColor"
            :getAvailableColors="getAvailableColors"
            @handle:pick="(type, value) => handlePick(type, value)"
        />
    </div>

    <div class="flex flex-wrap gap-3">
        <div
            v-for="size in variants.find(
                (variant) => variant.name.toLowerCase() === 'size',
            )?.options"
            :key="size.id"
        >
            <button
                class="border border-gray-300 w-11 h-11 grid place-content-center relative text-xs break-all"
                @click="handlePick('size', size.value)"
                :class="{
                    'bg-main text-accent font-bold shadow-md border-gray border-3':
                        selectedSize === size.value,
                    'meh-diagonal text-gray border-gray':
                        !getAvailableSizes.includes(size.value),
                }"
            >
                {{ size.value }}
            </button>
        </div>
    </div>

    <button
        @click="addItemToCart"
        :disabled="
            !getAvailableSizes.includes(selectedSize) ||
            !getAvailableColors.includes(selectedColor)
        "
        class="view-all text-xs text-center"
    >
        {{ $t("common.product.addToCart") }}
    </button>
</template>

<script setup>
import { useI18n } from "vue-i18n";
import { useStorage } from "@vueuse/core";
import { ref, computed, watch } from "vue";
import { useToast } from "vue-toastification";
import ProductSlider from "./ProductSlider.vue";
import { cartState, addToCart } from "@lib/store/shop/index.js";

const toast = useToast();
const { t } = useI18n();

const props = defineProps({
    variants: { type: Array, required: true },
    items: { type: Object, required: true },
    product: { type: Object, required: true },
});
const selectedColor = useStorage(
    "selectedColor",
    props.items[0].options.find((o) => o.name.toLowerCase() === "color")
        ?.value || null,
);
const selectedSize = useStorage(
    "selectedSize",
    props.items[0].options.find((o) => o.name.toLowerCase() === "size")
        ?.value || null,
);

const selected = useStorage("selectedItem", props.items[0]);

const getAvailableSizes = computed(() => {
    return Array.from(
        new Set(
            props.items
                .filter(
                    (item) =>
                        item.quantity > 0 &&
                        (!selectedColor.value ||
                            item.options.some(
                                (option) =>
                                    option.name.toLowerCase() === "color" &&
                                    option.value === selectedColor.value,
                            )),
                )
                .map(
                    (item) =>
                        item.options.find(
                            (option) => option.name.toLowerCase() === "size",
                        )?.value,
                ),
        ),
    );
});

const getAvailableColors = computed(() => {
    return Array.from(
        new Set(
            props.items
                .filter(
                    (item) =>
                        item.quantity > 0 &&
                        (!selectedSize.value ||
                            item.options.some(
                                (option) =>
                                    option.name.toLowerCase() === "size" &&
                                    option.value === selectedSize.value,
                            )),
                )
                .map(
                    (item) =>
                        item.options.find(
                            (option) => option.name.toLowerCase() === "color",
                        )?.value,
                ),
        ),
    );
});
const handlePick = (type, value) => {
    if (type === "color") selectedColor.value = value;
    if (type === "size") selectedSize.value = value;

    const matchedItem = props.items.find(
        (item) =>
            item.options.some(
                (option) =>
                    option.name.toLowerCase() === "color" &&
                    option.value === selectedColor.value,
            ) &&
            item.options.some(
                (option) =>
                    option.name.toLowerCase() === "size" &&
                    option.value === selectedSize.value,
            ),
    );

    if (matchedItem) {
        selected.value = matchedItem;
    }
};

watch(
    [selectedColor, selectedSize, selected],
    ([newColor, newSize, newItem]) => {
        selectedColor.value = newColor;
        selectedSize.value = newSize;
        selected.value = newItem;
    },
    { immediate: true },
);

const addItemToCart = () => {
    const cartItem = {
        ...props.product,
        items: props.product.items.filter((i) => i.id === selected.value.id),
    };
    addToCart(cartItem);
    cartState.value.open = true;
    toast.success(
        t("components.cart.itemAdded", {
            name:
                props.product.name +
                "/" +
                selectedColor.value +
                "/" +
                selectedSize.value,
        }),
    );
};
</script>

<style lang="scss" scoped></style>
