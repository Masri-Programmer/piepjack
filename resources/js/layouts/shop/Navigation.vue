<template>
    <header
        ref="navigation"
        class="border-b bg-accent_dark border-gray relative"
    >
        <!-- <TopBanner /> -->
        <div class="navigation-bar">
            <Button
                variant="ghost"
                size="icon"
                class="menu-button p-0 h-auto w-auto"
                @click="toggleNav"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="menu-icon h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M4 6h16M4 12h16M4 18h16"
                    />
                </svg>
            </Button>
            <div>
                <router-link to="/" class="logo-link">
                    <img :src="logoCircle" alt="Logo" class="logo-img" />
                </router-link>
            </div>
            <div class="nav-links" @mouseleave="closeDropdown">
                <router-link to="/" class="nav-item border-animate">
                    <span @mouseleave="closeDropdown">{{
                        $t("common.menu.home")
                    }}</span>
                </router-link>
                <router-link to="/collections" class="nav-item border-animate">
                    <span>{{ $t("common.menu.shopAll") }}</span>
                </router-link>
                <div
                    class="nav-item border-animate"
                    @mouseover="openDropdown('COLLECTIONS')"
                >
                    <router-link to="/collections" @mouseover="closeDropdown">
                        {{ $t("common.menu.collections") }}
                    </router-link>
                </div>
                <router-link to="/about" class="nav-item border-animate">
                    <span @mouseleave="closeDropdown">{{
                        $t("common.menu.aboutUs")
                    }}</span>
                </router-link>
            </div>

            <nav class="flex items-center text-xs">
                <ul class="flex items-center space-x-1 sm:space-x-2">
                    <li class="flex">
                        <LanguageDropdown
                            :open="isLangDropdownOpen"
                            :handle:close="closeLangDropdownOpen"
                            :handle:toggle="toggleLangDropdownOpen"
                            class="cursor-pointer hover:bg-main hover:text-accent transition-colors p-2 rounded-none"
                        />
                    </li>

                    <li class="flex">
                        <a
                            href="/lunar/login"
                            class="p-2 text-foreground hover:bg-main hover:text-accent transition-colors rounded-none flex items-center justify-center"
                            aria-label="User Account"
                        >
                            <ShieldUser class="w-5 h-5" stroke-width="1.5" />
                        </a>
                    </li>

                    <li class="flex items-center">
                        <button
                            @click="toggleTheme"
                            class="p-2 text-foreground hover:bg-main hover:text-accent transition-colors rounded-none outline-none focus-visible:ring-2 focus-visible:ring-main flex items-center justify-center"
                            aria-label="Toggle Theme"
                        >
                            <Sun
                                v-if="isDark"
                                class="w-5 h-5"
                                stroke-width="1.5"
                            />
                            <Moon v-else class="w-5 h-5" stroke-width="1.5" />
                        </button>
                    </li>

                    <li class="flex items-center">
                        <button
                            @click="toggleModal"
                            class="p-2 text-foreground hover:bg-main hover:text-accent transition-colors rounded-none outline-none focus-visible:ring-2 focus-visible:ring-main flex items-center justify-center"
                            aria-label="Open Search"
                        >
                            <Search class="w-5 h-5" stroke-width="1.5" />
                        </button>
                    </li>

                    <li class="flex items-center">
                        <button
                            @click="toggleCart"
                            class="relative p-2 text-foreground hover:bg-main hover:text-accent transition-colors rounded-none outline-none focus-visible:ring-2 focus-visible:ring-main flex items-center justify-center"
                            aria-label="Open Cart"
                        >
                            <ShoppingBag class="w-5 h-5" stroke-width="1.5" />

                            <span
                                v-if="cartTotalQuantity > 0"
                                class="absolute top-0 right-0 translate-x-1/4 -translate-y-1/4 bg-main text-accent text-[10px] font-bold px-1.5 py-0.5 border-2 border-background rounded-none"
                            >
                                {{ cartTotalQuantity }}
                            </span>
                        </button>
                    </li>
                </ul>
            </nav>
        </div>

        <transition name="fade">
            <div
                v-if="activeDropdown === 'COLLECTIONS' && data?.data"
                class="dropdown-menu border-b"
                @mouseleave="closeDropdown"
                @mouseover="openDropdown('COLLECTIONS')"
            >
                <transition-group
                    name="fade-up"
                    tag="div"
                    class="dropdown-content"
                >
                    <template
                        v-for="(collection, index) in data.data"
                        :key="index"
                    >
                        <router-link
                            :to="
                                '/collections/' +
                                collection.id +
                                '/' +
                                collection.slug
                            "
                            class="dropdown-link"
                        >
                            {{ collection.name }}
                        </router-link>
                    </template>
                </transition-group>
            </div>
        </transition>
    </header>
    <SearchModal :open="isModalOpen" @close="closeModal" />
    <Cart @close="toggleCart" />
    <NavSidebar
        :nav="isNavOpen"
        @closeNav="toggleNav"
        :shopAllCategories="data?.data"
        :collections="data?.data"
    />
</template>

<script setup>
import { ref } from "vue";
import "@assets/css/navigation.css";
import { useDark, useToggle } from "@vueuse/core";
import { Button } from "@components/ui/button";
import NavSidebar from "./NavSidebar.vue";
import logoCircle from "@img/logo-new.png";
import { apiQuery } from "@lib/helpers";
import Cart from "@components/shop/cart/Cart.vue";
import TopBanner from "@layouts/shop/TopBanner.vue";
import SearchModal from "@pages/shop/SearchModal.vue";
import { cartState, cartTotalQuantity } from "@lib/store/shop/index.js";
import LanguageDropdown from "@components/LanguageDropdown.vue";
import { ShieldUser, Sun, Moon, Search, ShoppingBag } from "lucide-vue-next";

const isDark = useDark({
    selector: "html",
    attribute: "class",
    valueDark: "dark",
    valueLight: "",
});

const toggleTheme = () => {
    console.log("[Navigation] Toggling theme. Current isDark:", isDark.value);
    isDark.value = !isDark.value;
    console.log("[Navigation] New isDark:", isDark.value);
};

const { data, error, isLoading } = apiQuery("categories").useGet({});

const activeDropdown = ref(null);

const openDropdown = (dropdownName) => {
    activeDropdown.value = dropdownName;
};

const closeDropdown = () => {
    activeDropdown.value = null;
};

const isModalOpen = ref(false);
const isLangDropdownOpen = ref(false);

function toggleLangDropdownOpen() {
    isLangDropdownOpen.value = !isLangDropdownOpen.value;
}
function closeLangDropdownOpen() {
    isLangDropdownOpen.value = false;
}
function toggleModal() {
    isModalOpen.value = !isModalOpen.value;
}
function closeModal() {
    isModalOpen.value = false;
}

function toggleCart() {
    cartState.value.open = !cartState.value.open;
}
const isNavOpen = ref(false);

function toggleNav() {
    isNavOpen.value = !isNavOpen.value;
}
</script>
