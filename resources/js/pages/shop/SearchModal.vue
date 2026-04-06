<script setup>
import { ref, computed, watch } from "vue";
import { useI18n } from "vue-i18n";
import { useDebounceFn } from "@vueuse/core";
import { Search, X } from "lucide-vue-next";
import { RouterLink } from "vue-router";

// Components & Libs
import Modal from "@components/Modal.vue";
import ProductSmallCard from "@components/shop/product/ProductSmallCard.vue";
import { apiQuery } from "@lib/helpers";
import searchIllustration from "@img/svg/search.svg";

const props = defineProps({
    open: { type: Boolean, default: false },
});

const emit = defineEmits(["close"]);

const { t } = useI18n();

// Tabs State
const selectedTab = ref(0);
const computedTabs = computed(() => [
    t("common.search.tabs.0"), // e.g., "Products"
    t("common.search.tabs.1"), // e.g., "Categories"
]);

const selectTab = (index) => {
    selectedTab.value = index;
};

// Search Logic - Localized for "Full Search"
const localSearchTerm = ref("");
const debouncedSearchTerm = ref("");

const updateDebounce = useDebounceFn((value) => {
    debouncedSearchTerm.value = value;
}, 400);

watch(localSearchTerm, (newVal) => {
    updateDebounce(newVal);
});

// Dedicated search params for "Full Search" (unfiltered by current category)
const searchParams = computed(() => ({
    search: debouncedSearchTerm.value,
    per_page: 30, // Increased for full search experience
    active: 1,
}));

// API Queries - Using dedicated searchParams
const { data, isLoading } = apiQuery("products").useGet(searchParams);
const { data: categories } = apiQuery("categories").useGet(searchParams);

// Clear search when modal is closed
watch(
    () => props.open,
    (isOpen) => {
        if (!isOpen) {
            localSearchTerm.value = "";
            debouncedSearchTerm.value = "";
        }
    },
);
</script>

<template>
    <Modal :open="props.open" @close="emit('close')">
        <div class="flex flex-col w-full bg-background text-foreground">
            <div
                class="flex items-center justify-between p-6 border-b border-border"
            >
                <h1 class="text-xl font-bold uppercase tracking-tight m-0">
                    {{ t("common.search.title") }}
                </h1>
            </div>

            <div class="p-6 pb-0">
                <div class="relative flex items-center">
                    <Search
                        class="absolute right-4 w-5 h-5 text-muted-foreground pointer-events-none"
                    />
                    <input
                        v-model="localSearchTerm"
                        type="search"
                        :placeholder="t('common.search.placeholder')"
                        class="w-full bg-muted border-none p-4 pl-12 text-base focus:ring-2 focus:ring-ring outline-none rounded-none transition-all"
                        autofocus
                    />
                </div>
            </div>

            <div class="px-6 mt-6">
                <div class="flex space-x-8 border-b border-border">
                    <button
                        v-for="(tab, index) in computedTabs"
                        :key="index"
                        @click="selectTab(index)"
                        :class="[
                            'pb-3 text-sm font-bold uppercase tracking-wider transition-all rounded-none border-b-2',
                            selectedTab === index
                                ? 'border-primary text-primary'
                                : 'border-transparent text-muted-foreground hover:text-foreground',
                        ]"
                    >
                        {{ tab }}
                    </button>
                </div>
            </div>

            <div
                class="p-6 min-h-[400px] max-h-[60vh] overflow-y-auto custom-scrollbar"
            >
                <div v-if="selectedTab === 0" class="space-y-4">
                    <div
                        v-if="data?.data?.length"
                        class="grid grid-cols-1 gap-2"
                    >
                        <RouterLink
                            v-for="product in data.data"
                            :key="product.id"
                            :to="`/product/${product.id}/${product.slug}`"
                            @click="emit('close')"
                            class="group block border border-transparent hover:border-border transition-colors"
                        >
                            <ProductSmallCard
                                :product="product"
                                class="rounded-none"
                            />
                        </RouterLink>
                    </div>

                    <div
                        v-else-if="!isLoading"
                        class="flex flex-col items-center justify-center py-12 opacity-40"
                    >
                        <img
                            :src="searchIllustration"
                            class="w-32 h-32 mb-4 grayscale"
                            alt="No results"
                        />
                        <p class="text-sm uppercase tracking-widest">
                            {{ t("common.search.no_results") }}
                        </p>
                    </div>
                </div>

                <div v-if="selectedTab === 1">
                    <div
                        v-if="categories?.data?.length"
                        class="flex flex-col border-t border-l border-border"
                    >
                        <RouterLink
                            v-for="category in categories.data"
                            :key="category.id"
                            :to="`/collections/${category.id}/${category.slug}`"
                            @click="emit('close')"
                            class="p-4 border-b border-r border-border hover:bg-muted transition-colors font-medium uppercase text-sm"
                        >
                            {{ category.name }}
                        </RouterLink>
                    </div>

                    <div
                        v-else
                        class="flex flex-col items-center justify-center py-12 opacity-40"
                    >
                        <img
                            :src="searchIllustration"
                            class="w-32 h-32 mb-4 grayscale"
                            alt="No results"
                        />
                        <p class="text-sm uppercase tracking-widest">
                            {{ t("common.search.no_categories") }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </Modal>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: var(--accent_light);
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: var(--main);
}
</style>
