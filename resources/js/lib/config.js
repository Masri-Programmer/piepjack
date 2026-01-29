import { createI18n } from "vue-i18n";
import { apiRequest } from "./helpers";
import de from "./locales/de/index";
import en from "./locales/en/index";
import { nextTick } from "vue";
import { SUPPORT_LOCALES } from "./constants";

const getSavedLocale = () => localStorage.getItem("lang") || "de";

export function setupI18n(options = { locale: getSavedLocale() }) {
    const i18n = createI18n({
        locale: options.locale,
        fallbackLocale: options.locale,
        messages: { en, de },
        ...options,
    });

    setI18nLanguage(i18n, options.locale);
    return i18n;
}
export function setI18nLanguage(i18n, locale) {
    if (i18n.mode === "legacy") {
        i18n.global.locale = locale;
    } else {
        i18n.global.locale.value = locale;
    }

    // Set HTML lang attribute
    document.querySelector("html").setAttribute("lang", locale);

    // Store in localStorage
    localStorage.setItem("lang", locale);

    return locale;
}
export async function loadLocaleMessages(i18n, locale) {
    // Only load if the locale isn't already loaded
    if (!i18n.global.availableLocales.includes(locale)) {
        try {
            const messages = await import(
                /* webpackChunkName: "locale-[request]" */ `./locales/${locale}/index.js`
            );
            i18n.global.setLocaleMessage(locale, messages.default);
        } catch (error) {
            console.error(`Failed to load locale: ${locale}`, error);
        }
    }

    return nextTick();
}
export const changeLanguage = async (lang) => {
    if (!SUPPORT_LOCALES.includes(lang)) {
        console.warn(`Locale ${lang} is not supported`);
        return;
    }
    await loadLocaleMessages(i18n, lang);
    return setI18nLanguage(i18n, lang);
};
export const i18n = setupI18n();

export const createQueryClient = (QueryClient) => {
    return new QueryClient({
        defaultOptions: {
            queries: {
                refetchOnWindowFocus: false,
                staleTime: 1000 * 60 * 5,
                cacheTime: 1000 * 60 * 10,
                retry: 2,
            },
        },
    });
};

// Shared Toast options
export const toastOptions = {
    position: "top-right",
    timeout: 2000,
    closeOnClick: true,
    pauseOnFocusLoss: true,
    pauseOnHover: true,
    draggable: true,
    draggablePercent: 0.6,
    showCloseButtonOnHover: false,
    hideProgressBar: true,
    closeButton: "button",
    icon: true,
    rtl: false,
    transition: "Vue-Toastification__bounce",
    maxToasts: 20,
    newestOnTop: true,
};
