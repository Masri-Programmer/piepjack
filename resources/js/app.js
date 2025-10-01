import "./bootstrap";

import { createApp } from "vue";
import App from "./App.vue";
import "@assets/css/app.css";
import router from "./router";

import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";

import { VueQueryPlugin, QueryClient } from "@tanstack/vue-query";

import { getCurrency, setCurrency } from "@lib/constants";
import { i18n, createQueryClient, toastOptions } from "@lib/config";

const appName = import.meta.env.APP_NAME || "Laravel";
const queryClient = createQueryClient(QueryClient);

const app = createApp(App);

app.use(router);
app.use(Toast, toastOptions);
app.use(i18n);
app.use(VueQueryPlugin, { queryClient });

app.config.globalProperties.$currency = getCurrency();
app.config.globalProperties.$setCurrency = (currency) => {
    setCurrency(currency);
    app.config.globalProperties.$currency = currency;
};
app.config.globalProperties.$appName = appName;

app.mount("#app");
