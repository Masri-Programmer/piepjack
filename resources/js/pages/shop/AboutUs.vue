<template>
    <PageLayout
        :title="$t('pages.about.vision_title')"
        :description="$t('pages.about.description')"
    >
        <template #header>
            <div
                class="border-b-[12px] border-border pb-10 text-center md:text-left"
            >
                <p
                    class="mb-4 text-xs font-bold uppercase tracking-[0.3em] text-muted-foreground"
                >
                    {{ $t("pages.about.vision_story_label") }}
                </p>
                <h1
                    class="text-6xl sm:text-7xl md:text-9xl font-bold uppercase tracking-tighter leading-[0.8] italic text-foreground"
                >
                    {{ $t("pages.about.title") }}
                </h1>
            </div>
        </template>

        <div class="space-y-24">
            <section
                class="bg-main text-accent p-8 sm:p-12 lg:p-16 border-4 border-border rounded-none relative"
            >
                <h2
                    class="text-3xl sm:text-5xl lg:text-6xl font-bold uppercase italic leading-[0.9] tracking-tighter mb-8"
                >
                    {{ $t("pages.about.quote.title") }}
                </h2>
                <div
                    class="space-y-6 text-lg sm:text-2xl font-bold uppercase tracking-widest text-accent/80"
                >
                    <p>
                        {{ $t("pages.about.quote.p1") }}
                    </p>
                    <p class="text-accent">
                        {{ $t("pages.about.quote.p2") }}
                    </p>
                    <p>{{ $t("pages.about.quote.p3") }}</p>
                    <p class="italic pt-4 border-t-2 border-accent/20">
                        {{ $t("pages.about.quote.footer") }}
                    </p>
                </div>
            </section>

            <section
                class="border-l-[12px] border-main pl-6 sm:pl-12 lg:pr-24 py-4"
            >
                <div
                    class="space-y-8 text-lg sm:text-xl font-medium leading-relaxed text-foreground text-pretty"
                >
                    <p>
                        {{ $t("pages.about.story.p1") }}
                    </p>
                    <p class="text-xl sm:text-2xl">
                        {{ $t("pages.about.story.p2") }}<br />
                        <span
                            class="font-bold italic uppercase tracking-wider text-main border-b-4 border-main pb-1 inline-block mt-2"
                        >
                            {{ $t("pages.about.story.promise") }}
                        </span>
                    </p>
                    <p>
                        {{ $t("pages.about.story.p3") }}
                    </p>

                    <ul
                        class="py-8 space-y-6 font-bold uppercase text-xl sm:text-3xl tracking-tighter italic border-y-2 border-border"
                    >
                        <li
                            v-for="(value, index) in $tm(
                                'pages.about.story.values'
                            )"
                            :key="index"
                            class="flex items-center gap-6"
                            :class="{ 'text-main': index === 2 }"
                        >
                            <div
                                class="p-2 bg-main text-accent rounded-none shrink-0"
                            >
                                <Zap
                                    class="w-6 h-6 sm:w-8 sm:h-8"
                                    stroke-width="2"
                                />
                            </div>
                            {{ value }}
                        </li>
                    </ul>

                    <p>
                        {{ $t("pages.about.story.p4") }}
                    </p>
                    <p class="font-bold uppercase tracking-widest text-2xl pt-4">
                        {{ $t("pages.about.story.welcome") }}
                    </p>
                </div>
            </section>

            <section class="pt-12 border-t-4 border-border">
                <Button
                    as-child
                    class="view-all w-full sm:w-auto h-auto px-8 py-8 sm:px-16 sm:py-10 text-lg sm:text-xl font-bold uppercase tracking-widest flex items-center justify-center gap-4 group rounded-none"
                >
                    <a
                        href="https://www.piepjack-clothing.de/"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        {{ $t("pages.about.linkText") }}
                        <ArrowRight
                            class="w-8 h-8 transition-transform duration-300 group-hover:translate-x-4"
                            stroke-width="2.5"
                        />
                    </a>
                </Button>
            </section>
        </div>
    </PageLayout>
</template>

<script setup>
import PageLayout from "@components/shop/general/PageLayout.vue";
import { ArrowRight, Zap } from "lucide-vue-next";
import { Button } from "@/components/ui/button";
</script>
