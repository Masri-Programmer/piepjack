import NProgress from "nprogress";
import { createRouter, createWebHistory } from "vue-router";
import "@assets/css/nprogress.css";
import { useStorage, useSessionStorage } from "@vueuse/core";
import { createApiResource } from "@lib/helpers";
import AppLayout from "./layouts/shop/AppLayout.vue";
import AppLayout2 from "./layouts/shop/AppLayout2.vue";
import PublicNotFoundLayout from "./layouts/shop/NotFound.vue";
import LaunchCountdown from "./pages/shop/LaunchCountdown.vue";
import { useArea } from "@lib/useArea.js";
import { setAppArea } from "@lib/useArea";
const { updateArea } = useArea();
const BASE_TITLE = import.meta.env.VITE_APP_NAME || "Piepjack";

const isAuthenticated = () => {
    const userLocal = useStorage("user", {});
    const userSession = useSessionStorage("user", {});
    return !!(userLocal.value.token || userSession.value.token);
};

const routes = [
    {
        path: "/",
        component: AppLayout,
        children: [
            {
                path: "",
                name: "Home",
                component: () => import("./pages/shop/Home.vue"),
            },
            {
                path: "terms-of-service",
                name: "terms-of-service",
                component: () => import("./pages/shop/TermsOfService.vue"),
                meta: { title: "Terms of Service" },
            },
            {
                path: "impressum",
                name: "Impressum",
                component: () => import("./pages/shop/Impressum.vue"),
                meta: { title: "Impressum" },
            },
            {
                path: "widerrufsbelehrung",
                name: "Widerrufsbelehrung",
                component: () => import("./pages/shop/CancellationPolicy.vue"),
                meta: { title: "Widerrufsbelehrung" },
            },
            {
                path: "shipping",
                name: "Shipping",
                component: () => import("./pages/shop/Shipping.vue"),
                meta: { title: "Shipping" },
            },
            {
                path: "faq",
                name: "faq",
                component: () => import("./pages/shop/FAQ.vue"),
                meta: { title: "FAQ" },
            },
            {
                path: "track-order",
                name: "TrackOrder",
                component: () => import("./pages/shop/TrackOrder.vue"),
                meta: { title: "Track Your Order" },
            },
            {
                path: "track-order/:id",
                name: "TrackOrderId",
                component: () => import("./pages/shop/TrackOrder.vue"),
                meta: { title: "Track Your Order" },
            },
            {
                path: "about",
                name: "AboutUs",
                component: () => import("./pages/shop/AboutUs.vue"),
                meta: { title: "About Us" },
            },
            {
                path: "collections",
                meta: { title: "Collections" },
                children: [
                    {
                        path: "",
                        name: "Collections",
                        component: () => import("./pages/shop/Collections.vue"),
                    },
                    {
                        path: "shop-all",
                        name: "CollectionsShopAll",
                        component: () => import("./pages/shop/Collections.vue"),
                    },
                    {
                        path: ":id/:slug",
                        name: "CollectionProduct",
                        component: () => import("./pages/shop/Collections.vue"),
                    },
                ],
            },
            {
                path: "product/:id/:slug",
                name: "Product",
                component: () =>
                    import("./components/shop/product/Product.vue"),
                meta: { title: "Product" },
            },
            {
                path: "datenschutzerklarung",
                name: "Datenschutzerklärung",
                component: () =>
                    import("./pages/shop/Datenschutzerklarung.vue"),
                meta: { title: "Datenschutzerklärung" },
            },
            {
                path: "cart",
                name: "Cart",
                component: () => import("./components/shop/cart/CartPage.vue"),
                meta: { title: "Cart" },
            },
            {
                path: "off-the-radar",
                name: "OffTheRadar",
                component: () => import("./pages/shop/OffTheRadar.vue"),
                meta: { title: "Off The Radar" },
            },
            {
                path: "return-order",
                meta: { title: "Return Order" },
                children: [
                    {
                        path: "",
                        name: "ReturnOrder",
                        component: () =>
                            import("./components/shop/order/ReturnOrder.vue"),
                    },
                    {
                        path: ":slug",
                        name: "ReturnOrderSlug",
                        component: () =>
                            import("./components/shop/order/ReturnSuccess.vue"),
                    },
                ],
            },
            {
                path: "success",
                name: "Success",
                component: () =>
                    import("./components/shop/checkout/Success.vue"),
                meta: { title: "Success" },
            },
            {
                path: "category/:slug",
                name: "Category",
                component: () => import("./pages/shop/Collections.vue"),
                meta: (route) => ({ title: route.params.slug }),
            },
        ],
    },
    {
        path: "/checkout",
        component: AppLayout2,
        children: [
            {
                path: "",
                name: "Checkout",
                component: () =>
                    import("./components/shop/checkout/Checkout.vue"),
                meta: { title: "Checkout" },
            },
        ],
    },
    {
        path: "/launch",
        name: "Launch",
        component: LaunchCountdown,
        meta: { title: "Launching Soon" },
    },
    {
        path: "/:pathMatch(.*)*",
        name: "GlobalNotFound",
        component: PublicNotFoundLayout,
        meta: { title: "Page Not Found" },
    },
];

const router = createRouter({
    history: createWebHistory(new URL(import.meta.env.VITE_APP_URL).pathname),
    routes: routes,
    scrollBehavior(to, from, savedPosition) {
        return savedPosition || { top: 0, behavior: "smooth" };
    },
});

router.beforeEach((to, from, next) => {
    NProgress.start();

    if (to.name !== "admin.login") {
        setAppArea("shop");
    }

    const pageTitle = to.meta.title;
    const titleSuffix = BASE_TITLE;
    if (typeof pageTitle === "function") {
        document.title = `${pageTitle(to)} - ${titleSuffix}`;
    } else if (pageTitle) {
        document.title = `${pageTitle} - ${titleSuffix}`;
    } else {
        document.title = titleSuffix;
    }

    const requiresAuth = to.matched.some((record) => record.meta.requiresAuth);
    const requiresGuest = to.matched.some(
        (record) => record.meta.requiresGuest,
    );
    const loggedIn = isAuthenticated();

    const targetDate = new Date(
        import.meta.env.VITE_LAUNCH_DATE || "2026-05-01T18:00:00",
    ).getTime();

    const now = new Date().getTime();
    const isPastLaunch = now >= targetDate;
    const isLaunchEnabled = import.meta.env.VITE_LAUNCH_PAGE_ENABLED === "true";
    const isBypass = to.query.preview === "true";

    // Show launch page ONLY if it's enabled in ENV AND we haven't reached the date yet.
    // However, if we've passed the date, we never show it.
    const shouldShowLaunch = !isPastLaunch && isLaunchEnabled;

    if (
        shouldShowLaunch &&
        !isBypass &&
        !to.path.startsWith("/admin") &&
        to.name !== "Launch" &&
        to.name !== "admin.login"
    ) {
        return next({ name: "Launch" });
    }

    // If we shouldn't show the launch page (either passed the date or disabled in env),
    // and the user tries to visit /launch directly, redirect them home.
    if (!shouldShowLaunch && to.name === "Launch") {
        return next({ name: "Home" });
    }

    if (requiresAuth && !loggedIn) {
        if (to.name !== "admin.login") {
            next({ name: "admin.login" });
        } else {
            next();
        }
    } else {
        next();
    }
});

router.afterEach(() => {
    NProgress.done();
});

export default router;
