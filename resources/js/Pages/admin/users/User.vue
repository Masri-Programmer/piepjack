<template>
    <PageLayout
        :data="links"
        :title="'User Info'"
        :isLoading="isLoading"
        :error="error"
    >
        <div class="text-accent" v-if="data?.data">
            <p><span class="font-bold">Email:</span> {{ data.data.email }}</p>
            <p>
                <span class="font-bold">Name:</span>
                {{ data.data.info?.first_name }}
                {{ data.data.info?.last_name }}
            </p>
            <p>
                <span class="font-bold">Address Line 1:</span>
                {{ data.data.info?.address_line_one }}
            </p>
            <p>
                <span class="font-bold">Address Line 2:</span>
                {{ data.data.info?.address_line_two }}
            </p>
            <h2 class="text-lg font-semibold">Orders</h2>
            <div class="overflow-x-auto">
                <table
                    class="w-full border border-collapse table-auto border-slate-300"
                >
                    <thead class="bg-slate-100">
                        <tr>
                            <th
                                class="px-4 py-2 text-left border border-slate-300"
                            >
                                ID
                            </th>
                            <th
                                class="px-4 py-2 text-left border border-slate-300"
                            >
                                Total Price (€)
                            </th>
                            <th
                                class="px-4 py-2 text-left border border-slate-300"
                            >
                                Status
                            </th>
                            <th
                                class="px-4 py-2 text-left border border-slate-300"
                            >
                                Created At
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="order in data.data.orders"
                            :key="order.id"
                            class="odd:bg-slate-50"
                        >
                            <td class="px-4 py-2 border border-slate-300">
                                {{ order.id }}
                            </td>
                            <td class="px-4 py-2 border border-slate-300">
                                {{ order.total_price }}
                            </td>
                            <td
                                class="px-4 py-2 capitalize border border-slate-300"
                            >
                                {{ order.status }}
                            </td>
                            <td class="px-4 py-2 border border-slate-300">
                                {{
                                    new Date(order.created_at).toLocaleString()
                                }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </PageLayout>
</template>

<script setup>
import { useRoute } from "vue-router";
import PageLayout from "@layouts/admin/PageLayout.vue";
import { ref, computed } from "vue";
import { apiQuery } from "@lib/helpers";

const route = useRoute();
const id = computed(() => route.params.id);
const slug = route.params.slug;
const { data, error, isLoading } = apiQuery("users").useGetById(id);
const links = ref([
    {
        title: "Home",
        link: "/",
    },
    {
        title: "users",
        link: "/users",
    },
    {
        title: slug,
        current: true,
        link: "",
    },
]);
</script>
