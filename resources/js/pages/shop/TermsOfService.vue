<template>
    <PageLayout
        title="Terms of Service"
        description="General terms and conditions and customer information for Piepjack Clothing."
    >
        <template #header>
            <div class="border-b-[12px] border-accent_dark pb-8">
                <h1 class="text-5xl sm:text-7xl md:text-8xl font-accent_dark uppercase tracking-tighter leading-none italic">
                    TERMS OF <br />
                    SERVICE
                </h1>
                <p class="mt-4 text-sm font-bold uppercase tracking-[0.2em] text-muted-foreground">
                    Allgemeine Geschäftsbedingungen & Kundeninformationen
                </p>
            </div>
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-16">
            <aside
                class="hidden lg:block lg:col-span-1 sticky top-24 h-fit space-y-4"
            >
                <div
                    class="text-xs font-accent_dark uppercase tracking-widest border-l-4 border-accent_dark pl-4 py-1"
                >
                    Navigation
                </div>
                <nav
                    class="flex flex-col space-y-2 text-xs font-bold uppercase"
                >
                    <a
                        href="#section-1"
                        class="hover:underline underline-offset-4"
                        >§ 1 Bestimmungen</a
                    >
                    <a
                        href="#section-2"
                        class="hover:underline underline-offset-4"
                        >§ 2 Vertragsschluss</a
                    >
                    <a
                        href="#section-3"
                        class="hover:underline underline-offset-4"
                        >§ 3 Umtausch</a
                    >
                    <a
                        href="#section-4"
                        class="hover:underline underline-offset-4"
                        >§ 4 Eigentum</a
                    >
                    <a
                        href="#info"
                        class="hover:underline underline-offset-4 text-muted-foreground"
                        >Kundeninfo</a
                    >
                </nav>
            </aside>

            <main class="lg:col-span-3 space-y-12 text-pretty">
                <section class="space-y-8">
                    <h2
                        class="text-2xl font-accent_dark uppercase bg-primary text-primary-foreground px-4 py-2 w-fit rounded-none"
                    >
                        I. Allgemeine Geschäftsbedingungen
                    </h2>

                    <div id="section-1" class="space-y-4">
                        <h3
                            class="text-lg font-accent_dark uppercase italic tracking-tight"
                        >
                            § 1 Grundlegende Bestimmungen
                        </h3>
                        <div
                            class="space-y-4 text-gray-800 dark:text-gray-300 leading-relaxed"
                        >
                            <p>
                                (1) Die nachstehenden Geschäftsbedingungen
                                gelten für Verträge, die Sie mit uns als
                                Anbieter
                                <span
                                    class="font-bold underline decoration-2 underline-offset-2"
                                    >(Marvin Pieprzyk)</span
                                >
                                über diese Internetseite schließen.
                            </p>
                            <p>
                                (2)
                                <span
                                    class="font-bold uppercase text-[10px] bg-muted px-1"
                                    >Verbraucher</span
                                >
                                ist jede natürliche Person, die ein
                                Rechtsgeschäft zu Zwecken abschließt, die
                                überwiegend weder ihrer gewerblichen noch ihrer
                                selbständigen beruflichen Tätigkeit zugerechnet
                                werden kann.
                            </p>
                        </div>
                    </div>

                    <Separator class="bg-gray-200" />

                    <div id="section-2" class="space-y-4">
                        <h3
                            class="text-lg font-accent_dark uppercase italic tracking-tight"
                        >
                            § 2 Zustandekommen des Vertrages
                        </h3>
                        <p
                            class="text-gray-800 dark:text-gray-300 leading-relaxed"
                        >
                            (1) Gegenstand des Vertrages ist der Verkauf von
                            Waren. Unsere Angebote im Internet sind
                            unverbindlich und stellen kein verbindliches Angebot
                            zum Abschluss eines Vertrages dar.
                        </p>
                    </div>

                    <Separator class="bg-gray-200" />

                    <div
                        id="section-3"
                        class="space-y-4 bg-accent_light p-6 border-l-4 border-accent_dark rounded-none"
                    >
                        <h3
                            class="text-lg font-accent_dark uppercase italic tracking-tight text-red-600"
                        >
                            § 3 Umtausch und Rückgabe
                        </h3>
                        <div class="space-y-3 text-sm font-medium">
                            <p>
                                (1) Ein Umtausch ist eine freiwillige
                                Serviceleistung. Ein Rechtsanspruch besteht
                                nicht.
                            </p>
                            <p>
                                (2) Bei mangelhafter Ware übernehmen wir die
                                Rücksendekosten und leisten Ersatz.
                            </p>
                            <p class="font-bold underline italic">
                                (3) Hygiene-Hinweis: Versiegelte Waren sind vom
                                Widerruf ausgeschlossen, sobald die Versiegelung
                                entfernt wurde.
                            </p>
                        </div>
                    </div>

                    <div id="section-4" class="space-y-4">
                        <h3
                            class="text-lg font-accent_dark uppercase italic tracking-tight"
                        >
                            § 4 Eigentumsvorbehalt
                        </h3>
                        <p
                            class="text-gray-800 dark:text-gray-300 leading-relaxed"
                        >
                            (1) Die Ware bleibt bis zur vollständigen Zahlung
                            des Kaufpreises unser Eigentum.
                        </p>
                    </div>
                </section>

                <section
                    id="info"
                    class="pt-12 border-t-2 border-accent_dark space-y-6"
                >
                    <h2 class="text-2xl font-accent_dark uppercase italic">
                        II. Kundeninformationen
                    </h2>

                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 gap-8 bg-primary text-primary-foreground p-8 rounded-none"
                    >
                        <div class="space-y-1">
                            <p
                                class="text-[10px] font-bold text-gray-400 uppercase tracking-widest"
                            >
                                Anbieter
                            </p>
                            <p class="text-lg font-bold uppercase">
                                PIEPJACK Clothing
                            </p>
                            <p class="text-sm">Inhaber: Marvin Pieprzyk</p>
                        </div>
                        <div class="space-y-1">
                            <p
                                class="text-[10px] font-bold text-gray-400 uppercase tracking-widest"
                            >
                                Support
                            </p>
                            <p class="text-lg font-bold">TEL: 015756421797</p>
                            <p class="text-sm underline italic">
                                info@piepjack-clothing.de
                            </p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <h4
                            class="font-accent_dark uppercase text-sm italic underline decoration-1 underline-offset-4"
                        >
                            Alternative Streitbeilegung
                        </h4>
                        <p class="text-sm text-gray-600">
                            Die EU-Kommission bietet eine Plattform zur
                            Online-Streitbeilegung:
                            <a
                                href="https://ec.europa.eu/consumers/odr"
                                target="_blank"
                                class="font-bold hover:bg-accent_dark hover:text-primary-foreground px-1 transition-colors"
                            >
                                https://ec.europa.eu/consumers/odr
                            </a>
                        </p>
                    </div>
                </section>

                <footer class="pt-12">
                    <p
                        class="text-[10px] font-accent_dark uppercase tracking-widest text-gray-400"
                    >
                        Stand: 29.08.2023 // PIEPJACK LEGAL DEPT.
                    </p>
                </footer>
            </main>
        </div>

        <div class="mt-20 border-t border-gray-100 pt-8">
            <Button variant="ghost" as-child class="rounded-none px-0 group">
                <router-link to="/" class="flex items-center gap-2">
                    <span
                        class="text-xs font-accent_dark uppercase border-b-2 border-accent_dark group-hover:bg-accent_dark group-hover:text-primary-foreground transition-all"
                    >
                        Zurück zum Shop
                    </span>
                </router-link>
            </Button>
        </div>
    </PageLayout>
</template>

<script setup>
import PageLayout from "@components/shop/general/PageLayout.vue";
import { Separator } from "@/components/ui/separator";
import { Button } from "@/components/ui/button";
</script>
