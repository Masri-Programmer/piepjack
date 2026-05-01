<script setup>
import { ref, computed, watch } from "vue";
import { useI18n } from "vue-i18n";
import { useDebounceFn } from "@vueuse/core";
import { Search } from "lucide-vue-next";
import { RouterLink } from "vue-router";

// Shadcn UI Components
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogDescription,
} from "@/components/ui/dialog";

// Components & Libs
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

// Search Logic
const localSearchTerm = ref("");
const debouncedSearchTerm = ref("");

const updateDebounce = useDebounceFn((value) => {
    debouncedSearchTerm.value = value;
}, 400);

watch(localSearchTerm, (newVal) => {
    updateDebounce(newVal);
});

// Dedicated search params
const searchParams = computed(() => ({
    search: debouncedSearchTerm.value,
    per_page: 30,
    active: 1,
}));

// API Queries
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

const handleOpenChange = (isOpen) => {
    if (!isOpen) {
        emit("close");
    }
};
</script>

<template>
    <Dialog :open="props.open" @update:open="handleOpenChange">
        <DialogContent
            class="rounded-none w-[95vw] sm:w-[600px] md:w-[800px] max-w-none h-[85vh] md:h-[75vh] flex flex-col p-0 gap-0 border-border bg-background shadow-xl outline-none"
        >
            <DialogHeader
                class="p-4 md:p-6 border-b border-border bg-background shrink-0"
            >
                <DialogTitle
                    class="text-xl font-bold uppercase tracking-tight text-foreground m-0"
                >
                    {{ t("common.search.title") }}
                </DialogTitle>
                <DialogDescription class="sr-only">
                    Search through our products and categories.
                </DialogDescription>
            </DialogHeader>

            <div
                class="relative flex items-center border-b border-border bg-background shrink-0 mx-4"
            >
                <Search
                    class="absolute right-6 w-5 h-5 text-muted-foreground pointer-events-none"
                />
                <input
                    v-model="localSearchTerm"
                    type="search"
                    :placeholder="t('common.search.placeholder')"
                    class="w-full bg-transparent p-4 md:p-6 pr-14 text-base focus:bg-accent-shadcn focus:outline-none transition-colors rounded-none placeholder:text-muted-foreground text-foreground"
                    autofocus
                />
            </div>

            <div
                class="px-4 md:px-6 border-b border-border bg-background shrink-0 pt-4"
            >
                <div class="flex space-x-8">
                    <button
                        v-for="(tab, index) in computedTabs"
                        :key="index"
                        @click="selectTab(index)"
                        :class="[
                            'pb-3 text-sm font-bold uppercase tracking-wider transition-all rounded-none border-b-2 outline-none',
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
                class="flex-1 overflow-y-auto p-4 md:p-6 custom-scrollbar bg-accent-shadcn/30"
            >
                <div v-if="selectedTab === 0" class="h-full">
                    <div
                        v-if="data?.data?.length"
                        class="grid grid-cols-1 sm:grid-cols-2 gap-3"
                    >
                        <RouterLink
                            v-for="product in data.data"
                            :key="product.id"
                            :to="`/product/${product.id}/${product.slug}`"
                            @click="emit('close')"
                            class="group block border border-transparent hover:border-border transition-colors bg-background"
                        >
                            <ProductSmallCard
                                :product="product"
                                :item="product"
                            />
                        </RouterLink>
                    </div>

                    <div
                        v-else-if="!isLoading"
                        class="flex flex-col items-center justify-center h-full opacity-40 min-h-[300px]"
                    >
                        <img
                            :src="searchIllustration"
                            class="w-24 md:w-32 h-24 md:h-32 mb-4 grayscale"
                            alt="No results"
                        />
                        <p
                            class="text-sm font-bold uppercase tracking-widest text-foreground"
                        >
                            {{ t("common.search.no_results") }}
                        </p>
                    </div>
                </div>

                <div v-if="selectedTab === 1" class="h-full">
                    <div
                        v-if="categories?.data?.length"
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3"
                    >
                        <RouterLink
                            v-for="category in categories.data"
                            :key="category.id"
                            :to="`/collections/${category.id}/${category.slug}`"
                            @click="emit('close')"
                            class="flex items-center p-4 border border-border bg-background hover:bg-muted transition-colors font-bold uppercase text-sm text-foreground rounded-none group"
                        >
                            <span
                                class="group-hover:translate-x-1 transition-transform"
                            >
                                {{ category.name }}
                            </span>
                        </RouterLink>
                    </div>

                    <div
                        v-else
                        class="flex flex-col items-center justify-center h-full opacity-40 min-h-[300px]"
                    >
                        <img
                            :src="searchIllustration"
                            class="w-24 md:w-32 h-24 md:h-32 mb-4 grayscale"
                            alt="No results"
                        />
                        <p
                            class="text-sm font-bold uppercase tracking-widest text-foreground"
                        >
                            {{ t("common.search.no_categories") }}
                        </p>
                    </div>
                </div>
            </div>
        </DialogContent>
    </Dialog>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
    width: 8px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: var(--border);
    border: 2px solid var(--background); /* Creates a padded look */
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: var(--muted-foreground);
}
</style>
