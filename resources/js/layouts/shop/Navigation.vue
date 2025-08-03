<template>
    <header ref="navigation" class="border-b bg-accent_dark border-gray">
        <TopBanner />
        <div class="navigation-bar">
            <button class="menu-button" @click="toggleNav">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="menu-icon"
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
            </button>
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

            <nav class="nav-icons">
                <ul class="icon-list">
                    <li @click="toggleLangDropdownOpen" class="flex">
                        <LanguageDropdown
                            :open="isLangDropdownOpen"
                            :handle:close="closeLangDropdownOpen"
                            :handle:toggle="toggleLangDropdownOpen"
                        />
                    </li>
                    <li class="flex">
                        <router-link to="/admin"
                            ><ShieldUser size="24" strokeWidth="1" />
                        </router-link>
                    </li>
                    <li>
                        <Search
                            size="24"
                            strokeWidth="1"
                            @click="toggleModal"
                            class="search-icon"
                        />
                    </li>
                    <div class="cart-icon" @click="toggleCart">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="cart-img"
                            stroke-width="1"
                            viewBox="0 0 16.933 16.933"
                        >
                            <path
                                d="M8.474 0C6.95 0 5.699 1.166 5.579 2.646h-.922c-.839 0-1.454.65-1.582 1.533L1.464 15.324c-.127.877.69 1.61 1.584 1.61h10.84c.9 0 1.708-.734 1.582-1.61L13.86 4.179c-.123-.851-.719-1.533-1.582-1.533h-.91A2.896 2.896 0 0 0 8.473 0Zm0 .529c1.238 0 2.25.927 2.366 2.117H6.108A2.36 2.36 0 0 1 8.474.53zM4.657 3.176h7.621c.633 0 .982.542 1.059 1.078l1.61 11.146c.08.554-.493 1.004-1.06 1.004H3.047c-.565 0-1.138-.447-1.058-1.004L3.6 4.254c.084-.588.46-1.078 1.058-1.078Zm6.195 2.115c0 1.299-1.06 2.35-2.378 2.35S6.096 6.59 6.096 5.29c0-.352-.529-.354-.529 0 0 1.588 1.306 2.879 2.907 2.879 1.6 0 2.906-1.291 2.906-2.88 0-.351-.528-.351-.528 0z"
                                fill="var(--main)"
                            ></path>
                        </svg>
                        <span
                            v-if="cartTotalQuantity() > 0"
                            class="cart-badge"
                            >{{ cartTotalQuantity() }}</span
                        >
                    </div>
                </ul>
            </nav>
        </div>

        <transition name="fade">
            <div
                v-if="activeDropdown === 'COLLECTIONS' && data?.data"
                class="dropdown-menu"
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
import { Search, ShieldUser } from "lucide-vue-next";
import NavSidebar from "./NavSidebar.vue";
import logoCircle from "@img/logo-new.png";
import { apiQuery } from "@lib/helpers";
import Cart from "@components/shop/cart/Cart.vue";
import TopBanner from "@layouts/shop/TopBanner.vue";
import SearchModal from "@pages/shop/SearchModal.vue";
import { cartState, cartTotalQuantity } from "@lib/store/shop/index.js";
import LanguageDropdown from "@components/LanguageDropdown.vue";

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
