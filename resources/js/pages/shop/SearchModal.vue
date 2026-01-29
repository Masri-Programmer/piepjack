<template>
    <Modal :open="props.open" @close="$emit('close')">
        <h1 class="search-modal-title">
            {{ $t("common.search.title") }}
        </h1>
        <div class="search-modal-container">
            <div class="search-modal-input-wrapper">
                <div class="search-modal-input-container">
                    <div class="search-modal-input-box">
                        <input
                            name="search"
                            type="search"
                            class="search-modal-input"
                            :placeholder="$t('common.search.placeholder')"
                            v-model="searchTerm"
                        />
                        <svg
                            class="search-modal-icon"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                            />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="search-modal-tabs">
                <ul class="search-modal-tabs-list">
                    <li
                        v-for="(tab, index) in computedTabs"
                        :key="index"
                        class="search-modal-tab"
                    >
                        <a
                            href="#"
                            @click.prevent="selectTab(index)"
                            :class="[
                                'search-modal-tab-link',
                                selectedTab === index
                                    ? 'search-modal-tab-active'
                                    : '',
                            ]"
                        >
                            {{ tab }}
                        </a>
                    </li>
                </ul>
                <div class="search-modal-content">
                    <div v-if="selectedTab === 0">
                        <div v-if="data?.data.length">
                            <template v-for="product in data.data">
                                <router-link
                                    @click="$emit('close')"
                                    :to="
                                        '/product/' +
                                        product.id +
                                        '/' +
                                        product.slug
                                    "
                                    v-if="product"
                                >
                                    <ProductSmallCard
                                        :product="product"
                                        :item="product"
                                    />
                                </router-link>
                            </template>
                        </div>
                        <img
                            v-else
                            :src="searchIllustration"
                            alt="search_Illustration"
                            class="search-modal-image"
                            loading="lazy"
                        />
                    </div>
                    <div v-if="selectedTab === 1">
                        <div
                            v-if="categories?.data.length"
                            class="search-modal-category-list"
                        >
                            <template v-for="category in categories.data">
                                <router-link
                                    :to="
                                        '/collections/' +
                                        category.id +
                                        '/' +
                                        category.slug
                                    "
                                    class="search-modal-category-link"
                                >
                                    {{ category.name }}
                                </router-link>
                            </template>
                        </div>
                        <img
                            v-else
                            :src="searchIllustration"
                            alt="search_Illustration"
                            class="search-modal-image"
                            loading="lazy"
                        />
                    </div>
                </div>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import { useI18n } from "vue-i18n";
import { ref, computed } from "vue";
import "@assets/css/pages/searchModal.css";
import { useDebounceFn } from "@vueuse/core";
import Modal from "@components/Modal.vue";
import { apiQuery } from "@lib/helpers";
import searchIllustration from "@img/svg/search.svg";
import ProductSmallCard from "@components/shop/product/ProductSmallCard.vue";
import { productParams } from "@lib/store/shop/index.js";

const props = defineProps({
    open: { type: Boolean },
});

const { t } = useI18n();
const computedTabs = computed(() => [
    t("common.search.tabs.0"),
    t("common.search.tabs.1"),
]);
const selectedTab = ref(0);

const selectTab = (index) => {
    selectedTab.value = index;
};
const searchTerm = computed({
    get: () => productParams.value.search,
    set: (value) => {
        productParams.value.search = value;
        debouncedRefetch();
    },
});

const { data, error, isError, isLoading, isPreviousData, isFetching, refetch } =
    apiQuery("products").useGet(productParams);

const {
    data: categories,
    error: errorCategories,
    isLoading: isLoadingCategories,
} = apiQuery("categories").useGet(productParams);

const debouncedRefetch = useDebounceFn(() => {
    refetch();
}, 500);
</script>
