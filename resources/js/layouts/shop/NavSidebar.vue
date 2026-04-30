<template>
    <!-- Teleport ensures the fixed overlay escapes any potential z-index stacking context issues -->
    <Teleport to="body">
        <!-- Overlay Background -->
        <transition
            enter-active-class="transition-opacity ease-linear duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity ease-linear duration-300"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-show="nav"
                class="fixed inset-0 z-40 bg-foreground/50 backdrop-blur-sm"
                aria-hidden="true"
            ></div>
        </transition>

        <!-- Sidebar Content -->
        <transition
            enter-active-class="transition ease-in-out duration-300 transform"
            enter-from-class="-translate-x-full"
            enter-to-class="translate-x-0"
            leave-active-class="transition ease-in-out duration-300 transform"
            leave-from-class="translate-x-0"
            leave-to-class="-translate-x-full"
        >
            <aside
                v-show="nav"
                ref="NavSidebarRef"
                class="fixed inset-y-0 left-0 z-50 flex w-full max-w-sm flex-col bg-background text-foreground shadow-2xl border-r border-border"
                aria-label="Main Navigation"
            >
                <!-- Header -->
                <header
                    class="flex items-center justify-between border-b border-border p-4"
                >
                    <span
                        class="text-sm font-bold uppercase tracking-widest text-foreground"
                    >
                        {{ $t("common.menu.menu", "Menu") }}
                    </span>
                    <button
                        @click="closeNavigation"
                        class="rounded-none p-2 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        aria-label="Close Navigation"
                    >
                        <X class="h-6 w-6" stroke-width="1.5" />
                    </button>
                </header>

                <!-- Main Navigation Tabs -->
                <div class="relative flex-1 overflow-x-hidden overflow-y-auto">
                    <!-- Level 1: Main Menu -->
                    <transition
                        enter-active-class="transition-transform duration-300 ease-in-out"
                        enter-from-class="-translate-x-full absolute w-full"
                        enter-to-class="translate-x-0 relative"
                        leave-active-class="transition-transform duration-300 ease-in-out absolute w-full"
                        leave-from-class="translate-x-0"
                        leave-to-class="-translate-x-full"
                    >
                        <nav v-if="openTab === 1" class="flex flex-col w-full">
                            <ul
                                class="flex flex-col uppercase text-sm font-medium tracking-wide"
                            >
                                <li class="border-b border-border">
                                    <RouterLink
                                        to="/collections"
                                        @click="closeNavigation"
                                        class="flex w-full items-center px-6 py-5 transition-colors hover:bg-muted hover:text-foreground"
                                    >
                                        {{ $t("common.menu.shopAll") }}
                                    </RouterLink>
                                </li>
                                <li class="border-b border-border">
                                    <RouterLink
                                        to="/"
                                        @click="closeNavigation"
                                        class="flex w-full items-center px-6 py-5 transition-colors hover:bg-muted hover:text-foreground"
                                    >
                                        {{ $t("common.menu.home") }}
                                    </RouterLink>
                                </li>
                                <li class="border-b border-border">
                                    <button
                                        @click="openTab = 3"
                                        class="flex w-full items-center justify-between px-6 py-5 uppercase transition-colors hover:bg-muted hover:text-foreground rounded-none"
                                    >
                                        <span>{{
                                            $t("common.menu.collections")
                                        }}</span>
                                        <ChevronRight
                                            class="h-5 w-5 text-muted-foreground"
                                            stroke-width="1.5"
                                        />
                                    </button>
                                </li>
                                <li class="border-b border-border">
                                    <RouterLink
                                        to="/about"
                                        @click="closeNavigation"
                                        class="flex w-full items-center px-6 py-5 transition-colors hover:bg-muted hover:text-foreground"
                                    >
                                        {{ $t("common.menu.aboutUs") }}
                                    </RouterLink>
                                </li>
                            </ul>
                        </nav>
                    </transition>

                    <!-- Level 2: Collections Sub-menu -->
                    <transition
                        enter-active-class="transition-transform duration-300 ease-in-out"
                        enter-from-class="translate-x-full absolute w-full"
                        enter-to-class="translate-x-0 relative"
                        leave-active-class="transition-transform duration-300 ease-in-out absolute w-full"
                        leave-from-class="translate-x-0"
                        leave-to-class="translate-x-full"
                    >
                        <nav
                            v-if="openTab === 3"
                            class="flex flex-col w-full bg-background"
                        >
                            <button
                                @click="openTab = 1"
                                class="flex w-full items-center bg-muted/30 px-6 py-5 uppercase transition-colors hover:bg-muted border-b border-border rounded-none"
                            >
                                <ChevronLeft
                                    class="mr-3 h-5 w-5 text-muted-foreground"
                                    stroke-width="1.5"
                                />
                                <span
                                    class="text-sm font-medium tracking-wide"
                                    >{{ $t("common.menu.collections") }}</span
                                >
                            </button>

                            <ul class="flex flex-col">
                                <li
                                    v-for="(collection, index) in collections"
                                    :key="collection.id || index"
                                    class="border-b border-border"
                                >
                                    <RouterLink
                                        :to="`/collections/${collection.id}/${collection.slug}`"
                                        @click="closeNavigation"
                                        class="flex w-full items-center px-8 py-4 text-sm transition-colors hover:bg-muted hover:text-foreground"
                                    >
                                        {{ collection.name }}
                                    </RouterLink>
                                </li>
                            </ul>
                        </nav>
                    </transition>
                </div>

                <!-- Footer / Action Items -->
                <footer
                    class="mt-auto flex grid grid-cols-3 border-t border-border bg-background"
                >
                    <!-- Language Selector -->
                    <div
                        class="flex items-center justify-center border-r border-border hover:bg-muted transition-colors"
                    >
                        <LanguageDropdown
                            :open="isLangDropdownOpen"
                            :handle-close="closeLangDropdownOpen"
                            :handle-toggle="toggleLangDropdownOpen"
                            class="h-full w-full rounded-none flex items-center justify-center p-4 cursor-pointer"
                        />
                    </div>

                    <!-- User Account -->
                    <a
                        href="/lunar/login"
                        class="flex items-center justify-center border-r border-border p-4 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground rounded-none"
                        aria-label="User Account"
                    >
                        <ShieldUser class="h-5 w-5" stroke-width="1.5" />
                    </a>

                    <!-- Theme Toggle -->
                    <button
                        @click="toggleTheme"
                        class="flex items-center justify-center p-4 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground rounded-none focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        aria-label="Toggle Theme"
                    >
                        <Sun v-if="isDark" class="h-5 w-5" stroke-width="1.5" />
                        <Moon v-else class="h-5 w-5" stroke-width="1.5" />
                    </button>
                </footer>
            </aside>
        </transition>
    </Teleport>
</template>

<script setup>
import { ref, watch } from "vue";
import { onClickOutside, useDark, useToggle } from "@vueuse/core";
import {
    X,
    ChevronLeft,
    ChevronRight,
    ShieldUser,
    Sun,
    Moon,
} from "lucide-vue-next";
import LanguageDropdown from "@components/LanguageDropdown.vue"; // Adjust alias if needed

const props = defineProps({
    nav: {
        type: Boolean,
        required: true,
    },
    shopAllCategories: {
        type: Array,
        default: () => [],
    },
    collections: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(["closeNav"]);

// --- State ---
const NavSidebarRef = ref(null);
const openTab = ref(1);
const isLangDropdownOpen = ref(false);

// Reset navigation state whenever it's closed
watch(
    () => props.nav,
    (isOpen) => {
        if (!isOpen) {
            setTimeout(() => {
                openTab.value = 1;
            }, 300); // Wait for transition before resetting
        }
    },
);

// --- Actions ---
const closeNavigation = () => {
    emit("closeNav");
};

onClickOutside(NavSidebarRef, () => {
    if (props.nav) {
        closeNavigation();
    }
});

// --- Theme Management ---
const isDark = useDark({
    selector: "html",
    attribute: "class",
    valueDark: "dark",
    valueLight: "",
});
const toggleTheme = useToggle(isDark);

// --- Language Dropdown Handlers ---
function toggleLangDropdownOpen() {
    isLangDropdownOpen.value = !isLangDropdownOpen.value;
}

function closeLangDropdownOpen() {
    isLangDropdownOpen.value = false;
}
</script>
