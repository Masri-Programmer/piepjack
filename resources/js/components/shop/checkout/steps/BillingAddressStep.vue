<template>
    <form @submit.prevent="submitStep" class="contact-form">
        <h1 class="text-2xl font-bold mb-6 uppercase tracking-tight">
            {{ $t("common.forms.billingAddress") }}
        </h1>

        <RadioGroup
            v-model="checkoutform.billingSameAsShipping"
            class="mb-8 space-y-3"
        >
            <div
                class="flex items-center space-x-4 p-5 border cursor-pointer transition-all rounded-none"
                :class="
                    checkoutform.billingSameAsShipping
                        ? 'border-accent_dark bg-accent_light ring-1 ring-accent_dark'
                        : 'border-muted'
                "
                @click="checkoutform.billingSameAsShipping = true"
            >
                <RadioGroupItem
                    :value="true"
                    id="same"
                    class="rounded-none border-accent_dark text-main"
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
                        ? 'border-accent_dark bg-accent_light ring-1 ring-accent_dark'
                        : 'border-muted'
                "
                @click="checkoutform.billingSameAsShipping = false"
            >
                <RadioGroupItem
                    :value="false"
                    id="diff"
                    class="rounded-none border-accent_dark text-main"
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
                            :class="[
                                'rounded-none border-gray-300 focus:ring-accent_dark',
                                errors.firstName && 'border-red-500',
                            ]"
                        />
                        <p v-if="errors.firstName" class="text-red-500 text-xs">
                            {{ errors.firstName }}
                        </p>
                    </div>
                    <div class="space-y-2">
                        <Label class="uppercase text-xs font-bold">{{
                            $t("common.forms.lastNameLabel")
                        }}</Label>
                        <Input
                            v-model="safeBilling.lastName"
                            :class="[
                                'rounded-none border-gray-300 focus:ring-accent_dark',
                                errors.lastName && 'border-red-500',
                            ]"
                        />
                        <p v-if="errors.lastName" class="text-red-500 text-xs">
                            {{ errors.lastName }}
                        </p>
                    </div>
                </div>

                <div class="space-y-2">
                    <Label class="uppercase text-xs font-bold">{{
                        $t("common.forms.country")
                    }}</Label>
                    <Select v-model="safeBilling.land">
                        <SelectTrigger
                            :class="[
                                'rounded-none h-12 border-gray-300',
                                errors.land && 'border-red-500',
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
                    <p v-if="errors.land" class="text-red-500 text-xs">
                        {{ errors.land }}
                    </p>
                </div>

                <div class="space-y-2">
                    <Label class="uppercase text-xs font-bold">{{
                        $t("common.forms.addressLabel")
                    }}</Label>
                    <Input
                        v-model="safeBilling.address"
                        :class="[
                            'rounded-none border-gray-300',
                            errors.address && 'border-red-500',
                        ]"
                    />
                    <p v-if="errors.address" class="text-red-500 text-xs">
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
                            :class="[
                                'rounded-none border-gray-300',
                                errors.zip && 'border-red-500',
                            ]"
                        />
                        <p v-if="errors.zip" class="text-red-500 text-xs">
                            {{ errors.zip }}
                        </p>
                    </div>
                    <div class="space-y-2">
                        <Label class="uppercase text-xs font-bold">{{
                            $t("common.forms.cityLabel")
                        }}</Label>
                        <Input
                            v-model="safeBilling.city"
                            :class="[
                                'rounded-none border-gray-300',
                                errors.city && 'border-red-500',
                            ]"
                        />
                        <p v-if="errors.city" class="text-red-500 text-xs">
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
                        :placeholder="$t('common.forms.state')"
                        :class="[
                            'rounded-none border-gray-300',
                            errors.stateProvince && 'border-red-500',
                        ]"
                    />
                    <p v-if="errors.stateProvince" class="text-red-500 text-xs">
                        {{ errors.stateProvince }}
                    </p>
                </div>
            </div>
        </transition>

        <div class="flex justify-between items-center mt-10">
            <Button
                type="button"
                variant="ghost"
                class="rounded-none flex items-center gap-2 px-0 hover:bg-transparent"
                @click="$emit('prev')"
            >
                <ChevronLeft :size="20" />
                <span class="uppercase text-xs font-bold">{{
                    $t("common.forms.backToShipping")
                }}</span>
            </Button>

            <Button
                type="submit"
                class="rounded-none bg-primary text-primary-foreground hover:bg-gray-800 px-8 py-6 uppercase font-bold"
            >
                {{ $t("common.forms.continueToPayment") }}
            </Button>
        </div>
    </form>
</template>

<script setup>
import axios from "axios";
import * as Yup from "yup";
import { reactive, ref, computed, onMounted } from "vue";
import { useI18n } from "vue-i18n";
import { ChevronLeft } from "lucide-vue-next";

// Shadcn imports
import { Button } from "@/components/ui/button";
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

// Initialization and state logic remains the same as your original snippet
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

const validationSchema = Yup.object().shape({
    firstName: Yup.string().required(t("validation.validation.firstName")),
    lastName: Yup.string().required(t("validation.validation.lastName")),
    address: Yup.string().required(t("validation.validation.address")),
    zip: Yup.string().required(t("validation.validation.zip")),
    city: Yup.string().required(t("validation.validation.city")),
    land: Yup.mixed().required(t("validation.validation.land")),
    stateProvince: Yup.string().required(t("validation.validation.state")),
});

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
        checkoutform.value.billing = { ...checkoutform.value }; // Sync logic
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
