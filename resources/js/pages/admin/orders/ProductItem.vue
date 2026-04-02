<template>
  <div
    class="flex items-center gap-4 border border-gray p-4 rounded-2xl shadow-md bg-transparent w-full"
  >
    <img
      :src="product.image"
      :alt="product.name"
      loading="lazy"
      class="object-cover w-40 h-40 rounded-lg shadow-md"
    />

    <div class="flex flex-col w-full">
      <h4 class="text-xl font-semibold text-accent capitalize">
        {{ product.name }}
      </h4>
      <p class="text-gray">{{ $t('admin.orders.table.id') }}:<span class="font-medium text-accent_dark"> {{ product.product_item_id }}</span></p>
      <p class="text-gray">
        {{ $t('admin.orderDetails.price') }}
        <span class="font-medium text-accent_dark"
          >€{{ product.price_per_item }}</span
        >
      </p>
      <p class="text-gray">
        {{ $t('admin.orderDetails.quantity') }}
        <span class="font-medium text-accent_dark">{{ product.quantity }}</span>
      </p>

      <div v-if="product.options.length" class="mt-3">
        <h5 class="font-semibold text-accent">{{ $t('admin.orderDetails.options') }}</h5>
        <ul class="mt-1 space-y-1">
          <li
            v-for="(option, index) in product.options"
            :key="index"
            class="flex justify-between text-sm"
          >
            <span class="text-gray">{{ option.name }}:</span>
            <span class="text-accent font-medium">{{ option.value }}</span>
          </li>
        </ul>
      </div>
      <div
        v-if="product.returned_quantity > 0"
        class="mt-3 p-3 rounded-xl bg-red-50 border border-red-300"
      >
        <h5 class="text-red-600 font-semibold flex items-center gap-1">
          <span class="text-sm">🔄</span> {{ $t('admin.orderDetails.returnedItems') }}
        </h5>
        <div class="grid grid-cols-2 gap-2 mt-1 text-red-500 text-sm">
          <p><strong>{{ $t('admin.orderDetails.returnedQty') }}</strong> {{ product.returned_quantity }}</p>
          <p><strong>{{ $t('admin.orders.table.id') }}:</strong> {{ product.return_id }}</p>
          <p><strong>{{ $t('admin.orderDetails.returnedReason') }}</strong> {{ product.return_reason }}</p>
          <p
            :class="[
              'font-bold',
              product.return_status === 'not_requested'
                ? 'text-green-500'
                : product.return_status === 'requested'
                ? 'text-yellow-700'
                : product.return_status === 'approved'
                ? 'text-fuchsia-500'
                : 'text-red-500',
            ]"
          >
            <strong>{{ $t('admin.returns.headers.status') }}:</strong> {{ product.return_status }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  product: {
    type: Object,
    required: true,
  },
});
</script>
