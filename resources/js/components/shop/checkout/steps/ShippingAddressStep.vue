<template>
    <form @submit.prevent="submitStep" class="max-w-2xl mx-auto space-y-8">
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
                        'rounded-none border-gray-300 focus:ring-accent_dark',
                        errors.email && 'border-red-500',
                    ]"
                />
                <p v-if="errors.email" class="text-red-500 text-xs mt-1">
                    {{ errors.email }}
                </p>
            </div>
        </div>

        <div class="space-y-6">
            <h1 class="text-2xl font-bold uppercase tracking-tight">
                {{ $t("common.forms.deliveryAddress") }}
            </h1>

            <div class="space-y-2">
                <Label class="text-xs font-bold uppercase">
                    {{ $t("common.forms.country") || "Land / Region" }}
                </Label>
                <Select
                    v-model="checkoutform.land"
                    @update:modelValue="
                        clearError('land');
                        validateField('land');
                    "
                >
                    <SelectTrigger
                        :class="[
                            'rounded-none h-12 border-gray-300',
                            errors.land && 'border-red-500',
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
                <p v-if="errors.land" class="text-red-500 text-xs mt-1">
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

        <div
            class="flex justify-between items-center pt-8 border-t border-gray-100 gap-4"
        >
            <Button
                as-child
                variant="ghost"
                class="rounded-none hover:bg-transparent px-0 group"
            >
                <router-link to="/cart" class="flex items-center gap-2">
                    <ChevronLeft
                        :size="20"
                        class="transition-transform group-hover:-translate-x-1"
                    />
                    <span class="text-xs font-bold uppercase">{{
                        $t("common.forms.backToCart")
                    }}</span>
                </router-link>
            </Button>

            <Button type="submit" class="view-all">
                {{ $t("common.forms.continueToShipping") }}
            </Button>
        </div>
    </form>
</template>

<script setup>
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
    land: Yup.mixed().required(t("validation.validation.land")),
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
const fetchCountries = async () => {
    try {
        const response = await axios
            .create()
            .get(
                "https://restcountries.com/v3.1/region/europe?fields=languages,flags,cca2,idd,translations,name",
            );
        countries.value = response.data;
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
