<template>
    <PageLayout
        :data="links"
        :title="$t('admin.users.info')"
        :is-loading="isLoading"
        :error="error"
    >
        <div v-if="data?.data" class="text-accent space-y-6">
            <div>
                <h2 class="text-xl font-semibold border-b pb-2 mb-3">
                    {{ $t('admin.users.generalInfo') }}
                </h2>
                <p><span class="font-bold">{{ $t('admin.users.email') }}:</span> {{ data.data.email }}</p>
            </div>

            <div>
                <h2 class="text-xl font-semibold border-b pb-2 mb-3">
                    {{ $t('admin.users.addresses') }}
                </h2>
                <div
                    v-if="data.data.info?.length > 0"
                    class="grid grid-cols-1 md:grid-cols-2 gap-4"
                >
                    <div
                        v-for="address in data.data.info"
                        :key="address.id"
                        class="p-4 border border-slate-300 rounded-lg bg-white"
                    >
                        <div
                            class="flex justify-between items-center mb-2"
                        >
                            <h3 class="text-lg font-bold">
                                {{ address.label }}
                            </h3>
                            <div class="flex gap-2">
                                <span
                                    v-if="address.is_default_shipping"
                                    class="text-xs font-semibold text-sky-800 bg-sky-100 px-2 py-1 rounded-full"
                                    >{{ $t('admin.users.defaultShipping') }}</span
                                >
                                <span
                                    v-if="address.is_default_billing"
                                    class="text-xs font-semibold text-emerald-800 bg-emerald-100 px-2 py-1 rounded-full"
                                    >{{ $t('admin.users.defaultBilling') }}</span
                                >
                            </div>
                        </div>
                        <div class="text-sm text-gray-600">
                            <p>{{ address.street_address }}</p>
                            <p>
                                {{ address.city }},
                                {{ address.state_province }}
                                {{ address.postal_code }}
                            </p>
                            <p>{{ address.country_name }}</p>
                        </div>
                    </div>
                </div>
                <p v-else class="text-gray-500">{{ $t('admin.users.noAddresses') }}</p>
            </div>

            <div>
                <h2 class="text-xl font-semibold border-b pb-2 mb-3">
                    {{ $t('admin.users.orders') }}
                </h2>
                <div
                    v-if="data.data.orders?.length > 0"
                    class="overflow-x-auto"
                >
                    <table
                        class="w-full border-collapse table-auto"
                    >
                        <thead class="bg-slate-100">
                            <tr>
                                <th class="table-header">{{ $t('admin.users.table.id') }}</th>
                                <th class="table-header">{{ $t('admin.users.table.totalPrice') }}</th>
                                <th class="table-header">{{ $t('admin.users.table.status') }}</th>
                                <th class="table-header">{{ $t('admin.users.table.createdAt') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="order in data.data.orders"
                                :key="order.id"
                                class="odd:bg-white even:bg-slate-50"
                            >
                                <td class="table-cell">{{ order.id }}</td>
                                <td class="table-cell">
                                    {{ order.total_price }}
                                </td>
                                <td class="table-cell capitalize">
                                    {{ order.status }}
                                </td>
                                <td class="table-cell">
                                    {{
                                        new Date(
                                            order.created_at,
                                        ).toLocaleString()
                                    }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <p v-else class="text-gray-500">{{ $t('admin.users.noOrders') }}</p>
            </div>
        </div>
    </PageLayout>
</template>

<script setup>
import { useRoute } from 'vue-router';
import PageLayout from '@layouts/admin/PageLayout.vue';
import { ref, computed } from 'vue';
import { apiQuery } from '@lib/helpers';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const route = useRoute();
const id = computed(() => route.params.id);
const slug = route.params.slug;
const { data, error, isLoading } = apiQuery('users').useGetById(id);

const links = computed(() => [
    {
        title: t('admin.links.home'),
        link: '/',
    },
    {
        title: t('admin.links.users'),
        link: '/users',
    },
    {
        title: slug,
        current: true,
        link: '',
    },
]);
</script>