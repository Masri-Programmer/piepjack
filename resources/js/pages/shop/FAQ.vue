<template>
    <PageLayout
        title="FAQ"
        description="Frequently asked questions about ordering, shipping, and returns at Piepjack Clothing."
    >
        <template #header>
            <div
                class="border-b-[12px] border-border pb-10 text-center md:text-left"
            >
                <p
                    class="mb-4 text-xs font-bold uppercase tracking-[0.3em] text-muted-foreground"
                >
                    {{ $t("pages.faq.help") }}
                </p>
                <h1
                    class="text-6xl sm:text-7xl md:text-9xl font-bold uppercase tracking-tighter leading-[0.8] italic break-words text-foreground"
                >
                    {{ $t("pages.faq.title") }}
                </h1>
            </div>
        </template>

        <div class="w-full space-y-24">
            <section
                v-for="(category, index) in faqData"
                :key="index"
                class="space-y-8"
            >
                <h2
                    class="text-xl font-bold uppercase bg-primary text-primary-foreground px-6 py-3 w-fit rounded-none tracking-tight italic"
                >
                    {{ $t(category.title) }}
                </h2>

                <Accordion type="single" collapsible class="w-full space-y-4">
                    <AccordionItem
                        v-for="(item, idx) in category.items"
                        :key="idx"
                        :value="`item-${index}-${idx}`"
                        class="border-2 border-border bg-background rounded-none transition-all duration-300"
                    >
                        <AccordionTrigger
                            class="px-6 py-6 text-left text-base sm:text-lg font-bold uppercase tracking-wide hover:no-underline hover:bg-main hover:text-accent transition-colors rounded-none group [&[data-state=open]]:bg-main [&[data-state=open]]:text-accent"
                        >
                            {{ $t(item.question) }}
                        </AccordionTrigger>

                        <AccordionContent
                            class="px-6 py-8 border-t-2 border-border bg-muted text-base font-medium leading-relaxed text-foreground/90"
                        >
                            <div
                                class="prose prose-zinc dark:prose-invert max-w-none"
                            >
                                <p>
                                    {{
                                        $t(item.answer, {
                                            email: "info@piepjack-clothing.com",
                                        })
                                    }}
                                </p>
                            </div>
                        </AccordionContent>
                    </AccordionItem>
                </Accordion>
            </section>
        </div>
    </PageLayout>
</template>

<script setup>
import { ref } from "vue";
import PageLayout from "@components/shop/general/PageLayout.vue";
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
