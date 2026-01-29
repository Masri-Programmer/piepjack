import NProgress from "nprogress";
import { createRouter, createWebHistory } from "vue-router";
import "@assets/css/nprogress.css";
import { useStorage, useSessionStorage } from "@vueuse/core";
import { createApiResource } from "@lib/helpers";
import AppLayout from "./layouts/shop/AppLayout.vue";
import AppLayout2 from "./layouts/shop/AppLayout2.vue";
import AdminAppLayout from "./layouts/admin/AppLayout.vue";
import AdminLoginLayout from "./layouts/admin/Login.vue";
import AdminNotFoundLayout from "./layouts/admin/NotFound.vue";
import PublicNotFoundLayout from "./layouts/shop/NotFound.vue";
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
        path: "/admin/login",
        name: "admin.login",
        component: AdminLoginLayout,
        meta: { requiresGuest: true, title: "Admin Login" },
    },
    {
        path: "/admin",
        component: AdminAppLayout,
        meta: { requiresAuth: true },
        children: [
            {
                path: "",
                name: "admin.home",
                redirect: { name: "admin.dashboard" },
            },
            {
                path: "dashboard",
                name: "admin.dashboard",
                component: () => import("./pages/admin/Dashboard.vue"),
                meta: { title: "Dashboard" },
            },
            {
                path: "products",
                name: "admin.products.show",
                component: () =>
                    import("./pages/admin/products/ProductsShow.vue"),
                meta: { title: "Products" },
            },
            {
                path: "products/add",
                name: "admin.products.add",
                component: () =>
                    import("./pages/admin/products/StoreProduct.vue"),
                meta: { title: "Add Product" },
            },
            {
                path: "products/:id/:slug",
                name: "admin.products.view",
                component: () => import("./pages/admin/products/Product.vue"),
                meta: (route) => ({ title: `Product: ${route.params.slug}` }),
            },
            {
                path: "products/:id",
                name: "admin.products.redirect",
                redirect: async (to) => {
                    const { id } = to.params;
                    try {
                        const product =
                            await createApiResource("products").getById(id);
                        if (product && product.slug) {
                            return {
                                name: "admin.products.view",
                                params: { id, slug: product.slug },
                            };
                        }
                        console.warn(
                            `Admin: Product with ID ${id} not found or slug missing. Redirecting to admin notfound.`,
                        );
                        return { name: "admin.notfound" };
                    } catch (error) {
                        console.error(
                            `Admin: Error fetching product ID ${id} for redirect:`,
                            error,
                        );
                        return { name: "admin.notfound" };
                    }
                },
            },
            {
                path: "categories",
                name: "admin.categories",
                component: () =>
                    import("./pages/admin/categories/Categories.vue"),
                meta: { title: "Categories" },
            },
            {
                path: "categories/add",
                name: "admin.categories.add",
                component: () =>
                    import("./pages/admin/categories/StoreCategory.vue"),
                meta: { title: "Add Category" },
            },
            {
                path: "categories/:id/:slug",
                name: "admin.categories.view",
                component: () =>
                    import("./pages/admin/categories/Category.vue"),
                meta: (route) => ({ title: `Category: ${route.params.slug}` }),
            },
            {
                path: "categories/:id",
                name: "admin.categories.redirect",
                redirect: async (to) => {
                    const { id } = to.params;
                    try {
                        const category =
                            await createApiResource("categories").getById(id);
                        if (category && category.slug) {
                            return {
                                name: "admin.categories.view",
                                params: { id, slug: category.slug },
                            };
                        }
                        console.warn(
                            `Admin: Category with ID ${id} not found or slug missing. Redirecting to admin notfound.`,
                        );
                        return { name: "admin.notfound" };
                    } catch (error) {
                        console.error(
                            `Admin: Error fetching category ID ${id} for redirect:`,
                            error,
                        );
                        return { name: "admin.notfound" };
                    }
                },
            },
            {
                path: "orders",
                name: "admin.orders",
                component: () => import("./pages/admin/orders/Orders.vue"),
                meta: { title: "Orders" },
            },
            {
                path: "orders/:id",
                name: "admin.orders.view",
                component: () => import("./pages/admin/orders/Order.vue"),
                meta: (route) => ({ title: `Order: ${route.params.id}` }),
            },
            {
                path: "returns",
                name: "admin.returns",
                component: () => import("./pages/admin/returns/Returns.vue"),
                meta: { title: "Returns" },
            },
            {
                path: "returns/:id",
                name: "admin.returns.view",
                component: () => import("./pages/admin/returns/Returns.vue"),
                meta: (route) => ({ title: `Return: ${route.params.id}` }),
            },
            {
                path: "users",
                name: "admin.users",
                component: () => import("./pages/admin/users/Users.vue"),
                meta: { title: "Users" },
            },
            {
                path: "users/:id",
                name: "admin.users.view",
                component: () => import("./pages/admin/users/User.vue"),
                meta: (route) => ({ title: `User: ${route.params.id}` }),
            },
            {
                path: "404",
                name: "admin.notfound",
                component: AdminNotFoundLayout,
                meta: { title: "Admin - Not Found" },
            },
            {
                path: ":adminPathMatch(.*)*",
                redirect: { name: "admin.notfound" },
            },
        ],
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

    if (to.path.startsWith("/admin")) {
        setAppArea("admin");
    } else if (to.name !== "admin.login") {
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

    if (requiresAuth && !loggedIn) {
        if (to.name !== "admin.login") {
            next({ name: "admin.login" });
        } else {
            next();
        }
    } else if (requiresGuest && loggedIn) {
        next({ name: "admin.dashboard" });
    } else {
        next();
    }
});

router.afterEach(() => {
    NProgress.done();
});

export default router;
