<script setup>
import PageLayout from "@components/shop/general/PageLayout.vue";
import { Separator } from "@/components/ui/separator";
import { Button } from "@/components/ui/button";
import { ArrowLeft } from "lucide-vue-next";
</script>

<template>
    <PageLayout
        :title="$t('pages.impressum.title')"
        :description="$t('pages.impressum.description')"
    >
        <template #header>
            <div
                class="border-b-[12px] border-border pb-10 text-center md:text-left"
            >
                <p
                    class="mb-4 text-xs font-bold uppercase tracking-[0.3em] text-muted-foreground"
                >
                    {{ $t("pages.impressum.last_updated") }}
                </p>
                <h1
                    class="text-6xl sm:text-7xl md:text-9xl font-bold uppercase tracking-tighter leading-[0.8] italic text-foreground"
                >
                    {{ $t("pages.impressum.title") }}
                </h1>
            </div>
        </template>

        <div class="max-w-4xl space-y-16">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <!-- Responsible according to TMG -->
                <section class="space-y-4">
                    <p class="text-xs font-bold uppercase tracking-widest text-muted-foreground">
                        {{ $t("pages.impressum.responsible.label") }}
                    </p>
                    <div class="text-3xl font-bold uppercase tracking-tighter italic text-foreground">
                        PIEPJACK Clothing
                    </div>
                    <div class="text-lg font-medium leading-relaxed text-muted-foreground/90">
                        Inhaber: Marvin Pieprzyk<br />
                        Schollendamm 122a<br />
                        27751 Delmenhorst<br />
                        Deutschland
                    </div>
                </section>

                <!-- Contact -->
                <section class="space-y-4">
                    <p class="text-xs font-bold uppercase tracking-widest text-muted-foreground">
                        {{ $t("pages.impressum.contact.title") }}
                    </p>
                    <div class="flex flex-col space-y-2">
                        <a href="mailto:info@piepjack-clothing.de" class="text-xl font-bold hover:text-main transition-colors">
                            info@piepjack-clothing.de
                        </a>
                        <a href="https://www.piepjack-clothing.de" target="_blank" class="text-xl font-bold hover:text-main transition-colors">
                            www.piepjack-clothing.de
                        </a>
                    </div>
                </section>
            </div>

            <Separator class="opacity-20" />

            <!-- Legal Notice / Markenrecht -->
            <section class="bg-muted p-8 border-2 border-border space-y-4">
                <h2 class="text-xl font-bold uppercase tracking-tight text-foreground">
                    {{ $t("pages.impressum.legal.title") }}
                </h2>
                <p class="text-lg font-medium italic text-muted-foreground">
                    {{ $t("pages.impressum.legal.content") }}
                </p>
            </section>

            <Separator class="opacity-20" />

            <!-- Dispute Resolution -->
            <section class="space-y-4">
                <h2 class="text-xl font-bold uppercase tracking-tight text-foreground">
                    {{ $t("pages.impressum.dispute.title") }}
                </h2>
                <div class="text-muted-foreground font-medium leading-relaxed">
                    {{ $t("pages.impressum.dispute.content") }}
                    <a href="https://ec.europa.eu/consumers/odr" target="_blank" class="text-main font-bold hover:underline ml-1">
                        https://ec.europa.eu/consumers/odr
                    </a>
                </div>
            </section>

            <!-- Footer -->
            <div class="pt-12 border-t-4 border-border flex justify-between items-center">
                <Button variant="ghost" as-child class="rounded-none px-0 group">
                    <router-link to="/" class="flex items-center gap-2">
                        <ArrowLeft class="w-4 h-4 transition-transform group-hover:-translate-x-1" />
                        <span class="text-xs font-bold uppercase tracking-widest border-b-2 border-foreground group-hover:bg-foreground group-hover:text-background transition-all">
                            Back to Shop
                        </span>
                    </router-link>
                </Button>
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-muted-foreground">
                    PIEPJACK LEGAL DEPT.
                </p>
            </div>
        </div>
    </PageLayout>
</template>
