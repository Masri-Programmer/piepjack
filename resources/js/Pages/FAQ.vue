<template>
  <div class="flex flex-col items-center w-full h-full px-4 py-10">
    <h1 class="text-sm font-bold text-center mb-3">
      {{ $t("pages.faq.help") }}
    </h1>
    <h1
      class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-extrabold text-center mb-6 sm:mb-8 md:mb-10 lg:mb-12"
    >
      {{ $t("pages.faq.title") }}
    </h1>
    <div class="w-full max-w-3xl">
      <div v-for="(category, index) in faqData" :key="index" class="mb-6">
        <p class="text-xl font-semibold border-b pb-2">
          {{ $t(category.title) }}
        </p>
        <details
          v-for="(item, idx) in category.items"
          :key="idx"
          class="group mt-4 border overflow-hidden"
        >
          <summary
            class="flex justify-between items-center px-4 py-3 cursor-pointer bg-gray-100 hover:bg-gray-200"
          >
            <span class="text-lg">{{ $t(item.question) }}</span>
            <span class="transition-transform group-open:rotate-45">+</span>
          </summary>
          <div class="px-4 py-3 bg-gray border-t text-accent">
            <p>
              {{ $t(item.answer, { email: "info@piepjack-clothing.com" }) }}
            </p>
          </div>
        </details>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";

const faqData = ref([
  {
    title: "pages.faq.order_process",
    items: [
      {
        question: "pages.faq.how_to_order",
        answer: "pages.faq.how_to_order_answer",
      },
      {
        question: "pages.faq.modify_cancel_order",
        answer: "pages.faq.modify_cancel_order_answer",
      },
    ],
  },
  {
    title: "pages.faq.payment_info",
    items: [
      {
        question: "pages.faq.payment_secure",
        answer: "pages.faq.payment_secure_answer",
      },
    ],
  },
  {
    title: "pages.faq.shipping_delivery",
    items: [
      {
        question: "pages.faq.when_shipped",
        answer: "pages.faq.when_shipped_answer",
      },
      {
        question: "pages.faq.shipping_cost",
        answer: "pages.faq.shipping_cost_answer",
      },
      {
        question: "pages.faq.change_address",
        answer: "pages.faq.change_address_answer",
      },
    ],
  },
  {
    title: "pages.faq.returns_refunds",
    items: [
      {
        question: "pages.faq.return_item",
        answer: "pages.faq.return_item_answer",
      },
      {
        question: "pages.faq.return_conditions",
        answer: "pages.faq.return_conditions_answer",
      },
      {
        question: "pages.faq.refund_time",
        answer: "pages.faq.refund_time_answer",
      },
    ],
  },
  {
    title: "pages.faq.tracking_orders",
    items: [
      {
        question: "pages.faq.track_order",
        answer: "pages.faq.track_order_answer",
      },
    ],
  },
  {
    title: "pages.faq.support_contact",
    items: [
      { question: "pages.faq.need_help", answer: "pages.faq.need_help_answer" },
    ],
  },
]);
</script>
