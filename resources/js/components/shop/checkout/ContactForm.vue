<template>
    <form @submit.prevent="submitForm" class="contact-form">
        <div class="contact-form-section">
            <Tooltip :content="$t('common.forms.emailTooltip')" />
            <h1 class="contact-form-title">{{ $t("common.forms.contact") }}</h1>
            <label class="contact-form-label">{{
                $t("common.forms.emailLabel")
            }}</label>
            <input
                v-model="checkoutform.email"
                type="email"
                autofocus
                autocomplete="email"
                :class="[
                    'contact-form-input',
                    errors.email ? 'contact-form-error' : '',
                ]"
                :placeholder="$t('common.forms.emailPlaceholder')"
            />
            <transition name="fade">
                <p v-if="errors.email" class="contact-form-error-message">
                    {{ errors.email }}
                </p>
            </transition>
        </div>

        <h1 class="contact-form-title">
            {{ $t("common.forms.deliveryAddress") }}
        </h1>

        <div class="contact-form-section">
            <label class="contact-form-label"> Land / Region </label>
            <select
                v-model="checkoutform.land"
                @change="updatePhoneCode"
                autocomplete="country-name"
                :class="[
                    'contact-form-input',
                    errors.land ? 'contact-form-error' : '',
                ]"
            >
                <option disabled value="" class="contact-form-placeholder">
                    {{ $t("common.forms.landPlaceholder") }}
                </option>
                <option
                    v-for="country in translatedCountries"
                    :key="country.code"
                    :value="country"
                    class="contact-form-option"
                >
                    {{ country.name }}
                </option>
            </select>
            <transition name="fade">
                <p v-if="errors.land" class="contact-form-error-message">
                    {{ errors.land }}
                </p>
            </transition>
        </div>

        <div class="contact-form-section">
            <label class="contact-form-label">
                {{ $t("common.forms.state") }}</label
            >
            <input
                v-model="checkoutform.stateProvince"
                type="text"
                autocomplete="address-level1"
                :class="[
                    'contact-form-input',
                    errors.stateProvince ? 'contact-form-error' : '',
                ]"
                placeholder="State / Province"
            />
            <transition name="fade">
                <p
                    v-if="errors.stateProvince"
                    class="contact-form-error-message"
                >
                    {{ errors.stateProvince }}
                </p>
            </transition>
        </div>

        <div class="contact-form-grid">
            <div class="contact-form-section">
                <label class="contact-form-label">{{
                    $t("common.forms.firstNameLabel")
                }}</label>
                <input
                    v-model="checkoutform.firstName"
                    type="text"
                    autocomplete="given-name"
                    :class="[
                        'contact-form-input',
                        errors.firstName ? 'contact-form-error' : '',
                    ]"
                    :placeholder="$t('common.forms.firstNamePlaceholder')"
                />
                <transition name="fade">
                    <p
                        v-if="errors.firstName"
                        class="contact-form-error-message"
                    >
                        {{ errors.firstName }}
                    </p>
                </transition>
            </div>
            <div class="contact-form-section">
                <label class="contact-form-label">{{
                    $t("common.forms.lastNameLabel")
                }}</label>
                <input
                    v-model="checkoutform.lastName"
                    type="text"
                    autocomplete="family-name"
                    :class="[
                        'contact-form-input',
                        errors.lastName ? 'contact-form-error' : '',
                    ]"
                    :placeholder="$t('common.forms.lastNamePlaceholder')"
                />
                <transition name="fade">
                    <p
                        v-if="errors.lastName"
                        class="contact-form-error-message"
                    >
                        {{ errors.lastName }}
                    </p>
                </transition>
            </div>
        </div>

        <div class="contact-form-section">
            <label class="contact-form-label">{{
                $t("common.forms.addressLabel")
            }}</label>
            <input
                v-model="checkoutform.address"
                type="text"
                autocomplete="street-address"
                :class="[
                    'contact-form-input',
                    errors.address ? 'contact-form-error' : '',
                ]"
                :placeholder="$t('common.forms.addressPlaceholder')"
            />
            <transition name="fade">
                <p v-if="errors.address" class="contact-form-error-message">
                    {{ errors.address }}
                </p>
            </transition>
        </div>

        <div class="contact-form-grid">
            <div class="contact-form-section">
                <label class="contact-form-label">{{
                    $t("common.forms.zipLabel")
                }}</label>
                <input
                    v-model="checkoutform.zip"
                    type="text"
                    autocomplete="postal-code"
                    :class="[
                        'contact-form-input',
                        errors.zip ? 'contact-form-error' : '',
                    ]"
                    :placeholder="$t('common.forms.zipPlaceholder')"
                />
                <transition name="fade">
                    <p v-if="errors.zip" class="contact-form-error-message">
                        {{ errors.zip }}
                    </p>
                </transition>
            </div>
            <div class="contact-form-section">
                <label class="contact-form-label">{{
                    $t("common.forms.cityLabel")
                }}</label>
                <input
                    v-model="checkoutform.city"
                    type="text"
                    autocomplete="address-level2"
                    :class="[
                        'contact-form-input',
                        errors.city ? 'contact-form-error' : '',
                    ]"
                    :placeholder="$t('common.forms.cityPlaceholder')"
                />
                <transition name="fade">
                    <p v-if="errors.city" class="contact-form-error-message">
                        {{ errors.city }}
                    </p>
                </transition>
            </div>
        </div>

        <div class="contact-form-legal-notice my-4 text-xs text-gray-500">
            Mit Ihrer Bestellung erklären Sie sich mit unseren
            <router-link to="/terms-of-service" class="underline text-blue-600"
                >AGB</router-link
            >
            und der
            <router-link
                to="/widerrufsbelehrung"
                class="underline text-blue-600"
                >Widerrufsbelehrung</router-link
            >
            einverstanden.
        </div>

        <div class="contact-form-footer">
            <button type="button">
                <router-link to="/cart" class="contact-form-back-link">
                    <ChevronLeft size="20" />{{ $t("common.forms.backToCart") }}
                </router-link>
            </button>
            <button type="submit" class="contact-form-submit view-all">
                <div v-if="!isLoading">
                    {{ $t("common.forms.continueToShipping") }}
                </div>
                <div v-else class="contact-form-loading">
                    <Spinner class="h-6 w-8" />
                </div>
            </button>
        </div>
    </form>
</template>

<script setup>
import axios from "axios";
import * as Yup from "yup";
import { useI18n } from "vue-i18n";
import Tooltip from "../Tooltip.vue";
import { apiQuery } from "@lib/helpers";
import "@assets/css/checkout/contactFrom.css";
import { ChevronLeft } from "lucide-vue-next";
import Spinner from "@components/ui/Spinner.vue";
import { onMounted, reactive, ref, computed, watch } from "vue";
import { cartState, checkoutform } from "@lib/store/shop/index.js";

const errors = reactive({
    email: null,
    firstName: null,
    lastName: null,
    address: null,
    zip: null,
    land: null,
    phone: null,
    city: null,
    stateProvince: null, // FIX: Added stateProvince to errors object
});
const { t } = useI18n();
const validationSchema = Yup.object().shape({
    email: Yup.string()
        .email(t("validation.validation.email.invalid"))
        .required(t("validation.validation.email.required")),
    firstName: Yup.string().required(t("validation.validation.firstName")),
    lastName: Yup.string().required(t("validation.validation.lastName")),
    address: Yup.string().required(t("validation.validation.address")),
    land: Yup.lazy((value) => {
        if (typeof value === "object" && value !== null) {
            return Yup.object()
                .shape({
                    tel_code: Yup.string().required(),
                    name: Yup.string().required(),
                    code: Yup.string().required(),
                    flag: Yup.object()
                        .shape({
                            png: Yup.string().url().required(),
                            svg: Yup.string().url().required(),
                            alt: Yup.string(),
                        })
                        .required(),
                })
                .required(t("validation.validation.land"));
        } else if (typeof value === "string") {
            return Yup.string()
                .min(1)
                .required(t("validation.validation.land"));
        }
        return Yup.mixed().required(t("validation.validation.land"));
    }),
    zip: Yup.string().required(t("validation.validation.zip")),
    city: Yup.string().required(t("validation.validation.city")),
    stateProvince: Yup.string().required("State or Province is required."),
});

const { mutate: checkout, isLoading } = apiQuery("checkout").useStore();

const submitForm = async () => {
    try {
        // Make sure to add 'stateProvince' to your checkoutform store
        await validationSchema.validate(checkoutform.value, {
            abortEarly: false,
        });
        const requestform = buildRequestForm(checkoutform.value);

        checkout(requestform, {
            onSuccess: (data) => {
                if (data?.url) {
                    resetErrors();
                    window.location.href = data.url;
                }
            },
        });
    } catch (validationErrors) {
        // Clear previous errors
        resetErrors();
        validationErrors.inner.forEach((error) => {
            errors[error.path] = error.message;
        });
        console.error("Validation Errors:", errors);
    }
};

const countries = ref([]);
const { locale } = useI18n();

const fetchCountries = async () => {
    try {
        const response = await axios.get(
            "https://restcountries.com/v3.1/region/europe?fields=languages,flags,cca2,idd,alpha,translations,name",
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
            const countryName =
                country.translations[langCode]?.common || country.name.common;
            return {
                name: countryName,
                tel_code:
                    country.idd.root +
                    (country.idd.suffixes ? country.idd.suffixes[0] : ""),
                code: country.cca2,
                flag: country.flags,
            };
        })
        .sort((a, b) => a.name.localeCompare(b.name));
});

const updatePhoneCode = () => {
    if (checkoutform.value.land.tel_code) {
        if (checkoutform.value.phone) {
            if (
                checkoutform.value.phone.startsWith("+") ||
                checkoutform.value.phone.startsWith("00")
            ) {
                return;
            }
            checkoutform.value.phone =
                checkoutform.value.land.tel_code + checkoutform.value.phone;
        } else {
            checkoutform.value.phone = checkoutform.value.land.tel_code;
        }
    }
};

const clearErrorOnInputChange = () => {
    Object.keys(checkoutform.value).forEach((key) => {
        watch(
            () => checkoutform.value[key],
            () => {
                if (errors[key]) {
                    errors[key] = null;
                }
            },
        );
    });
};

const buildRequestForm = (form) => {
    return {
        email: form.email,
        first_name: form.firstName,
        last_name: form.lastName,

        street_address: form.address,
        city: form.city,
        postal_code: form.zip,
        country_code: form.land.code,
        state_province: form.stateProvince,

        products: transformCartItmes(cartState.value.cartItems),
        promo_code: cartState.value.promoCode || null,
    };
};

function transformCartItmes(cartItems) {
    return cartItems.flatMap((product) => {
        return product.items.map((item) => ({
            id: item.id,
            quantity: item.cartQuantity,
        }));
    });
}

function resetErrors() {
    Object.keys(errors).forEach((key) => {
        errors[key] = null;
    });
}
onMounted(() => {
    fetchCountries();
    clearErrorOnInputChange();
});
</script>
