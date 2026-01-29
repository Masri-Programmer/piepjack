<template>
    <PageLayout
        :data="links"
        :title="'User Info'"
        :is-loading="isLoading"
        :error="error"
    >
        <div v-if="data?.data" class="text-accent space-y-6">
            <div>
                <h2 class="text-xl font-semibold border-b pb-2 mb-3">
                    General Information
                </h2>
                <p><span class="font-bold">Email:</span> {{ data.data.email }}</p>
            </div>

            <div>
                <h2 class="text-xl font-semibold border-b pb-2 mb-3">
                    Addresses
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
                                    >Default Shipping</span
                                >
                                <span
                                    v-if="address.is_default_billing"
                                    class="text-xs font-semibold text-emerald-800 bg-emerald-100 px-2 py-1 rounded-full"
                                    >Default Billing</span
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
                <p v-else class="text-gray-500">No addresses found.</p>
            </div>

            <div>
                <h2 class="text-xl font-semibold border-b pb-2 mb-3">
                    Orders
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
                                <th class="table-header">ID</th>
                                <th class="table-header">Total Price (€)</th>
                                <th class="table-header">Status</th>
                                <th class="table-header">Created At</th>
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
                <p v-else class="text-gray-500">This user has no orders.</p>
            </div>
        </div>
    </PageLayout>
</template>

<script setup>
import { useRoute } from 'vue-router';
import PageLayout from '@layouts/admin/PageLayout.vue';
import { ref, computed } from 'vue';
import { apiQuery } from '@lib/helpers';

const route = useRoute();
const id = computed(() => route.params.id);
const slug = route.params.slug;
const { data, error, isLoading } = apiQuery('users').useGetById(id);

const links = ref([
    {
        title: 'Home',
        link: '/',
    },
    {
        title: 'users',
        link: '/users',
    },
    {
        title: slug,
        current: true,
        link: '',
    },
]);
</script>