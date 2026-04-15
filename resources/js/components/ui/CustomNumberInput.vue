<template>
    <div
        v-if="addToCart"
        class="flex items-center border border-border bg-background rounded-none w-fit"
    >
        <Button
            variant="ghost"
            size="icon"
            @click="handleDecrement"
            class="h-8 w-8 rounded-none border-r border-border hover:bg-accent-shadcn text-foreground hover:text-foreground shrink-0"
        >
            <Minus class="w-3 h-3" />
        </Button>

        <Input
            type="number"
            v-model="count"
            @input="(event) => updateQuantity(event.target.value)"
            name="custom-input-number"
            :min="min"
            :max="max"
            class="h-8 w-12 rounded-none border-none p-0 text-center text-sm font-medium focus-visible:ring-0 shadow-none hide-spin-button bg-transparent text-foreground"
        />

        <Button
            variant="ghost"
            size="icon"
            @click="handleIncrement"
            class="h-8 w-8 rounded-none border-l border-border hover:bg-accent-shadcn text-foreground hover:text-foreground shrink-0"
        >
            <Plus class="w-3 h-3" />
        </Button>
    </div>
</template>

<script setup>
import { ref, watch } from "vue";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toastification";
import { useCounter } from "@vueuse/core";
import { removeFromCart, updateItemQuantity } from "@lib/store/shop/index.js";

// Shadcn & Icons
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Plus, Minus } from "lucide-vue-next";

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
    },
);

const updateQuantity = (newQuantity) => {
    updateItemQuantity({
        productId: props.product.id,
        itemId: props.item?.id,
        quantity: parseInt(newQuantity, 10) || 1, // Ensure integer
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
        t("components.cart.removeCartConfirmation"),
    );
    if (confirmation) {
        removeFromCart({
            productId: props.product.id,
            itemId: props.item?.id,
        });
    }
};
</script>

<style scoped>
.hide-spin-button::-webkit-outer-spin-button,
.hide-spin-button::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

.hide-spin-button[type="number"] {
    -moz-appearance: textfield;
}
</style>
