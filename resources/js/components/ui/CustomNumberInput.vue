<template>
    <div class="cart-product-custom-number-input">
        <div class="cart-product-quantity-input" v-if="addToCart">
            <div class="cart-product-quantity-buttons">
                <button
                    @click="handleDecrement"
                    class="cart-product-quantity-decrement"
                >
                    <span class="m-auto text-2xl font-thin">−</span>
                </button>
                <input
                    type="number"
                    v-model="count"
                    @input="(event) => updateQuantity(event.target.value)"
                    class="cart-product-quantity-input-box"
                    name="custom-input-number"
                    :min="min"
                    :max="max"
                />
                <button
                    @click="handleIncrement"
                    class="cart-product-quantity-increment"
                >
                    <span class="m-auto text-2xl font-thin">+</span>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import "@assets/css/customerNumberInput.css";
import { ref, watch } from "vue";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { useCounter } from "@vueuse/core";
import { removeFromCart, updateItemQuantity } from "@lib/store/shop/index.js";

const { t } = useI18n();
const toast = useToast();

const props = defineProps({
    modelValue: {
        type: Number,
        required: true,
    },
    min: {
        type: Number,
        default: 1,
    },
    max: {
        type: Number,
        default: Infinity,
    },
    addToCart: {
        type: Boolean,
        default: false,
    },
    label: {
        type: String,
        default: "",
    },
    item: {
        type: Object,
        default: () => ({}),
    },
    product: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(["update:modelValue"]);

const { count, inc, dec, set } = useCounter(props.modelValue);

watch(
    () => props.modelValue,
    (newValue) => {
        set(newValue);
    }
);

const updateQuantity = (newQuantity) => {
    updateItemQuantity({
        productId: props.product.id,
        itemId: props.item?.id,
        quantity: newQuantity,
    });
};

const handleIncrement = () => {
    if (props.item.quantity === count.value) {
        toast.warning(t("common.product.maximum_quantity"));
        return;
    } else {
        updateQuantity(count.value + 1);
        inc();
    }
};

const handleDecrement = () => {
    if (count.value > 1) {
        updateQuantity(count.value - 1);
        dec();
    } else {
        handleDelete();
    }
};
const handleDelete = () => {
    const confirmation = window.confirm(
        t("components.cart.removeCartConfirmation")
    );
    if (confirmation) {
        removeFromCart({
            productId: props.product.id,
            itemId: props.item?.id,
        });
    }
};
</script>
