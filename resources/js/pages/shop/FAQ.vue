<template>
    <div
        class="max-w-4xl mx-auto px-6 py-16 sm:py-24 selection:bg-accent_dark selection:text-[#f2f0a1]"
    >
        <header
            class="mb-16 border-b-[12px] border-accent_dark pb-8 text-center md:text-left"
        >
            <p
                class="mb-2 text-sm font-bold uppercase tracking-[0.2em] text-muted-foreground"
            >
                {{ $t("pages.faq.help") }}
            </p>
            <h1
                class="text-5xl sm:text-7xl md:text-8xl font-accent_dark uppercase tracking-tighter leading-none italic break-words"
            >
                {{ $t("pages.faq.title") }}
            </h1>
        </header>

        <div class="w-full space-y-16">
            <div
                v-for="(category, index) in faqData"
                :key="index"
                class="space-y-6"
            >
                <h2
                    class="text-2xl font-accent_dark uppercase bg-primary text-primary-foreground px-4 py-2 w-fit rounded-none tracking-tight italic"
                >
                    {{ $t(category.title) }}
                </h2>

                <Accordion type="single" collapsible class="w-full space-y-4">
                    <AccordionItem
                        v-for="(item, idx) in category.items"
                        :key="idx"
                        :value="`item-${index}-${idx}`"
                        class="border-2 border-accent_dark bg-background rounded-none"
                    >
                        <AccordionTrigger
                            class="px-5 py-5 text-left text-sm sm:text-base font-accent_dark uppercase tracking-wide hover:no-underline hover:bg-accent_dark hover:text-[#f2f0a1] transition-colors rounded-none group [&[data-state=open]]:bg-accent_dark [&[data-state=open]]:text-[#f2f0a1]"
                        >
                            {{ $t(item.question) }}
                        </AccordionTrigger>

                        <AccordionContent
                            class="px-5 py-6 border-t-2 border-accent_dark bg-accent_light text-base font-medium text-gray-800 leading-relaxed"
                        >
                            <p>
                                {{
                                    $t(item.answer, {
                                        email: "info@piepjack-clothing.com",
                                    })
                                }}
                            </p>
                        </AccordionContent>
                    </AccordionItem>
                </Accordion>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import {
    Accordion,
    AccordionContent,
    AccordionItem,
    AccordionTrigger,
} from "@/components/ui/accordion";

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
            {
                question: "pages.faq.need_help",
                answer: "pages.faq.need_help_answer",
            },
        ],
    },
]);
</script>
