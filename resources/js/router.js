import NProgress from "nprogress";
import { createRouter, createWebHistory } from "vue-router";
import "@assets/css/nprogress.css";
import { useStorage, useSessionStorage } from '@vueuse/core';
import { createApiResource } from '@lib/helpers';

import AppLayout from "./Layouts/AppLayout.vue";
import AppLayout2 from "./Layouts/AppLayout2.vue";
import AdminAppLayout from "./Layouts/admin/AppLayout.vue";
import AdminLoginLayout from "./Layouts/admin/Login.vue";
import AdminNotFoundLayout from "./Layouts/admin/NotFound.vue";
import PublicNotFoundLayout from "./Layouts/NotFound.vue";

const BASE_TITLE = "PIEPJACK CLOTHING";

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
                component: () => import("./Pages/Home.vue"),
            },
            {
                path: "terms-of-service",
                name: "terms-of-service",
                component: () => import("./Pages/TermsOfService.vue"),
                meta: { title: "Terms of Service" },
            },
            {
                path: "impressum",
                name: "Impressum",
                component: () => import("./Pages/Impressum.vue"),
                meta: { title: "Impressum" },
            },
            {
                path: "shipping",
                name: "Shipping",
                component: () => import("./Pages/Shipping.vue"),
                meta: { title: "Shipping" },
            },
            {
                path: "faq",
                name: "faq",
                component: () => import("./Pages/FAQ.vue"),
                meta: { title: "FAQ" },
            },
            {
                path: "track-order",
                name: "TrackOrder",
                component: () => import("./Pages/TrackOrder.vue"),
                meta: { title: "Track Your Order" },
            },
            {
                path: "track-order/:id",
                name: "TrackOrderId",
                component: () => import("./Pages/TrackOrder.vue"),
                meta: { title: "Track Your Order" },
            },
            {
                path: "about",
                name: "AboutUs",
                component: () => import("./Pages/AboutUs.vue"),
                meta: { title: "About Us" },
            },
            {
                path: "collections",
                meta: { title: "Collections" },
                children: [
                    {
                        path: "",
                        name: "Collections",
                        component: () => import("./Pages/Collections.vue"),
                    },
                    {
                        path: "shop-all",
                        name: "CollectionsShopAll",
                        component: () => import("./Pages/Collections.vue"),
                    },
                    {
                        path: ":id/:slug",
                        name: "CollectionProduct",
                        component: () => import("./Pages/Collections.vue"),
                    },
                ],
            },
            {
                path: "product/:id/:slug",
                name: "Product",
                component: () => import("./Components/product/Product.vue"),
                meta: { title: "Product" },
            },
            {
                path: "datenschutzerklarung",
                name: "Datenschutzerklärung",
                component: () => import("./Pages/Datenschutzerklarung.vue"),
                meta: { title: "Datenschutzerklärung" },
            },
            {
                path: "cart",
                name: "Cart",
                component: () => import("./Components/cart/CartPage.vue"),
                meta: { title: "Cart" },
            },
            {
                path: "off-the-radar",
                name: "OffTheRadar",
                component: () => import("./Pages/OffTheRadar.vue"),
                meta: { title: "Off The Radar" },
            },
            {
                path: "return-order",
                meta: { title: "Return Order" },
                children: [
                    {
                        path: "",
                        name: "ReturnOrder",
                        component: () => import("./Components/order/ReturnOrder.vue"),
                    },
                    {
                        path: ":slug",
                        name: "ReturnOrderSlug",
                        component: () => import("./Components/order/ReturnSuccess.vue"),
                    },
                ],
            },
            {
                path: "success",
                name: "Success",
                component: () => import("./Components/checkout/Success.vue"),
                meta: { title: "Success" },
            },
            {
                path: "category/:slug",
                name: "Category",
                component: () => import("./Pages/Collections.vue"),
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
                component: () => import("./Components/checkout/Checkout.vue"),
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
                component: () => import("./Pages/admin/Dashboard.vue"),
                meta: { title: "Dashboard" },
            },
            {
                path: "products",
                name: "admin.products.show",
                component: () => import("./Pages/admin/products/ProductsShow.vue"),
                meta: { title: "Products" },
            },
            {
                path: "products/add",
                name: "admin.products.add",
                component: () => import("./Pages/admin/products/StoreProduct.vue"),
                meta: { title: "Add Product" },
            },
            {
                path: "products/:id/:slug",
                name: "admin.products.view",
                component: () => import("./Pages/admin/products/Product.vue"),
                meta: (route) => ({ title: `Product: ${route.params.slug}` }),
            },
            {
                path: "products/:id",
                name: "admin.products.redirect",
                redirect: async (to) => {
                    const { id } = to.params;
                    try {
                        const product = await createApiResource("products").getById(id);
                        if (product && product.slug) {
                            return { name: "admin.products.view", params: { id, slug: product.slug } };
                        }
                        console.warn(`Admin: Product with ID ${id} not found or slug missing. Redirecting to admin notfound.`);
                        return { name: "admin.notfound" };
                    } catch (error) {
                        console.error(`Admin: Error fetching product ID ${id} for redirect:`, error);
                        return { name: "admin.notfound" };
                    }
                },
            },
            {
                path: "categories",
                name: "admin.categories",
                component: () => import("./Pages/admin/categories/Categories.vue"),
                meta: { title: "Categories" },
            },
            {
                path: "categories/add",
                name: "admin.categories.add",
                component: () => import("./Pages/admin/categories/StoreCategory.vue"),
                meta: { title: "Add Category" },
            },
            {
                path: "categories/:id/:slug",
                name: "admin.categories.view",
                component: () => import("./Pages/admin/categories/Category.vue"),
                meta: (route) => ({ title: `Category: ${route.params.slug}` }),
            },
            {
                path: "categories/:id",
                name: "admin.categories.redirect",
                redirect: async (to) => {
                    const { id } = to.params;
                    try {
                        const category = await createApiResource("categories").getById(id);
                        if (category && category.slug) {
                            return { name: "admin.categories.view", params: { id, slug: category.slug } };
                        }
                        console.warn(`Admin: Category with ID ${id} not found or slug missing. Redirecting to admin notfound.`);
                        return { name: "admin.notfound" };
                    } catch (error) {
                        console.error(`Admin: Error fetching category ID ${id} for redirect:`, error);
                        return { name: "admin.notfound" };
                    }
                },
            },
            {
                path: "orders",
                name: "admin.orders",
                component: () => import("./Pages/admin/orders/Orders.vue"),
                meta: { title: "Orders" },
            },
            {
                path: "orders/:id",
                name: "admin.orders.view",
                component: () => import("./Pages/admin/orders/Order.vue"),
                meta: (route) => ({ title: `Order: ${route.params.id}` }),
            },
            {
                path: "returns",
                name: "admin.returns",
                component: () => import("./Pages/admin/returns/Returns.vue"),
                meta: { title: "Returns" },
            },
            {
                path: "returns/:id",
                name: "admin.returns.view",
                component: () => import("./Pages/admin/returns/Returns.vue"),
                meta: (route) => ({ title: `Return: ${route.params.id}` }),
            },
            {
                path: "users",
                name: "admin.users",
                component: () => import("./Pages/admin/users/Users.vue"),
                meta: { title: "Users" },
            },
            {
                path: "users/:id",
                name: "admin.users.view",
                component: () => import("./Pages/admin/users/User.vue"),
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
                redirect: { name: "admin.notfound" }
            }
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
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: routes,
    scrollBehavior(to, from, savedPosition) {
        return savedPosition || { top: 0, behavior: 'smooth' };
    },
});

router.beforeEach((to, from, next) => {
    NProgress.start();

    if (to.path.startsWith('/admin')) {
        localStorage.setItem('area', 'admin');
    } else if (to.name !== 'admin.login') {
        localStorage.setItem('area', 'shop');
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

    const requiresAuth = to.matched.some(record => record.meta.requiresAuth);
    const requiresGuest = to.matched.some(record => record.meta.requiresGuest);
    const loggedIn = isAuthenticated();

    if (requiresAuth && !loggedIn) {
        if (to.name !== 'admin.login') {
            next({ name: 'admin.login', });
        } else {
            next();
        }
    } else if (requiresGuest && loggedIn) {
        next({ name: 'admin.dashboard' });
    } else {
        next();
    }
});

router.afterEach(() => {
    NProgress.done();
});

export default router;