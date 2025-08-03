<template>
  <p class="text-gray-300 text-xs">{{ selected?.price }} €</p>
  <div class="flex flex-row gap-4">
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
        (variant) => variant.name.toLowerCase() === 'size'
      )?.options"
      :key="size.id"
    >
      <button
        class="border w-8 h-8 grid place-content-center p-5 relative text-xs text-wrap break-words "
        @click="handlePick('size', size.value)"
        :class="[
          selectedSize === size.value &&
            'bg-main text-accent font-bold shadow-md border-gray border-3',
          !getAvailableSizes.includes(size.value)
            ? 'meh-diagonal border border-gray text-gray w-8 h-8 relative grid place-content-center p-5'
            : 'border w-8 h-8 grid place-content-center p-5 relative',
        ]"
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
import { cartState , addToCart} from "@lib/store/shop/index.js";

const toast = useToast();
const { t } = useI18n();

const props = defineProps({
  variants: { type: Array, required: true },
  items: { type: Object, required: true },
  product: { type: Object, required: true },
});
const selectedColor = useStorage(
  "selectedColor",
  props.items[0].options.find((o) => o.name.toLowerCase() === "color")?.value ||
    null
);
const selectedSize = useStorage(
  "selectedSize",
  props.items[0].options.find((o) => o.name.toLowerCase() === "size")?.value ||
    null
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
                  option.value === selectedColor.value
              ))
        )
        .map(
          (item) =>
            item.options.find((option) => option.name.toLowerCase() === "size")
              ?.value
        )
    )
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
                  option.value === selectedSize.value
              ))
        )
        .map(
          (item) =>
            item.options.find((option) => option.name.toLowerCase() === "color")
              ?.value
        )
    )
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
          option.value === selectedColor.value
      ) &&
      item.options.some(
        (option) =>
          option.name.toLowerCase() === "size" &&
          option.value === selectedSize.value
      )
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
  { immediate: true }
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
    })
  );
};
</script>

<style lang="scss" scoped></style>
