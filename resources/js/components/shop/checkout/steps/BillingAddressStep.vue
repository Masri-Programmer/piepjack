<template>
    <CheckoutStepLayout
        :title="$t('common.forms.billingAddress')"
        :prevLabel="$t('common.forms.backToShipping')"
        :nextLabel="$t('common.forms.continueToPayment')"
        @prev="$emit('prev')"
        @submit="submitStep"
    >
        <RadioGroup
            v-model="checkoutform.billingSameAsShipping"
            class="mb-8 space-y-3"
        >
            <div
                class="flex items-center space-x-4 p-5 border cursor-pointer transition-all rounded-none"
                :class="
                    checkoutform.billingSameAsShipping
                        ? 'border-primary bg-accent-shadcn ring-1 ring-primary'
                        : 'border-muted'
                "
                @click="checkoutform.billingSameAsShipping = true"
            >
                <RadioGroupItem
                    :value="true"
                    id="same"
                    class="rounded-none border-primary text-primary"
                />
                <Label
                    htmlFor="same"
                    class="text-sm font-bold uppercase tracking-tight cursor-pointer"
                >
                    {{ $t("common.forms.sameAsShipping") }}
                </Label>
            </div>

            <div
                class="flex items-center space-x-4 p-5 border cursor-pointer transition-all rounded-none"
                :class="
                    !checkoutform.billingSameAsShipping
                        ? 'border-primary bg-accent-shadcn ring-1 ring-primary'
                        : 'border-muted'
                "
                @click="checkoutform.billingSameAsShipping = false"
            >
                <RadioGroupItem
                    :value="false"
                    id="diff"
                    class="rounded-none border-primary text-primary"
                />
                <Label
                    htmlFor="diff"
                    class="text-sm font-bold uppercase tracking-tight cursor-pointer"
                >
                    {{ $t("common.forms.differentBilling") }}
                </Label>
            </div>
        </RadioGroup>

        <transition name="fade">
            <div v-if="!checkoutform.billingSameAsShipping" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label class="uppercase text-xs font-bold">{{
                            $t("common.forms.firstNameLabel")
                        }}</Label>
                        <Input
                            v-model="safeBilling.firstName"
                            @input="clearError('firstName')"
                            @blur="validateField('firstName')"
                            :class="[
                                'rounded-none border-border focus:ring-primary',
                                errors.firstName && 'border-destructive',
                            ]"
                        />
                        <p
                            v-if="errors.firstName"
                            class="text-destructive text-xs"
                        >
                            {{ errors.firstName }}
                        </p>
                    </div>
                    <div class="space-y-2">
                        <Label class="uppercase text-xs font-bold">{{
                            $t("common.forms.lastNameLabel")
                        }}</Label>
                        <Input
                            v-model="safeBilling.lastName"
                            @input="clearError('lastName')"
                            @blur="validateField('lastName')"
                            :class="[
                                'rounded-none border-border focus:ring-primary',
                                errors.lastName && 'border-destructive',
                            ]"
                        />
                        <p
                            v-if="errors.lastName"
                            class="text-destructive text-xs"
                        >
                            {{ errors.lastName }}
                        </p>
                    </div>
                </div>

                <div class="space-y-2">
                    <Label class="uppercase text-xs font-bold">{{
                        $t("common.forms.country")
                    }}</Label>
                    <Select
                        v-model="safeBilling.land"
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
                                :placeholder="
                                    $t('common.forms.landPlaceholder')
                                "
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
                    <p v-if="errors.land" class="text-destructive text-xs">
                        {{ errors.land }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label class="uppercase text-xs font-bold">{{
                        $t("common.forms.addressLabel")
                    }}</Label>
                    <Input
                        v-model="safeBilling.address"
                        @input="clearError('address')"
                        @blur="validateField('address')"
                        :class="[
                            'rounded-none border-border',
                            errors.address && 'border-destructive',
                        ]"
                    />
                    <p v-if="errors.address" class="text-destructive text-xs">
                        {{ errors.address }}
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label class="uppercase text-xs font-bold">{{
                            $t("common.forms.zipLabel")
                        }}</Label>
                        <Input
                            v-model="safeBilling.zip"
                            @input="clearError('zip')"
                            @blur="validateField('zip')"
                            :class="[
                                'rounded-none border-border',
                                errors.zip && 'border-destructive',
                            ]"
                        />
                        <p v-if="errors.zip" class="text-destructive text-xs">
                            {{ errors.zip }}
                        </p>
                    </div>
                    <div class="space-y-2">
                        <Label class="uppercase text-xs font-bold">{{
                            $t("common.forms.cityLabel")
                        }}</Label>
                        <Input
                            v-model="safeBilling.city"
                            @input="clearError('city')"
                            @blur="validateField('city')"
                            :class="[
                                'rounded-none border-border',
                                errors.city && 'border-destructive',
                            ]"
                        />
                        <p v-if="errors.city" class="text-destructive text-xs">
                            {{ errors.city }}
                        </p>
                    </div>
                </div>

                <div class="space-y-2">
                    <Label class="uppercase text-xs font-bold">{{
                        $t("common.forms.state")
                    }}</Label>
                    <Input
                        v-model="safeBilling.stateProvince"
                        @input="clearError('stateProvince')"
                        @blur="validateField('stateProvince')"
                        :placeholder="$t('common.forms.state')"
                        :class="[
                            'rounded-none border-border',
                            errors.stateProvince && 'border-destructive',
                        ]"
                    />
                    <p
                        v-if="errors.stateProvince"
                        class="text-destructive text-xs"
                    >
                        {{ errors.stateProvince }}
                    </p>
                </div>
            </div>
        </transition>
    </CheckoutStepLayout>
</template>

<script setup>
import axios from "axios";
import * as Yup from "yup";
import { reactive, ref, computed, onMounted } from "vue";
import { useI18n } from "vue-i18n";
import CheckoutStepLayout from "./CheckoutStepLayout.vue";

import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import { checkoutform } from "@lib/store/shop/index.js";

const emit = defineEmits(["next", "prev"]);
const { t, locale } = useI18n();

if (!checkoutform.value.billing) {
    checkoutform.value.billing = {
        firstName: "",
        lastName: "",
        address: "",
        zip: "",
        city: "",
        land: "",
        stateProvince: "",
    };
}

const safeBilling = computed(() => checkoutform.value.billing);
const errors = reactive({
    firstName: null,
    lastName: null,
    address: null,
    zip: null,
    city: null,
    land: null,
    stateProvince: null,
});

// ... Validation logic and fetching countries (exact same as original) ...
const validationSchema = Yup.object().shape({
    firstName: Yup.string()
        .max(255, t("validation.validation.firstNameMax", { max: 255 }))
        .required(t("validation.validation.firstName")),
    lastName: Yup.string()
        .max(255, t("validation.validation.lastNameMax", { max: 255 }))
        .required(t("validation.validation.lastName")),
    address: Yup.string()
        .max(255, t("validation.validation.addressMax", { max: 255 }))
        .required(t("validation.validation.address")),
    zip: Yup.string()
        .max(12, t("validation.validation.zipMax", { max: 12 }))
        .required(t("validation.validation.zip")),
    city: Yup.string()
        .max(255, t("validation.validation.cityMax", { max: 255 }))
        .required(t("validation.validation.city")),
    land: Yup.mixed().required(t("validation.validation.land")),
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
            safeBilling.value[field],
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
    if (checkoutform.value.billingSameAsShipping) {
        checkoutform.value.billing = { ...checkoutform.value };
        emit("next");
        return;
    }
    try {
        await validationSchema.validate(checkoutform.value.billing, {
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

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
