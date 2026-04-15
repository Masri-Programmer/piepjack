<template>
    <CheckoutStepLayout
        :title="$t('common.forms.shippingMethod')"
        :prevLabel="$t('common.forms.backToInfo')"
        :nextLabel="$t('common.forms.continueToBilling')"
        :nextDisabled="!checkoutform.shippingMethodId"
        @prev="$emit('prev')"
        @submit="$emit('next')"
    >
        <div v-if="isLoading" class="flex justify-center py-12">
            <Spinner class="h-10 w-10 text-primary" />
        </div>

        <div
            v-else-if="!displayShippingMethods.length"
            class="text-center py-12 text-muted-foreground"
        >
            {{ $t("common.forms.noShippingMethods") }}
        </div>

        <RadioGroup
            v-else
            :modelValue="checkoutform.shippingMethodId"
            @update:modelValue="selectMethod"
            class="space-y-3"
        >
            <div
                v-for="method in displayShippingMethods"
                :key="method.id"
                @click="selectMethod(method.id)"
                class="flex items-center justify-between p-5 border cursor-pointer transition-all duration-200 rounded-none"
                :class="
                    checkoutform.shippingMethodId === method.id
                        ? 'border-primary bg-accent-shadcn ring-1 ring-primary'
                        : 'border-muted hover:border-gray-400'
                "
            >
                <div class="flex items-center gap-4">
                    <RadioGroupItem
                        :value="method.id"
                        :id="'method-' + method.id"
                        class="rounded-none border-primary text-primary pointer-events-none"
                    />
                    <Label
                        :htmlFor="'method-' + method.id"
                        class="cursor-pointer"
                    >
                        <p
                            class="font-bold text-sm uppercase tracking-tight text-foreground"
                        >
                            {{ method.name }}
                        </p>
                        <p
                            class="text-xs text-muted-foreground mt-0.5"
                            v-if="method.description"
                        >
                            {{ method.description }}
                        </p>
                    </Label>
                </div>
                <p class="font-bold text-sm text-foreground">
                    {{
                        method.price === 0
                            ? $t("common.forms.free")
                            : method.formatted_price
                    }}
                </p>
            </div>
        </RadioGroup>
    </CheckoutStepLayout>
</template>

<script setup>
import { onMounted, ref, computed, watch } from "vue";
import CheckoutStepLayout from "./CheckoutStepLayout.vue";
import { checkoutform, cartState } from "@lib/store/shop/index.js";
import { apiRequest } from "@lib/helpers";
import Spinner from "@components/ui/Spinner.vue";

// Import Shadcn UI Radio components & Label
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group";
import { Label } from "@/components/ui/label";

defineEmits(["next", "prev"]);

const shippingMethods = ref([]);
const isLoading = ref(false);

const displayShippingMethods = computed(() => shippingMethods.value);

// Extracted selection logic to handle both click on div and RadioGroup updates
const selectMethod = (methodId) => {
    const selected = shippingMethods.value.find((m) => m.id === methodId);
    if (selected) {
        checkoutform.value.shippingMethodId = selected.id;
        checkoutform.value.shippingMethod = selected;
    }
};

const loadMethods = async () => {
    const countryCode =
        checkoutform.value.land?.code || checkoutform.value.land;

    const payload = {
        country_code: countryCode,
        postal_code: checkoutform.value.zip,
        email: checkoutform.value.email,
        products: transformCartItems(cartState.value.cartItems),
    };

    if (!payload.country_code || !payload.products.length) return;

    isLoading.value = true;
    try {
        const response = await apiRequest("post", "/shipping-methods", payload);
        if (response?.data) {
            shippingMethods.value = response.data;

            // Default to first if not set
            if (
                !checkoutform.value.shippingMethodId &&
                shippingMethods.value.length
            ) {
                selectMethod(shippingMethods.value[0].id);
            } else if (checkoutform.value.shippingMethodId) {
                // Sync current selection
                selectMethod(checkoutform.value.shippingMethodId);
            }
        }
    } catch (error) {
        console.error("Failed to load shipping methods:", error);
    } finally {
        isLoading.value = false;
    }
};

function transformCartItems(cartItems) {
    return cartItems.flatMap((product) => {
        return product.items.map((item) => ({
            id: item.id,
            quantity: parseInt(item.cartQuantity) || 1,
        }));
    });
}

onMounted(loadMethods);

watch(
    [
        () => checkoutform.value.land?.code || checkoutform.value.land,
        () => checkoutform.value.zip,
        () => cartState.value.cartItems,
    ],
    () => loadMethods(),
    { deep: true },
);
</script>
