<script setup>
import PageLayout from "@components/shop/general/PageLayout.vue";
import { Separator } from "@/components/ui/separator";
import { Button } from "@/components/ui/button";
import { ArrowLeft } from "lucide-vue-next";
</script>

<template>
    <PageLayout
        :title="$t('pages.cancellation.title')"
        :description="$t('pages.cancellation.description')"
    >
        <template #header>
            <div
                class="border-b-[12px] border-border pb-10 text-center md:text-left"
            >
                <p
                    class="mb-4 text-xs font-bold uppercase tracking-[0.3em] text-muted-foreground"
                >
                    {{ $t("pages.cancellation.last_updated") }}
                </p>
                <h1
                    class="text-6xl sm:text-7xl md:text-9xl font-bold uppercase tracking-tighter leading-[0.8] italic text-foreground"
                >
                    {{ $t("pages.cancellation.title") }}
                </h1>
            </div>
        </template>

        <div class="max-w-4xl space-y-16">
            <!-- Introduction -->
            <section class="prose prose-xl prose-invert max-w-none">
                <p class="text-muted-foreground leading-relaxed text-pretty">
                    {{ $t("pages.cancellation.intro") }}
                </p>
            </section>

            <!-- Policy Sections -->
            <div class="space-y-12">
                <!-- Right of Withdrawal -->
                <section class="space-y-6">
                    <h2
                        class="text-3xl font-bold uppercase tracking-tight text-foreground border-l-8 border-main pl-6"
                    >
                        {{ $t("pages.cancellation.policy_title") }}
                    </h2>
                    <div
                        class="prose prose-lg prose-invert max-w-none text-muted-foreground/90 whitespace-pre-wrap leading-relaxed"
                    >
                        {{ $t("pages.cancellation.policy_content") }}
                    </div>
                </section>

                <Separator class="opacity-20" />

                <!-- Consequences -->
                <section class="space-y-6">
                    <h2
                        class="text-3xl font-bold uppercase tracking-tight text-foreground border-l-8 border-main pl-6"
                    >
                        {{ $t("pages.cancellation.consequences_title") }}
                    </h2>
                    <div
                        class="prose prose-lg prose-invert max-w-none text-muted-foreground/90 whitespace-pre-wrap leading-relaxed"
                    >
                        {{ $t("pages.cancellation.consequences_content") }}
                    </div>
                </section>

                <Separator class="opacity-20" />

                <!-- Exclusion -->
                <section class="space-y-6">
                    <h2
                        class="text-3xl font-bold uppercase tracking-tight text-foreground border-l-8 border-main pl-6"
                    >
                        {{ $t("pages.cancellation.exclusion_title") }}
                    </h2>
                    <div
                        class="prose prose-lg prose-invert max-w-none text-muted-foreground/90 whitespace-pre-wrap leading-relaxed"
                    >
                        {{ $t("pages.cancellation.exclusion_content") }}
                    </div>
                </section>

                <Separator class="opacity-20" />

                <!-- Withdrawal Form -->
                <section class="bg-muted p-8 border-2 border-border space-y-6">
                    <h2
                        class="text-2xl font-bold uppercase tracking-tight text-foreground"
                    >
                        {{ $t("pages.cancellation.form_title") }}
                    </h2>
                    <div
                        class="prose prose-md prose-invert max-w-none text-muted-foreground/90 whitespace-pre-wrap leading-relaxed font-mono text-sm"
                    >
                        {{ $t("pages.cancellation.form_content") }}
                    </div>
                </section>
            </div>

            <!-- Footer Note -->
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
