<script setup>
import PageLayout from "@components/shop/general/PageLayout.vue";
import { ArrowLeft } from "lucide-vue-next";
import { Button } from "@/components/ui/button";
</script>

<template>
    <PageLayout
        :title="$t('pages.shipping.title')"
        :description="$t('pages.shipping.description')"
    >
        <template #header>
            <div
                class="border-b-[12px] border-border pb-10 text-center md:text-left"
            >
                <p
                    class="mb-4 text-xs font-bold uppercase tracking-[0.3em] text-muted-foreground"
                >
                    {{ $t("pages.shipping.logistics") }}
                </p>
                <h1
                    class="text-6xl sm:text-7xl md:text-9xl font-bold uppercase tracking-tighter leading-[0.8] italic text-foreground"
                >
                    {{ $t("pages.shipping.title") }}
                </h1>
            </div>
        </template>

        <div class="max-w-4xl space-y-16">
            <div class="grid md:grid-cols-2 gap-12">
                <div class="border-l-8 border-main pl-6">
                    <p
                        class="text-2xl leading-tight font-bold text-foreground italic uppercase tracking-tighter"
                    >
                        {{ $t("pages.shipping.tracking_info_header") }}
                    </p>
                </div>
                <p
                    class="text-sm leading-relaxed text-muted-foreground uppercase tracking-widest font-medium"
                >
                    {{ $t("pages.shipping.tracking_info_description") }}
                </p>
            </div>

            <div
                class="w-full overflow-hidden border-4 border-border bg-background"
            >
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr
                                class="bg-main text-accent uppercase text-xs tracking-[0.2em] font-bold"
                            >
                                <th class="p-6 border-r-2 border-accent/20">
                                    {{ $t("pages.shipping.table.country") }}
                                </th>
                                <th
                                    class="p-6 border-r-2 border-accent/20 text-center"
                                >
                                    {{ $t("pages.shipping.table.delivery") }}
                                </th>
                                <th
                                    class="p-6 border-r-2 border-accent/20 text-center"
                                >
                                    {{ $t("pages.shipping.table.cost") }}
                                </th>
                                <th class="p-6 text-center">
                                    {{
                                        $t("pages.shipping.table.free_shipping")
                                    }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y-4 divide-border">
                            <tr class="hover:bg-muted transition-colors group">
                                <td
                                    class="p-6 font-bold uppercase italic border-r-2 border-border text-lg"
                                >
                                    {{ $t("pages.shipping.countries.germany") }}
                                </td>
                                <td
                                    class="p-6 text-center border-r-2 border-border font-mono text-lg"
                                >
                                    4-8
                                </td>
                                <td
                                    class="p-6 text-center border-r-2 border-border font-bold text-lg"
                                >
                                    €3,95
                                </td>
                                <td
                                    class="p-6 text-center font-bold text-main whitespace-nowrap"
                                >
                                    {{ $t("pages.shipping.over") }} €70
                                </td>
                            </tr>
                            <tr class="hover:bg-muted transition-colors group">
                                <td
                                    class="p-6 font-bold uppercase italic border-r-2 border-border text-lg"
                                >
                                    {{ $t("pages.shipping.countries.austria") }}
                                </td>
                                <td
                                    class="p-6 text-center border-r-2 border-border font-mono text-lg"
                                >
                                    4-8
                                </td>
                                <td
                                    class="p-6 text-center border-r-2 border-border font-bold text-lg"
                                >
                                    €4,95
                                </td>
                                <td
                                    class="p-6 text-center font-bold text-main whitespace-nowrap"
                                >
                                    {{ $t("pages.shipping.over") }} €70
                                </td>
                            </tr>
                            <tr class="hover:bg-muted transition-colors group">
                                <td
                                    class="p-6 font-bold uppercase italic border-r-2 border-border text-lg"
                                >
                                    {{
                                        $t(
                                            "pages.shipping.countries.switzerland_eu",
                                        )
                                    }}
                                </td>
                                <td
                                    class="p-6 text-center border-r-2 border-border font-mono text-lg"
                                >
                                    3-5
                                </td>
                                <td
                                    class="p-6 text-center border-r-2 border-border font-bold text-lg"
                                >
                                    €9,99
                                </td>
                                <td
                                    class="p-6 text-center opacity-30 font-bold"
                                >
                                    —
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div
                class="p-8 bg-muted border-2 border-border text-center relative overflow-hidden"
            >
                <div class="relative z-10">
                    <p
                        class="font-bold uppercase tracking-widest text-sm italic text-foreground"
                    >
                        {{ $t("pages.shipping.warning") }}
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div
                class="pt-12 border-t-4 border-border flex justify-between items-center"
            >
                <Button
                    variant="ghost"
                    as-child
                    class="rounded-none px-0 group"
                >
                    <router-link to="/" class="flex items-center gap-2">
                        <ArrowLeft
                            class="w-4 h-4 transition-transform group-hover:-translate-x-1"
                        />
                        <span
                            class="text-xs font-bold uppercase tracking-widest border-b-2 border-foreground group-hover:bg-foreground group-hover:text-background transition-all"
                        >
                            Back to Shop
                        </span>
                    </router-link>
                </Button>
                <p
                    class="text-[10px] font-bold uppercase tracking-[0.2em] text-muted-foreground"
                >
                    PIEPJACK LOGISTICS
                </p>
            </div>
        </div>
    </PageLayout>
</template>
