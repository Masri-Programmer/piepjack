<template>
    <PageLayout
        :data="links"
        :title="'Order # ' + data?.id"
        :isLoading="isLoading"
        :error="error"
    >
        <div class="pb-4">
            <h3 class="font-semibold text-accent_dark">Customer Details</h3>
            <p class="text-accent_dark">
                {{ data?.customer_detail.first_name }}
                {{ data?.customer_detail.last_name }}
            </p>
            <p class="text-accent_dark">
                {{ data?.customer_detail.address_line_one }}
            </p>
            <p class="text-accent_dark">
                {{ data?.customer_detail.address_line_two }}
            </p>
            <p class="text-accent_dark">
                Email: {{ data?.customer_detail.customer.email }}
            </p>
        </div>

        <div class="py-4 border-t">
            <h3 class="font-semibold text-accent_dark">Order Details</h3>
            <p class="text-accent_dark">
                Total Price: €{{ data?.total_price }}
            </p>
            <p class="text-accent_dark">
                Status:
                <span :class="statusClass(data)">{{ data?.status }}</span>
            </p>
            <p class="text-accent_dark">
                Created At: {{ formatDate(data?.created_at) }}
            </p>
            <p class="text-accent_dark">
                Updated At: {{ formatDate(data?.updated_at) }}
            </p>
        </div>

        <div v-if="data?.returning" class="py-4 border-t">
            <h3 class="font-semibold text-accent_dark">Return Details</h3>
            <p class="text-accent_dark">
                Return Number: {{ data.returning.return_number }}
            </p>
            <p class="text-accent_dark">
                Return Status:
                <span :class="returnStatusClass(data.returning)">{{
                    data.returning.status
                }}</span>
            </p>
            <p class="text-accent_dark">
                Return Reason: {{ data.returning.reason }}
            </p>
        </div>

        <div class="py-4 border-t">
            <h3 class="font-semibold text-accent_dark">Products</h3>
            <div
                v-for="(product, index) in data?.products"
                :key="index"
                class="py-4 transition-all duration-300"
            >
                <p
                    v-if="returnedProductIds.has(product.product_item_id)"
                    class="mb-2 text-sm font-semibold text-red-700"
                >
                    ℹ️ This item is marked for return.
                </p>
                <ProductItem :product="product" />
            </div>
        </div>
    </PageLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import { useRoute } from "vue-router";
import ProductItem from "./ProductItem.vue";
import PageLayout from "@layouts/admin/PageLayout.vue";
import { formatDate, apiQuery } from "@lib/helpers";

const route = useRoute();
const id = computed(() => route.params.id);
const slug = route.params.id;
const { data, error, isLoading } = apiQuery("orders").useGetById(id);

const returnedProductIds = computed(() => {
    if (!data.value?.returning?.items) {
        return new Set();
    }
    return new Set(
        data.value.returning.items.map((item) => item.product_item_id)
    );
});

const statusClass = (order) => {
    return order?.status === "paid" ? "text-green-600" : "text-red-600";
};

const returnStatusClass = (returning) => {
    const statusClasses = {
        approved: "text-green-600",
        pending: "text-yellow-600",
        rejected: "text-red-600",
    };
    return statusClasses[returning?.status] || "text-gray-600";
};

const links = ref([
    {
        title: "Home",
        link: "/",
    },
    {
        title: "Orders",
        link: "/Orders",
    },
    {
        title: slug,
        current: true,
        link: "",
    },
]);
</script>
