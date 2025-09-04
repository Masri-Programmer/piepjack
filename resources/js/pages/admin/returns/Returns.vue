<template>
    <PageLayout :data="links" :title="$t('admin.returns.title')">
        <div
            class="flex justify-between items-stretch flex-wrap min-h-[70px] pb-6 text-accent"
        >
            <h3
                class="flex flex-col items-start justify-center m-2 ml-0 font-medium text-xl/tight text-dark"
            >
                <span class="mr-3 font-semibold text-dark"
                    >{{ $t('admin.returns.allReturns') }} ({{
                        data?.meta.total
                    }})</span
                >
                <span class="mt-1 font-medium text-gray text-lg/normal"
                    >{{ $t('admin.returns.subtitle') }}</span
                >
            </h3>
            <SearchField
                v-model="searchTerm"
                :placeholder="$t('admin.returns.searchPlaceholder')"
            />
        </div>
        <div class="flex-auto block text-accent">
            <div class="overflow-x-auto">
                <table
                    class="w-full my-0 align-middle text-dark border-neutral-200"
                >
                    <thead class="align-bottom">
                        <tr class="font-semibold text-[0.95rem] text-gray">
                            <th class="px-1 pb-3 uppercase text-start">
                                {{ $t('admin.returns.headers.id') }}
                            </th>
                            <th class="px-1 pb-3 uppercase text-start">
                                {{ $t('admin.returns.headers.orderId') }}
                            </th>
                            <th
                                class="pb-3 px-1 text-start uppercase min-w-[120px]"
                            >
                                {{ $t('admin.returns.headers.status') }}
                            </th>
                            <th class="px-1 pb-3 uppercase text-start">
                                {{ $t('admin.returns.headers.reason') }}
                            </th>
                            <th
                                class="pb-3 px-1 text-start min-w-[150px] uppercase"
                            >
                                {{ $t('admin.returns.headers.createdAt') }}
                            </th>
                            <th
                                class="pb-3 px-1 text-start min-w-[150px] uppercase"
                            >
                                {{ $t('admin.returns.headers.updatedAt') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-if="!data?.data.length && !isLoading && !isError"
                            class="my-6 text-xl font-bold"
                        >
                            {{
                                $t('admin.returns.noReturnsFound')
                            }}
                        </tr>
                        <ProductsShowSkeleton v-if="isLoading" />
                        <tr
                            class="my-6 text-xl border-b border-neutral-200 ont-bold"
                            v-if="error"
                        >
                            {{
                                error
                            }}
                        </tr>
                        <template v-if="!isLoading && data?.data">
                            <tr
                                class="border-b border-dashed last:border-b-0 border-main"
                                v-for="returning in data?.data"
                            >
                                <td>{{ returning.id }}</td>
                                <td class="text-blue-400 underline">
                                    <router-link
                                        :to="`/admin/orders/${returning.order_id}`"
                                    >
                                        # {{ returning.order_id }}
                                    </router-link>
                                </td>
                                <td class="p-3">
                                    <div class="flex items-center">
                                        <div
                                            class="flex flex-col justify-start w-full"
                                        >
                                            <span
                                                class="mb-1 font-semibold transition-colors duration-200 ease-in-out text-lg/normal text-secondary-inverse hover:text-blue-500"
                                            >
                                                <select
                                                    v-model="returning.status"
                                                    id="status"
                                                    :class="[
                                                        'font-bold',
                                                        returning.status ===
                                                        'not_requested'
                                                            ? 'text-green-500'
                                                            : returning.status ===
                                                              'requested'
                                                            ? 'text-yellow-700'
                                                            : returning.status ===
                                                              'approved'
                                                            ? 'text-fuchsia-500'
                                                            : 'text-red-500',
                                                    ]"
                                                    @change="
                                                        (e) =>
                                                            handleUpdate(
                                                                returning,
                                                                'status',
                                                                e.target.value
                                                            )
                                                    "
                                                    class="block w-full p-2 mt-1 border rounded-md border-gray focus:outline-none focus:ring focus:ring-accent_dark"
                                                >
                                                    <option
                                                        v-for="status in data
                                                            ?.meta.statuses"
                                                        :key="status"
                                                        :class="[
                                                            'font-bold',
                                                            status ===
                                                            'not_requested'
                                                                ? 'text-green-500'
                                                                : status ===
                                                                  'requested'
                                                                ? 'text-yellow-700'
                                                                : status ===
                                                                  'approved'
                                                                ? 'text-fuchsia-500'
                                                                : 'text-red-500',
                                                        ]"
                                                        :value="status"
                                                    >
                                                        {{ status }}
                                                    </option>
                                                </select>
                                            </span>
                                        </div>
                                    </div>
                                    </td>
                                <td class="p-3 text-sm text-start text-pretty">
                                    {{ returning.reason }}
                                </td>
                                <td class="p-3 text-start">
                                    <span
                                        class="text-xs text-center align-baseline inline-flex p-3 mr-auto items-center font-semibold text-[.95rem] leading-none text-blue-500 bg-blue-500/20 rounded-lg"
                                        >{{
                                            formatDate(returning.created_at)
                                        }}</span
                                    >
                                </td>
                                <td class="p-3 text-start">
                                    <span
                                        class="text-xs text-center align-baseline inline-flex p-3 mr-auto items-center font-semibold text-[.95rem] leading-none text-blue-500 bg-blue-500/20 rounded-lg"
                                        >{{
                                            formatDate(returning.updated_at)
                                        }}</span
                                    >
                                </td>
                                <td class="p-3 text-start">
                                    <div class="flex items-center gap-2">
                                        <button
                                            :disabled="deleteLoading"
                                            class="ml-auto relative text-main bg-accent hover:text-white hover:bg-red-500 flex items-center h-[30px] w-[30px] text-base font-medium leading-normal text-center align-middle cursor-pointer rounded-2xl transition-colors duration-200 ease-in-out shadow-none breturning-0 justify-center"
                                            @click="
                                                () => handleDelete(returning.id)
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
                                                :to="`/admin/orders/${returning.order_id}`"
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
                    :isFetching="isFetching"
                    @pageChanged="handlePageChange"
                />
            </div>
        </div>
    </PageLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import { returnsParams } from "@lib/store/admin";
import { useToggle } from "@vueuse/core";
import { useDebounceFn } from "@vueuse/core";
import PageLayout from "@layouts/admin/PageLayout.vue";
import Pagination from "@layouts/admin/Pagination.vue";
import { Trash2, ChevronRight } from "lucide-vue-next";
import SearchField from "@components/admin/SearchField.vue";
import { formatDate, apiQuery } from "@lib/helpers";
import ProductsShowSkeleton from "../products/ProductsShowSkeleton.vue";
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const searchTerm = computed({
    get: () => returnsParams.value.search,
    set: (value) => {
        returnsParams.value.search = value;
        debouncedRefetch();
    },
});

const page = computed({
    get: () => returnsParams.value.page,
    set: (value) => {
        returnsParams.value.page = value;
        debouncedRefetch();
    },
});

const { data, error, isError, isLoading, isPreviousData, isFetching, refetch } =
    apiQuery("returns").useGet(returnsParams);

const {
    mutate: updateReturn,
    isLoading: updateLoading,
    isSuccess: updateSuccess,
    error: updateErrorMessage,
} = apiQuery("returns").useUpdate();
const {
    mutate: deleteReturn,
    isLoading: deleteLoading,
    isSuccess: deleteSuccess,
    error: deleteErrorMessage,
} = apiQuery("returns").useDelete();

const handleUpdate = async (returning, property, value) => {
    const form = { ...returning, [property]: value };
    updateReturn({ id: returning.id, data: form });
};
const handleDelete = async (id) => {
    deleteReturn(id);
};

const handlePageChange = (newPage, action) => {
    if (action === "increment" && !isPreviousData) {
        returnsParams.value.page = returnsParams.value.page + 1;
    } else if (action === "decrement") {
        returnsParams.value.page = Math.max(returnsParams.value.page - 1, 1);
    } else {
        returnsParams.value.page = newPage;
        debouncedRefetch();
    }
};

const links = computed(() => [
    { title: t('admin.links.home'), link: "/" },
    { title: t('admin.links.products'), current: true, link: "/products" },
]);

const [value, toggle] = useToggle();
const debouncedRefetch = useDebounceFn(() => {
    refetch();
}, 300);
</script>