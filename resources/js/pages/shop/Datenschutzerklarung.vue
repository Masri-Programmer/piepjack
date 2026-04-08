<script setup>
import PageLayout from "@components/shop/general/PageLayout.vue";
import { Separator } from "@/components/ui/separator";
import { Button } from "@/components/ui/button";
import { Mail, Phone, ArrowLeft } from "lucide-vue-next";

// Data (could be localized too, but for now we keep them here if needed, 
// though we use translations for labels)
const COMPANY_NAME = "Piepjack Clothing";
const COMPANY_EMAIL = "info@piepjack-clothing.de";
const COMPANY_PHONE = "015756421797";
</script>

<template>
    <PageLayout
        :title="$t('pages.privacy.title')"
        :description="$t('pages.privacy.description')"
    >
        <template #header>
            <div
                class="border-b-[12px] border-border pb-10 text-center md:text-left"
            >
                <p
                    class="mb-4 text-xs font-bold uppercase tracking-[0.3em] text-muted-foreground"
                >
                    {{ $t("pages.privacy.last_updated") }}
                </p>
                <h1
                    class="text-6xl sm:text-7xl md:text-9xl font-bold uppercase tracking-tighter leading-[0.8] italic text-foreground"
                >
                    {{ $t("pages.privacy.title") }}
                </h1>
            </div>
        </template>

        <div class="max-w-4xl space-y-16">
            <section class="prose prose-xl prose-invert max-w-none">
                <p class="text-muted-foreground leading-relaxed text-pretty">
                    {{ $t("pages.privacy.intro") }}
                </p>
            </section>

            <div class="space-y-12">
                <!-- Responsible Body -->
                <section class="space-y-4">
                    <h2 class="text-2xl font-bold uppercase tracking-tight text-foreground">
                        {{ $t("pages.privacy.responsible.title") }}
                    </h2>
                    <div class="space-y-4 text-muted-foreground/90 font-medium">
                        <p>{{ $t("pages.privacy.responsible.description") }}</p>
                        <div class="flex flex-col space-y-2 p-6 bg-muted border-2 border-border">
                            <p class="text-lg font-bold uppercase">{{ COMPANY_NAME }}</p>
                            <p class="flex items-center gap-2">
                                <Mail class="w-4 h-4 text-main" />
                                <a :href="'mailto:' + COMPANY_EMAIL" class="hover:underline">{{ COMPANY_EMAIL }}</a>
                            </p>
                            <p class="flex items-center gap-2">
                                <Phone class="w-4 h-4 text-main" />
                                {{ COMPANY_PHONE }}
                            </p>
                        </div>
                    </div>
                </section>

                <Separator class="opacity-20" />

                <!-- Data Collection -->
                <section class="space-y-6">
                    <h2 class="text-2xl font-bold uppercase tracking-tight text-foreground">
                        {{ $t("pages.privacy.dataCollection.title") }}
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="p-6 border-2 border-border space-y-2">
                            <h3 class="font-bold uppercase text-main text-sm">
                                {{ $t("pages.privacy.dataCollection.visit.title") }}
                            </h3>
                            <p class="text-sm text-muted-foreground">
                                {{ $t("pages.privacy.dataCollection.visit.description") }}
                            </p>
                        </div>
                        <div class="p-6 border-2 border-border space-y-2">
                            <h3 class="font-bold uppercase text-main text-sm">
                                {{ $t("pages.privacy.dataCollection.purchase.title") }}
                            </h3>
                            <p class="text-sm text-muted-foreground">
                                {{ $t("pages.privacy.dataCollection.purchase.description") }}
                            </p>
                        </div>
                        <div class="p-6 border-2 border-border space-y-2">
                            <h3 class="font-bold uppercase text-main text-sm">
                                {{ $t("pages.privacy.dataCollection.contact.title") }}
                            </h3>
                            <p class="text-sm text-muted-foreground">
                                {{ $t("pages.privacy.dataCollection.contact.description") }}
                            </p>
                        </div>
                    </div>
                </section>

                <Separator class="opacity-20" />

                <!-- Third Parties and Retention -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                    <section class="space-y-4">
                        <h2 class="text-2xl font-bold uppercase tracking-tight text-foreground">
                            {{ $t("pages.privacy.dataSharing.title") }}
                        </h2>
                        <ul class="space-y-2 text-muted-foreground font-medium">
                            <li v-for="(item, index) in $tm('pages.privacy.dataSharing.items')" :key="index" class="flex gap-3">
                                <span class="text-main">/</span> {{ item }}
                            </li>
                        </ul>
                    </section>
                    <section class="space-y-4">
                        <h2 class="text-2xl font-bold uppercase tracking-tight text-foreground">
                            {{ $t("pages.privacy.retention.title") }}
                        </h2>
                        <p class="text-muted-foreground font-medium">
                            {{ $t("pages.privacy.retention.description") }}
                        </p>
                    </section>
                </div>

                <Separator class="opacity-20" />

                <!-- Rights -->
                <section class="bg-primary text-primary-foreground p-8 space-y-6">
                    <h2 class="text-2xl font-bold uppercase tracking-tight">
                        {{ $t("pages.privacy.rights.title") }}
                    </h2>
                    <ul class="space-y-3 font-medium">
                        <li v-for="(right, index) in $tm('pages.privacy.rights.list')" :key="index" class="flex items-center gap-3">
                            <div class="w-1.5 h-1.5 bg-background"></div>
                            {{ right }}
                        </li>
                    </ul>
                </section>

                <!-- Cookies and Security -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                    <section class="space-y-4">
                        <h2 class="text-2xl font-bold uppercase tracking-tight text-foreground">
                            {{ $t("pages.privacy.cookies.title") }}
                        </h2>
                        <p class="text-muted-foreground text-sm">
                            {{ $t("pages.privacy.cookies.description") }}
                        </p>
                        <div class="flex gap-2">
                            <span class="text-[10px] font-bold uppercase bg-muted px-2 py-1 border border-border">
                                {{ $t("pages.privacy.cookies.necessary") }}
                            </span>
                            <span class="text-[10px] font-bold uppercase bg-muted px-2 py-1 border border-border">
                                {{ $t("pages.privacy.cookies.analytics") }}
                            </span>
                        </div>
                    </section>
                    <section class="space-y-4">
                        <h2 class="text-2xl font-bold uppercase tracking-tight text-foreground">
                            {{ $t("pages.privacy.security.title") }}
                        </h2>
                        <p class="text-muted-foreground text-sm border-l-4 border-main pl-4">
                            {{ $t("pages.privacy.security.description") }}
                        </p>
                    </section>
                </div>
            </div>

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
