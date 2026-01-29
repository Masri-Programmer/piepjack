export const PRODUCTS_PER_PAGE = 10;
export const USERS_PER_PAGE = 10;
export const CUSTOMERS_PER_PAGE = 10;
export const SHOP_LINK = import.meta.env.VITE_APP_URL;
export const ADMIN_LINK = import.meta.env.VITE_ADMIN_URL;
export const SUPPORT_LOCALES = ["en", "de"];

const CURRENCY_KEY = "appCurrency";
const DEFAULT_CURRENCY = "EUR";

export const getCurrency = () => {
    return localStorage.getItem(CURRENCY_KEY) || DEFAULT_CURRENCY;
};

export const setCurrency = (currency) => {
    localStorage.setItem(CURRENCY_KEY, currency);
};
