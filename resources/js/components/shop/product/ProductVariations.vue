<template>
    <div class="flex flex-col gap-6">
        <div v-if="selected" class="text-xl font-bold">
            {{ selected.formatted_price }}
        </div>

        <div
            v-for="option in computedVariants"
            :key="option.name"
            class="flex flex-col gap-3"
        >
            <span
                class="text-xs font-bold uppercase tracking-wider text-muted-foreground"
            >
                {{ option.name }}:
                <span class="text-main">{{ selectedValues[option.name] }}</span>
            </span>

            <!-- Color picker using Slider/Swatches -->
            <div
                v-if="option.name.toLowerCase() === 'color'"
                class="flex flex-row gap-4 flex-wrap"
            >
                <div
                    v-for="color in option.options"
                    :key="color.id"
                    class="flex items-center"
                >
                    <button
                        @click="handlePick(option.name, color.value)"
                        :class="[
                            selectedValues[option.name] === color.value
                                ? 'border-2 border-accent_dark p-1'
                                : 'border border-transparent p-1',
                            !isOptionAvailable(option.name, color.value) &&
                                'opacity-40 cursor-not-allowed',
                        ]"
                        class="flex items-center flex-col justify-center rounded-none transition-all"
                        :title="color.value"
                    >
                        <div
                            class="w-8 h-8 rounded-none shadow-inner"
                            :style="{
                                backgroundColor: getColorCode(color.value),
                            }"
                        ></div>
                    </button>
                </div>
            </div>

            <!-- Generic button picker for Size, Material, etc. -->
            <div v-else class="flex flex-wrap gap-2">
                <Button
                    v-for="optValue in option.options"
                    :key="optValue.id"
                    variant="outline"
                    class="min-w-[3rem] h-10 px-3 border text-xs transition-all duration-200"
                    @click="handlePick(option.name, optValue.value)"
                    :disabled="!isOptionAvailable(option.name, optValue.value)"
                    :class="{
                        'bg-primary text-primary-foreground border-accent_dark font-bold shadow-md':
                            selectedValues[option.name] === optValue.value,
                        'border-muted  hover:border-accent_dark hover:bg-primary hover:text-primary-foreground ':
                            selectedValues[option.name] !== optValue.value &&
                            isOptionAvailable(option.name, optValue.value),
                        'opacity-30 cursor-not-allowed bg-accent_light':
                            !isOptionAvailable(option.name, optValue.value),
                    }"
                >
                    {{ optValue.value }}
                </Button>
            </div>
        </div>

        <div class="flex flex-col gap-3 mt-4">
            <Button
                @click="addItemToCart"
                :disabled="
                    !isSelectionComplete || !selected || selected.stock <= 0
                "
                class="view-all text-xs py-4 bg-primary text-primary-foreground uppercase tracking-widest font-bold disabled:opacity-50 disabled:cursor-not-allowed hover:bg-gray-800 transition-colors h-auto"
            >
                {{
                    selected && selected.stock <= 0
                        ? $t("common.product.productOutofStock")
                        : $t("common.product.addToCart")
                }}
            </Button>
            <div
                v-if="selected && selected.stock > 0"
                :class="[
                    'text-[10px] font-bold uppercase  transition-colors duration-300',
                    selected.stock <= 5
                        ? 'text-red-600'
                        : 'text-muted-foreground',
                ]"
            >
                {{
                    selected.stock <= 5
                        ? $t("common.product.lowStock", {
                              count: selected.stock,
                          })
                        : $t("common.product.stock", { count: selected.stock })
                }}
            </div>
        </div>
    </div>
</template>

<script setup>
import { useI18n } from "vue-i18n";
import { ref, computed, watch, onMounted } from "vue";
import { useToast } from "vue-toastification";
import { cartState, addToCart } from "@lib/store/shop/index.js";
import { Button } from "@/components/ui/button";

const toast = useToast();
const { t } = useI18n();

const props = defineProps({
    items: { type: Array, required: true }, // Lunar Product Variants
    product: { type: Object, required: true },
});

// Dynamically build the variants array format from the Lunar items
const computedVariants = computed(() => {
    const optionsMap = {};

    props.items.forEach((item) => {
        item.options?.forEach((opt) => {
            if (!optionsMap[opt.name]) {
                optionsMap[opt.name] = new Set();
            }
            optionsMap[opt.name].add(opt.value);
        });
    });

    return Object.keys(optionsMap).map((name) => ({
        name,
        options: Array.from(optionsMap[name]).map((val) => ({
            id: val,
            value: val,
        })),
    }));
});

const selectedValues = ref({});

const initSelections = () => {
    if (props.items.length > 0) {
        // Find first available item
        const firstAvailable =
            props.items.find((item) => item.stock > 0) || props.items[0];
        const newValues = {};
        firstAvailable.options?.forEach((opt) => {
            newValues[opt.name] = opt.value;
        });
        selectedValues.value = newValues;
    }
};

onMounted(initSelections);

const selected = computed(() => {
    if (!isSelectionComplete.value) return null;

    return props.items.find((item) => {
        return Object.entries(selectedValues.value).every(
            ([optionName, optionValue]) => {
                return item.options?.some(
                    (opt) =>
                        opt.name === optionName && opt.value === optionValue,
                );
            },
        );
    });
});

const isSelectionComplete = computed(() => {
    return computedVariants.value.every((v) => selectedValues.value[v.name]);
});

const isOptionAvailable = (optionName, optionValue) => {
    // Check if there is ANY variant that has this value AND matches other currently selected options
    // To be more user-friendly, we only check against other selections if they are already made.
    return props.items.some((item) => {
        if (item.stock <= 0) return false;

        // Must have this option value
        const hasValue = item.options?.some(
            (opt) => opt.name === optionName && opt.value === optionValue,
        );
        if (!hasValue) return false;

        // Must match other selections (except the one we are currently checking)
        return Object.entries(selectedValues.value).every(([name, value]) => {
            if (name === optionName) return true;
            return item.options?.some(
                (opt) => opt.name === name && opt.value === value,
            );
        });
    });
};

const handlePick = (name, value) => {
    selectedValues.value = {
        ...selectedValues.value,
        [name]: value,
    };
};

// Helper for colors - you might want a more robust map or store this in the DB
const getColorCode = (colorName) => {
    const map = {
        Black: "#000000",
        Schwarz: "#000000",
        White: "#ffffff",
        Weiss: "#ffffff",
        Weiß: "#ffffff",
        Red: "#ff0000",
        Rot: "#ff0000",
        Blue: "#0000ff",
        Blau: "#0000ff",
        Green: "#00ff00",
        Grün: "#00ff00",
        Yellow: "#ffff00",
        Gelb: "#ffff00",
        Orange: "#ffa500",
        Purple: "#800080",
        Lila: "#800080",
        Pink: "#ffc0cb",
        Gray: "#808080",
        Grey: "#808080",
        Grau: "#808080",
        Brown: "#a52a2a",
        Braun: "#a52a2a",
        Beige: "#f5f5dc",
        Navy: "#000080",
        Gold: "#ffd700",
        Silver: "#c0c0c0",
        Silber: "#c0c0c0",
    };
    return map[colorName] || colorName;
};

const addItemToCart = () => {
    if (!selected.value) return;

    const cartItem = {
        ...props.product,
        items: [selected.value], // Only the selected variant
    };

    addToCart(cartItem);
    cartState.value.open = true;

    const optionsStr = Object.values(selectedValues.value).join(" / ");
    toast.success(
        t("components.cart.itemAdded", {
            name: `${props.product.name} (${optionsStr})`,
        }),
    );
};
</script>
