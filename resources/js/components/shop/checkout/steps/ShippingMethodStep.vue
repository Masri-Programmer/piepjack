<template>
    <div class="contact-form">
        <h1 class="contact-form-title">
            {{ $t("common.forms.shippingMethod") }}
        </h1>

        <div v-if="isLoading" class="flex justify-center py-12">
            <Spinner class="h-10 w-10" />
        </div>

        <div
            v-else-if="!shippingMethods.length"
            class="text-center py-12 text-muted-foreground"
        >
            Keine Versandmethoden für Ihre Adresse verfügbar. Bitte prüfen Sie
            Ihre Angaben.
        </div>

        <div v-else class="space-y-3">
            <div
                v-for="method in shippingMethods"
                :key="method.id"
                @click="checkoutform.shippingMethodId = method.id"
                class="flex items-center justify-between p-5 border cursor-pointer transition-all duration-200"
                :class="
                    checkoutform.shippingMethodId === method.id
                        ? 'border-accent_dark bg-accent_light ring-1 ring-accent_dark'
                        : 'border-muted hover:border-gray-400'
                "
            >
                <div class="flex items-center gap-4">
                    <input
                        type="radio"
                        :id="'method-' + method.id"
                        :value="method.id"
                        v-model="checkoutform.shippingMethodId"
                        class="accent-accent_dark w-4 h-4"
                    />
                    <div>
                        <p class="font-bold text-sm uppercase tracking-tight">
                            {{ method.name }}
                        </p>
                        <p
                            class="text-xs text-muted-foreground mt-0.5"
                            v-if="method.description"
                        >
                            {{ method.description }}
                        </p>
                    </div>
                </div>
                <p class="font-bold text-sm">
                    {{
                        method.price === 0
                            ? "Kostenlos"
                            : method.formatted_price
                    }}
                </p>
            </div>
        </div>

        <div class="contact-form-footer mt-8">
            <Button
                @click="$emit('prev')"
                variant="ghost"
                class="contact-form-back-link"
            >
                <ChevronLeft size="20" />{{ $t("common.forms.backToInfo") }}
            </Button>
            <Button
                @click="$emit('next')"
                :disabled="!checkoutform.shippingMethodId"
                class="contact-form-submit view-all h-auto"
            >
                {{ $t("common.forms.continueToBilling") }}
            </Button>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref, computed, watch } from "vue";
import { ChevronLeft } from "lucide-vue-next";
import { checkoutform, cartState } from "@lib/store/shop/index.js";
import { apiRequest } from "@lib/helpers";
import Spinner from "@components/ui/Spinner.vue";
import { Button } from "@/components/ui/button";
import "@assets/css/checkout/contactFrom.css";

defineEmits(["next", "prev"]);

const shippingMethods = ref([]);
const isLoading = ref(false);

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
                checkoutform.value.shippingMethodId =
                    shippingMethods.value[0].id;
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
