<script setup>
import PageLayout from "@components/shop/general/PageLayout.vue";
import { Separator } from "@/components/ui/separator";
import { Button } from "@/components/ui/button";
import { ArrowLeft } from "lucide-vue-next";
import { useI18n } from "vue-i18n";

const { tm, t } = useI18n();

// Filter out the 'title' key from sections object to get only numbered sections
const sections = Object.entries(tm("pages.tos.sections")).filter(
    ([key]) => key !== "title"
);

const customerInfo = tm("pages.tos.info");
</script>

<template>
    <PageLayout
        :title="$t('pages.tos.title')"
        :description="$t('pages.tos.description')"
    >
        <template #header>
            <div
                class="border-b-[12px] border-border pb-10 text-center md:text-left"
            >
                <p
                    class="mb-4 text-xs font-bold uppercase tracking-[0.3em] text-muted-foreground"
                >
                    {{ $t("pages.tos.last_updated") }}
                </p>
                <h1
                    class="text-6xl sm:text-7xl md:text-9xl font-bold uppercase tracking-tighter leading-[0.8] italic text-foreground"
                >
                    {{ $t("pages.tos.title") }}
                </h1>
            </div>
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-16">
            <!-- Sidebar Navigation -->
            <aside
                class="hidden lg:block lg:col-span-1 sticky top-24 h-fit space-y-4"
            >
                <div
                    class="text-xs font-bold uppercase tracking-widest border-l-4 border-main pl-4 py-1"
                >
                    Navigation
                </div>
                <nav
                    class="flex flex-col space-y-2 text-xs font-bold uppercase"
                >
                    <a
                        v-for="([key, section]) in sections"
                        :key="key"
                        :href="`#${key}`"
                        class="hover:underline underline-offset-4 transition-all hover:text-main"
                    >
                        {{ section.title }}
                    </a>
                    <a
                        href="#info"
                        class="hover:underline underline-offset-4 text-muted-foreground hover:text-main transition-all"
                    >
                        {{ $t("pages.tos.info.title") }}
                    </a>
                </nav>
            </aside>

            <!-- Main Content -->
            <main class="lg:col-span-3 space-y-16 text-pretty">
                <section class="space-y-12">
                    <h2
                        class="text-2xl font-bold uppercase bg-main text-accent px-6 py-3 w-fit tracking-tight"
                    >
                        {{ $t("pages.tos.sections.title") }}
                    </h2>

                    <!-- Dynamic Sections -->
                    <div
                        v-for="([key, section]) in sections"
                        :key="key"
                        :id="key"
                        class="space-y-6 group"
                    >
                        <h3
                            class="text-2xl font-bold uppercase italic tracking-tighter text-foreground group-hover:text-main transition-colors"
                        >
                            {{ section.title }}
                        </h3>
                        <div
                            class="space-y-4 text-lg font-medium leading-relaxed text-muted-foreground/90"
                        >
                            <p v-for="(p, pKey) in section" :key="pKey" v-show="pKey !== 'title'">
                                {{ p }}
                            </p>
                        </div>
                        <Separator class="bg-border/50" />
                    </div>
                </section>

                <!-- Customer Information Section -->
                <section
                    id="info"
                    class="pt-16 border-t-8 border-border space-y-10"
                >
                    <h2 class="text-4xl font-black uppercase italic tracking-tighter">
                        {{ customInfo?.title || $t('pages.tos.info.title') }}
                    </h2>

                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 gap-0 border-4 border-main"
                    >
                        <div class="p-8 bg-main text-accent space-y-2">
                            <p class="text-[10px] font-black uppercase tracking-[0.3em] opacity-60">
                                {{ $t("pages.tos.info.provider") }}
                            </p>
                            <p class="text-2xl font-black uppercase tracking-tighter">
                                PIEPJACK Clothing
                            </p>
                            <p class="text-sm font-bold italic opacity-80">
                                {{ $t("pages.tos.info.owner") }}
                            </p>
                        </div>
                        <div class="p-8 bg-background border-t-4 sm:border-t-0 sm:border-l-4 border-main space-y-2">
                            <p class="text-[10px] font-black uppercase tracking-[0.3em] text-muted-foreground">
                                {{ $t("pages.tos.info.support") }}
                            </p>
                            <p class="text-2xl font-black tracking-tighter text-foreground">
                                TEL: 015756421797
                            </p>
                            <a href="mailto:info@piepjack-clothing.de" class="text-sm font-bold italic underline decoration-2 underline-offset-4 text-main block">
                                info@piepjack-clothing.de
                            </a>
                        </div>
                    </div>

                    <!-- Dispute Resolution -->
                    <div class="space-y-4 p-8 bg-muted border-2 border-border">
                        <h4 class="font-bold uppercase text-lg tracking-tight">
                            {{ $t("pages.tos.info.dispute.title") }}
                        </h4>
                        <div class="text-base text-muted-foreground font-medium leading-relaxed">
                            {{ $t("pages.tos.info.dispute.p1") }}
                            <a
                                href="https://ec.europa.eu/consumers/odr"
                                target="_blank"
                                class="inline-block mt-2 font-black text-main hover:underline decoration-2 underline-offset-4"
                            >
                                https://ec.europa.eu/consumers/odr
                            </a>
                        </div>
                    </div>
                </section>

                <!-- Footer -->
                <footer class="pt-12 border-t-2 border-border flex justify-between items-center">
                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-muted-foreground">
                        {{ $t("pages.tos.footer") }}
                    </p>
                    <p class="text-[10px] font-black text-main">PIEPJACK // LEGAL</p>
                </footer>
            </main>
        </div>

        <!-- Back Button -->
        <div class="mt-20 pt-10 border-t-4 border-border">
            <Button variant="ghost" as-child class="rounded-none px-0 group">
                <router-link to="/" class="flex items-center gap-3">
                    <ArrowLeft class="w-5 h-5 transition-transform group-hover:-translate-x-1" />
                    <span class="text-xs font-bold uppercase tracking-widest border-b-2 border-foreground group-hover:bg-foreground group-hover:text-background transition-all">
                        {{ $t("pages.tos.backToShop") }}
                    </span>
                </router-link>
            </Button>
        </div>
    </PageLayout>
</template>
