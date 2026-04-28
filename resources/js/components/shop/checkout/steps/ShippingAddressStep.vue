<template>
    <CheckoutStepLayout
        :prevLabel="$t('common.forms.backToCart')"
        prevRoute="/cart"
        :nextLabel="$t('common.forms.continueToShipping')"
        @submit="submitStep"
    >
        <div class="space-y-4">
            <h1 class="text-2xl font-bold uppercase tracking-tight">
                {{ $t("common.forms.contact") }}
            </h1>
            <div class="space-y-2">
                <Label class="text-xs font-bold uppercase">{{
                    $t("common.forms.emailLabel")
                }}</Label>
                <Input
                    v-model="checkoutform.email"
                    type="email"
                    autofocus
                    autocomplete="email"
                    @input="clearError('email')"
                    @blur="validateField('email')"
                    :placeholder="$t('common.forms.emailPlaceholder')"
                    :class="[
                        'rounded-none border-border focus:ring-accent-shadcn',
                        errors.email && 'border-destructive',
                    ]"
                />
                <p v-if="errors.email" class="text-destructive text-xs mt-1">
                    {{ errors.email }}
                </p>
            </div>
        </div>

        <div class="space-y-6">
            <h1 class="text-2xl font-bold uppercase tracking-tight">
                {{ $t("common.forms.deliveryAddress") }}
            </h1>

            <div class="space-y-2">
                <Label class="text-xs font-bold uppercase">{{
                    $t("common.forms.country")
                }}</Label>
                <Select
                    v-model="checkoutform.land"
                    @update:modelValue="
                        clearError('land');
                        validateField('land');
                    "
                >
                    <SelectTrigger
                        :class="[
                            'rounded-none h-12 border-border',
                            errors.land && 'border-destructive',
                        ]"
                    >
                        <SelectValue
                            :placeholder="$t('common.forms.landPlaceholder')"
                        />
                    </SelectTrigger>
                    <SelectContent class="rounded-none">
                        <SelectItem
                            v-for="country in translatedCountries"
                            :key="country.code"
                            :value="country"
                            class="rounded-none"
                        >
                            {{ country.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <p v-if="errors.land" class="text-destructive text-xs mt-1">
                    {{ errors.land }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <Label class="text-xs font-bold uppercase">{{
                        $t("common.forms.firstNameLabel")
                    }}</Label>
                    <Input
                        v-model="checkoutform.firstName"
                        @input="clearError('firstName')"
                        @blur="validateField('firstName')"
                        :class="[
                            'rounded-none border-gray-300',
                            errors.firstName && 'border-red-500',
                        ]"
                    />
                    <p
                        v-if="errors.firstName"
                        class="text-red-500 text-xs mt-1"
                    >
                        {{ errors.firstName }}
                    </p>
                </div>
                <div class="space-y-2">
                    <Label class="text-xs font-bold uppercase">{{
                        $t("common.forms.lastNameLabel")
                    }}</Label>
                    <Input
                        v-model="checkoutform.lastName"
                        @input="clearError('lastName')"
                        @blur="validateField('lastName')"
                        :class="[
                            'rounded-none border-gray-300',
                            errors.lastName && 'border-red-500',
                        ]"
                    />
                    <p v-if="errors.lastName" class="text-red-500 text-xs mt-1">
                        {{ errors.lastName }}
                    </p>
                </div>
            </div>

            <div class="space-y-2">
                <Label class="text-xs font-bold uppercase">{{
                    $t("common.forms.addressLabel")
                }}</Label>
                <Input
                    v-model="checkoutform.address"
                    @input="clearError('address')"
                    @blur="validateField('address')"
                    :class="[
                        'rounded-none border-gray-300',
                        errors.address && 'border-red-500',
                    ]"
                />
                <p v-if="errors.address" class="text-red-500 text-xs mt-1">
                    {{ errors.address }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <Label class="text-xs font-bold uppercase">{{
                        $t("common.forms.zipLabel")
                    }}</Label>
                    <Input
                        v-model="checkoutform.zip"
                        @input="clearError('zip')"
                        @blur="validateField('zip')"
                        :class="[
                            'rounded-none border-gray-300',
                            errors.zip && 'border-red-500',
                        ]"
                    />
                    <p v-if="errors.zip" class="text-red-500 text-xs mt-1">
                        {{ errors.zip }}
                    </p>
                </div>
                <div class="space-y-2">
                    <Label class="text-xs font-bold uppercase">{{
                        $t("common.forms.cityLabel")
                    }}</Label>
                    <Input
                        v-model="checkoutform.city"
                        @input="clearError('city')"
                        @blur="validateField('city')"
                        :class="[
                            'rounded-none border-gray-300',
                            errors.city && 'border-red-500',
                        ]"
                    />
                    <p v-if="errors.city" class="text-red-500 text-xs mt-1">
                        {{ errors.city }}
                    </p>
                </div>
            </div>

            <div class="space-y-2">
                <Label class="text-xs font-bold uppercase">{{
                    $t("common.forms.state")
                }}</Label>
                <Input
                    v-model="checkoutform.stateProvince"
                    @input="clearError('stateProvince')"
                    @blur="validateField('stateProvince')"
                    :placeholder="$t('common.forms.state')"
                    :class="[
                        'rounded-none border-gray-300',
                        errors.stateProvince && 'border-red-500',
                    ]"
                />
                <p
                    v-if="errors.stateProvince"
                    class="text-red-500 text-xs mt-1"
                >
                    {{ errors.stateProvince }}
                </p>
            </div>
        </div>
    </CheckoutStepLayout>
</template>

<script setup>
import CheckoutStepLayout from "./CheckoutStepLayout.vue";
import axios from "axios";
import * as Yup from "yup";
import { useI18n } from "vue-i18n";
import { ChevronLeft } from "lucide-vue-next";
import { onMounted, reactive, ref, computed } from "vue";

// Shadcn UI components
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { Label } from "@/components/ui/label";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";

import { checkoutform } from "@lib/store/shop/index.js";

const emit = defineEmits(["next"]);
const { t, locale } = useI18n();

const errors = reactive({
    email: null,
    firstName: null,
    lastName: null,
    address: null,
    zip: null,
    land: null,
    city: null,
    stateProvince: null,
});

const validationSchema = Yup.object().shape({
    email: Yup.string()
        .email(t("validation.validation.email.invalid"))
        .max(255, t("validation.validation.email.max", { max: 255 }))
        .required(t("validation.validation.email.required")),
    firstName: Yup.string()
        .max(255, t("validation.validation.firstNameMax", { max: 255 }))
        .required(t("validation.validation.firstName")),
    lastName: Yup.string()
        .max(255, t("validation.validation.lastNameMax", { max: 255 }))
        .required(t("validation.validation.lastName")),
    address: Yup.string()
        .max(255, t("validation.validation.addressMax", { max: 255 }))
        .required(t("validation.validation.address")),
    land: Yup.mixed()
        .required(t("validation.validation.land"))
        .test("is-selected", t("validation.validation.land"), (value) => {
            return value && typeof value === "object" && value.code;
        }),
    zip: Yup.string()
        .max(12, t("validation.validation.zipMax", { max: 12 }))
        .required(t("validation.validation.zip")),
    city: Yup.string()
        .max(255, t("validation.validation.cityMax", { max: 255 }))
        .required(t("validation.validation.city")),
    stateProvince: Yup.string()
        .max(255, t("validation.validation.stateMax", { max: 255 }))
        .required(t("validation.validation.state")),
});

const clearError = (field) => {
    errors[field] = null;
};

const validateField = async (field) => {
    try {
        await Yup.reach(validationSchema, field).validate(
            checkoutform.value[field],
        );
        errors[field] = null;
    } catch (error) {
        errors[field] = error.message;
    }
};

const countries = ref([]);
// Array of Stripe-supported European country codes (ISO 3166-1 alpha-2)
const STRIPE_EUROPE_COUNTRIES = [
    "AT",
    "BE",
    "BG",
    "HR",
    "CY",
    "CZ",
    "DK",
    "EE",
    "FI",
    "FR",
    "DE",
    "GR",
    "HU",
    "IE",
    "IT",
    "LV",
    "LI",
    "LT",
    "LU",
    "MT",
    "NL",
    "NO",
    "PL",
    "PT",
    "RO",
    "SK",
    "SI",
    "ES",
    "SE",
    "CH",
    "GB",
    "GI",
];

const fetchCountries = async () => {
    try {
        const response = await axios
            .create()
            .get(
                "https://restcountries.com/v3.1/region/europe?fields=languages,flags,cca2,idd,translations,name",
            );

        // Filter the response data to only include Stripe-supported countries
        countries.value = response.data.filter((country) =>
            STRIPE_EUROPE_COUNTRIES.includes(country.cca2),
        );
    } catch (error) {
        console.error("Error fetching countries:", error);
    }
};

const translatedCountries = computed(() => {
    return countries.value
        .map((country) => {
            const langCode = locale.value === "de" ? "deu" : "eng";
            return {
                name:
                    country.translations[langCode]?.common ||
                    country.name.common,
                code: country.cca2,
            };
        })
        .sort((a, b) => a.name.localeCompare(b.name));
});

const submitStep = async () => {
    try {
        await validationSchema.validate(checkoutform.value, {
            abortEarly: false,
        });
        emit("next");
    } catch (validationErrors) {
        Object.keys(errors).forEach((key) => (errors[key] = null));
        validationErrors.inner.forEach((error) => {
            errors[error.path] = error.message;
        });
    }
};

onMounted(fetchCountries);
</script>
