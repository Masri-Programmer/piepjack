<template>
    <PageLayout
        :data="links"
        :title="
            $t('admin.orderDetails.title', {
                orderNumber: data?.order_number || '',
            })
        "
        :isLoading="isLoading"
        :error="error"
    >
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-4">
            <div>
                <h3 class="font-semibold text-accent_dark">
                    {{ $t('admin.orderDetails.customerDetails') }}
                </h3>
                <p class="text-accent_dark">
                    {{ data?.user.first_name }}
                    {{ data?.user.last_name }}
                </p>
                <p class="text-accent_dark">
                    {{ $t('admin.orderDetails.email') }} {{ data?.user.email }}
                </p>
            </div>
            <div>
                <h3 class="font-semibold text-accent_dark">
                    {{ $t('admin.orderDetails.shippingAddress') }}
                </h3>
                <p class="text-accent_dark">
                    {{ data?.shipping_address.street_address }}
                </p>
                <p class="text-accent_dark">
                    {{ data?.shipping_address.postal_code }}
                    {{ data?.shipping_address.city }}
                </p>
                <p class="text-accent_dark">
                    {{ data?.shipping_address.state_province }},
                    {{ data?.shipping_address.country_name }}
                </p>
            </div>
            <div
                v-if="
                    data?.billing_address &&
                    data?.billing_address.id !== data?.shipping_address.id
                "
            >
                <h3 class="font-semibold text-accent_dark">
                    {{ $t('admin.orderDetails.billingAddress') }}
                </h3>
                <p class="text-accent_dark">
                    {{ data?.billing_address.street_address }}
                </p>
                <p class="text-accent_dark">
                    {{ data?.billing_address.postal_code }}
                    {{ data?.billing_address.city }}
                </p>
                <p class="text-accent_dark">
                    {{ data?.billing_address.state_province }},
                    {{ data?.billing_address.country_name }}
                </p>
            </div>
        </div>

        <div class="py-4 border-t">
            <h3 class="font-semibold text-accent_dark">
                {{ $t('admin.orderDetails.orderDetails') }}
            </h3>
            <p class="text-accent_dark">
                {{ $t('admin.orderDetails.totalPrice') }} €{{
                    data?.total_price
                }}
            </p>
            <p class="text-accent_dark">
                {{ $t('admin.orderDetails.status') }}
                <span :class="statusClass(data)">{{ data?.status }}</span>
            </p>
            <p class="text-accent_dark">
                {{ $t('admin.orderDetails.createdAt') }}
                {{ formatDate(data?.created_at) }}
            </p>
            <p class="text-accent_dark">
                {{ $t('admin.orderDetails.updatedAt') }}
                {{ formatDate(data?.updated_at) }}
            </p>
        </div>

        <div v-if="data?.returning" class="py-4 border-t">
            <h3 class="font-semibold text-accent_dark">
                {{ $t('admin.orderDetails.returnDetails') }}
            </h3>
            <p class="text-accent_dark">
                {{ $t('admin.orderDetails.returnNumber') }}
                {{ data.returning.return_number }}
            </p>
            <p class="text-accent_dark">
                {{ $t('admin.orderDetails.returnStatus') }}
                <span :class="returnStatusClass(data.returning)">{{
                    data.returning.status
                }}</span>
            </p>
            <p class="text-accent_dark">
                {{ $t('admin.orderDetails.returnReason') }}
                {{ data.returning.reason }}
            </p>
        </div>

        <div class="py-4 border-t">
            <h3 class="font-semibold text-accent_dark">
                {{ $t('admin.orderDetails.products') }}
            </h3>
            <div
                v-for="(product, index) in data?.products"
                :key="index"
                class="py-4 transition-all duration-300"
            >
                <p
                    v-if="returnedProductIds.has(product.product_item_id)"
                    class="mb-2 text-sm font-semibold text-red-700"
                >
                    ℹ️ {{ $t('admin.orderDetails.itemMarkedForReturn') }}
                </p>
                <ProductItem :product="product" />
            </div>
        </div>
    </PageLayout>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import { useI18n } from 'vue-i18n';
import ProductItem from './ProductItem.vue';
import PageLayout from '@layouts/admin/PageLayout.vue';
import { formatDate, apiQuery } from '@lib/helpers';

const { t } = useI18n();
const route = useRoute();
const id = computed(() => route.params.id);
const slug = route.params.id;
const { data, error, isLoading } = apiQuery('orders').useGetById(id);

const returnedProductIds = computed(() => {
    if (!data.value?.returning?.items) {
        return new Set();
    }
    return new Set(
        data.value.returning.items.map((item) => item.product_item_id)
    );
});

const statusClass = (order) => {
    return order?.status === 'paid' ? 'text-green-600' : 'text-red-600';
};

const returnStatusClass = (returning) => {
    const statusClasses = {
        approved: 'text-green-600',
        pending: 'text-yellow-600',
        rejected: 'text-red-600',
    };
    return statusClasses[returning?.status] || 'text-gray-600';
};

const links = computed(() => [
    {
        title: t('admin.links.home'),
        link: '/',
    },
    {
        title: t('admin.links.orders'),
        link: '/Orders',
    },
    {
        title: slug,
        current: true,
        link: '',
    },
]);
</script>