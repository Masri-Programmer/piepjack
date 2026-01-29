<template>
    <PageLayout
        :data="links"
        :title="$t('admin.categories.title')"
        @button:click="toggle(!value)"
        :button="{ title: $t('admin.categories.addNew'), click: true }"
    >
        <div
            class="flex justify-between items-stretch flex-wrap min-h-[70px] pb-6 text-accent"
        >
            <h3
                class="flex flex-col items-start justify-center m-2 ml-0 font-medium text-xl/tight text-dark"
            >
                <span class="mr-3 font-semibold text-dark">{{
                    $t("admin.categories.allCategories", {
                        count: data?.meta.total ?? 0,
                    })
                }}</span>

                <span class="mt-1 font-medium text-gray text-lg/normal">{{
                    $t("admin.categories.subtitle")
                }}</span>
            </h3>
            <SearchField v-model="searchTerm" />
        </div>

        <div class="flex-auto block text-accent">
            <div class="overflow-x-auto">
                <table
                    class="w-full my-0 align-middle text-dark border-neutral-200"
                >
                    <thead class="align-bottom">
                        <tr class="font-semibold text-[0.95rem] text-gray">
                            <th class="pb-3 px-1 text-start uppercase">
                                {{ $t("admin.categories.table.id") }}
                            </th>

                            <th
                                class="pb-3 px-1 text-start min-w-[175px] uppercase"
                            >
                                {{ $t("admin.categories.table.name") }}
                            </th>

                            <th class="pb-3 px-1 text-start uppercase">
                                {{ $t("admin.categories.table.published") }}
                            </th>

                            <th
                                class="pb-3 px-1 text-start min-w-[50px] uppercase"
                            >
                                {{ $t("admin.categories.table.promoted") }}
                            </th>

                            <th
                                class="pb-3 px-1 text-start min-w-[50px] uppercase"
                            >
                                {{ $t("admin.categories.table.relatedTo") }}
                            </th>

                            <th
                                class="pb-3 px-1 text-start min-w-[50px] uppercase"
                            >
                                {{ $t("admin.categories.table.details") }}
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr
                            v-if="!data?.data.length && !isLoading && !isError"
                            class="font-bold text-xl my-6"
                        >
                            {{
                                $t("admin.categories.notFound")
                            }}
                        </tr>

                        <ProductsShowSkeleton v-if="isLoading" />

                        <tr
                            class="border-b border-neutral-200 ont-bold text-xl my-6"
                            v-if="error"
                        >
                            {{
                                error
                            }}
                        </tr>

                        <template v-if="!isLoading && data?.data">
                            <CategoryRow
                                v-if="value"
                                @success:store="toggle(false)"
                            />

                            <tr
                                class="border-b border-dashed last:border-b-0 border-main"
                                v-for="category in data?.data"
                            >
                                <td>{{ category.id }}</td>

                                <td class="p-3">
                                    <div class="flex items-center">
                                        <div
                                            class="flex flex-col justify-start"
                                        >
                                            <span
                                                class="mb-1 font-semibold transition-colors duration-200 ease-in-out text-lg/normal text-secondary-inverse hover:text-blue-500"
                                            >
                                                <input
                                                    v-model="category.name"
                                                    type="text"
                                                    id="name"
                                                    @change="
                                                        (e) =>
                                                            handleUpdate(
                                                                category,
                                                                'name',
                                                                e.target.value
                                                            )
                                                    "
                                                    class="mt-1 block w-full p-2 border border-gray rounded-md focus:outline-none focus:ring focus:ring-accent_dark"
                                                />
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td class="p-3 text-start">
                                    <ToggleSwitch
                                        v-model="category.active"
                                        @update:toggle="
                                            (boolean) =>
                                                handleUpdate(
                                                    category,
                                                    'active',
                                                    boolean
                                                )
                                        "
                                    />
                                </td>

                                <td class="pr-0 text-start">
                                    <ToggleSwitch
                                        v-model="category.promoted"
                                        @update:toggle="
                                            (boolean) =>
                                                handleUpdate(
                                                    category,
                                                    'promoted',
                                                    boolean
                                                )
                                        "
                                    />
                                </td>

                                <td class="pr-0 text-start">
                                    <div
                                        class="text-center align-baseline inline-flex px-4 py-3 mr-auto items-center font-semibold text-[.95rem] leading-none rounded-lg w-full"
                                    >
                                        <select
                                            :value="null"
                                            id="category_id"
                                            @change="
                                                (event) =>
                                                    handleUpdate(
                                                        category,
                                                        'parent_id',
                                                        event.target.value
                                                    )
                                            "
                                            class="shadow-md border border-gray text-accent_dark sm:text-sm rounded-lg focus:ring-accent_dark focus:border-accent_dark block w-full p-2.5"
                                        >
                                            <option value="" disabled>
                                                {{
                                                    $t(
                                                        "admin.categories.selectCategory"
                                                    )
                                                }}
                                            </option>

                                            <option
                                                v-for="category in data?.data"
                                                :key="category.id"
                                                :value="category.id"
                                            >
                                                {{ category.name }}
                                            </option>
                                        </select>
                                    </div>
                                </td>

                                <td class="p-3 text-start">
                                    <div class="flex items-center gap-2">
                                        <button
                                            :disabled="deleteLoading"
                                            class="ml-auto relative text-main bg-accent hover:text-white hover:bg-red-500 flex items-center h-[30px] w-[30px] text-base font-medium leading-normal text-center align-middle cursor-pointer rounded-2xl transition-colors duration-200 ease-in-out shadow-none border-0 justify-center"
                                            @click="
                                                () => handleDelete(category.id)
                                            "
                                        >
                                            <Trash2
                                                size="16"
                                                stroke-width="1.5"
                                            />
                                        </button>

                                        <button>
                                            <router-link
                                                class="ml-auto relative text-main bg-accent hover:text-white hover:bg-blue-500 flex items-center h-[30px] w-[30px] text-base font-medium leading-normal text-center align-middle cursor-pointer rounded-2xl transition-colors duration-200 ease-in-out shadow-none border-0 justify-center"
                                                :to="`/admin/categories/${category.id}/${category.slug}`"
                                            >
                                                <span
                                                    class="flex items-center justify-center p-0 m-0 leading-none shrink-0"
                                                >
                                                    <ChevronRight
                                                        size="16"
                                                        stroke-width="1.5"
                                                    />
                                                </span>
                                            </router-link>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                <Pagination
                    :currentPage="page"
                    :totalPages="data?.meta.last_page ?? 1"
                    :isFetching="isLoading"
                    @pageChanged="handlePageChange"
                />
            </div>
        </div>
    </PageLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import { useI18n } from "vue-i18n";
import { categoriesParams } from "@lib/store/admin";
import { useToggle } from "@vueuse/core";
import { apiQuery } from "@lib/helpers";
import CategoryRow from "./CategoryRow.vue";
import { useDebounceFn } from "@vueuse/core";
import PageLayout from "@layouts/admin/PageLayout.vue";
import Pagination from "@layouts/admin/Pagination.vue";
import { Trash2, ChevronRight } from "lucide-vue-next";
import SearchField from "@components/admin/SearchField.vue";
import ToggleSwitch from "@components/admin/ToggleSwitch.vue";
import ProductsShowSkeleton from "../products/ProductsShowSkeleton.vue";

const { t } = useI18n();

const searchTerm = computed({
    get: () => categoriesParams.value.search,
    set: (value) => {
        categoriesParams.value.search = value;
        debouncedRefetch();
    },
});
const page = computed({
    get: () => categoriesParams.value.page,
    set: (value) => {
        categoriesParams.value.page = value;
        debouncedRefetch();
    },
});

const { data, error, isLoading, isError, refetch, isPreviousData } =
    apiQuery("categories").useGet(categoriesParams);
const { mutate: updateCategory, isLoading: updateLoading } =
    apiQuery("categories").useUpdate();
const { mutate: deleteCategory, isLoading: deleteLoading } =
    apiQuery("categories").useDelete();

const handleUpdate = async (category, property, value) => {
    const form = { ...category, [property]: value };
    updateCategory({ id: category.id, data: form });
};

const debouncedRefetch = useDebounceFn(() => {
    refetch();
}, 300);

const handlePageChange = (newPage, action) => {
    if (action === "increment" && !isPreviousData) {
        categoriesParams.value.page = categoriesParams.value.page + 1;
    } else if (action === "decrement") {
        categoriesParams.value.page = Math.max(
            categoriesParams.value.page - 1,
            1
        );
    } else {
        categoriesParams.value.page = newPage;
        debouncedRefetch();
    }
};

const [value, toggle] = useToggle();
const handleDelete = async (id) => {
    deleteCategory(id);
};

const links = ref([
    {
        title: t("common.menu.home"),
        link: "/",
    },
    {
        title: t("admin.categories.title"),
        current: true,
        link: "/categories",
    },
]);
</script>