<template>
    <div
        class="max-w-6xl mx-auto px-6 py-16 sm:py-24 selection:bg-accent_dark selection:text-primary-foreground"
    >
        <header class="mb-16 border-b-[12px] border-accent_dark pb-8">
            <h1
                class="text-4xl sm:text-6xl md:text-7xl font-accent_dark uppercase tracking-tighter leading-none italic break-words"
            >
                DATENSCHUTZ-<br />ERKLÄRUNG
            </h1>
            <p
                class="mt-4 text-sm font-bold uppercase tracking-[0.2em] text-muted-foreground"
            >
                Privacy Policy // Stand: {{ LAST_UPDATED }}
            </p>
        </header>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-16">
            <aside
                class="hidden lg:block lg:col-span-1 sticky top-24 h-fit space-y-4"
            >
                <div
                    class="text-xs font-accent_dark uppercase tracking-widest border-l-4 border-accent_dark pl-4 py-1"
                >
                    Inhalt
                </div>
                <nav
                    class="flex flex-col space-y-2 text-xs font-bold uppercase"
                >
                    <a
                        href="#section-1"
                        class="hover:underline underline-offset-4"
                        >1. Verantwortlich</a
                    >
                    <a
                        href="#section-2"
                        class="hover:underline underline-offset-4"
                        >2. Datenverarbeitung</a
                    >
                    <a
                        href="#section-3"
                        class="hover:underline underline-offset-4"
                        >3. Dritte</a
                    >
                    <a
                        href="#section-4"
                        class="hover:underline underline-offset-4"
                        >4. Speicherung</a
                    >
                    <a
                        href="#section-5"
                        class="hover:underline underline-offset-4"
                        >5. Ihre Rechte</a
                    >
                    <a
                        href="#section-6"
                        class="hover:underline underline-offset-4"
                        >6. Cookies</a
                    >
                    <a
                        href="#section-7"
                        class="hover:underline underline-offset-4"
                        >7. Sicherheit</a
                    >
                </nav>
            </aside>

            <main class="lg:col-span-3 space-y-12 text-pretty">
                <p
                    class="text-xl font-medium leading-relaxed border-l-4 border-accent_dark pl-6"
                >
                    Wir von
                    <span class="font-accent_dark uppercase">{{
                        COMPANY_NAME
                    }}</span>
                    nehmen den Schutz Ihrer persönlichen Daten sehr ernst. Diese
                    Datenschutzerklärung informiert Sie über die Erhebung,
                    Verarbeitung und den Schutz Ihrer personenbezogenen Daten.
                </p>

                <section id="section-1" class="space-y-4">
                    <h2
                        class="text-xl font-accent_dark uppercase bg-primary text-primary-foreground px-4 py-2 w-fit rounded-none"
                    >
                        1. Verantwortliche Stelle
                    </h2>
                    <div
                        class="flex flex-col space-y-2 text-gray-800 dark:text-gray-300 font-medium"
                    >
                        <p class="text-lg font-accent_dark uppercase">
                            {{ COMPANY_NAME }}
                        </p>
                        <p class="flex items-center gap-2">
                            <Mail class="w-4 h-4" />
                            <a
                                :href="'mailto:' + COMPANY_EMAIL"
                                class="hover:bg-accent_dark hover:text-primary-foreground px-1 -ml-1 transition-colors"
                            >
                                {{ COMPANY_EMAIL }}
                            </a>
                        </p>
                        <p class="flex items-center gap-2">
                            <Phone class="w-4 h-4" />
                            {{ COMPANY_PHONE }}
                        </p>
                    </div>
                </section>

                <Separator class="bg-gray-200" />

                <section id="section-2" class="space-y-6">
                    <h2
                        class="text-xl font-accent_dark uppercase italic tracking-tight"
                    >
                        2. Erhebung und Verarbeitung
                    </h2>
                    <div class="space-y-4">
                        <div
                            class="p-4 border border-muted rounded-none bg-accent_light"
                        >
                            <p
                                class="text-xs font-accent_dark uppercase tracking-widest text-muted-foreground mb-1"
                            >
                                Beim Besuch der Website
                            </p>
                            <p class="text-sm font-medium">
                                IP-Adresse, Browsertyp, Betriebssystem,
                                Zugriffszeit, besuchte Seiten.
                            </p>
                        </div>
                        <div
                            class="p-4 border border-muted rounded-none bg-accent_light"
                        >
                            <p
                                class="text-xs font-accent_dark uppercase tracking-widest text-muted-foreground mb-1"
                            >
                                Beim Einkauf
                            </p>
                            <p class="text-sm font-medium">
                                Name, Adresse, E-Mail, Telefonnummer,
                                Zahlungsdaten.
                            </p>
                        </div>
                        <div
                            class="p-4 border border-muted rounded-none bg-accent_light"
                        >
                            <p
                                class="text-xs font-accent_dark uppercase tracking-widest text-muted-foreground mb-1"
                            >
                                Bei Kontaktaufnahme
                            </p>
                            <p class="text-sm font-medium">
                                Name, E-Mail, Telefonnummer, Nachrichteninhalt.
                            </p>
                        </div>
                    </div>
                </section>

                <Separator class="bg-gray-200" />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <section id="section-3" class="space-y-4">
                        <h2
                            class="text-xl font-accent_dark uppercase italic tracking-tight"
                        >
                            3. Weitergabe an Dritte
                        </h2>
                        <ul class="space-y-2 text-sm font-medium">
                            <li class="flex gap-2">
                                <span class="text-main font-accent_dark"
                                    >→</span
                                >
                                Versanddienstleister (z.B. DHL, Hermes)
                            </li>
                            <li class="flex gap-2">
                                <span class="text-main font-accent_dark"
                                    >→</span
                                >
                                Zahlungsanbieter (z.B. PayPal)
                            </li>
                            <li class="flex gap-2">
                                <span class="text-main font-accent_dark"
                                    >→</span
                                >
                                IT-Dienstleister (Website-Wartung)
                            </li>
                        </ul>
                    </section>

                    <section id="section-4" class="space-y-4">
                        <h2
                            class="text-xl font-accent_dark uppercase italic tracking-tight"
                        >
                            4. Speicherdauer
                        </h2>
                        <p class="text-sm font-medium leading-relaxed">
                            Ihre Daten werden nur so lange gespeichert, wie es
                            erforderlich oder gesetzlich vorgeschrieben ist.
                        </p>
                    </section>
                </div>

                <Separator class="bg-gray-200" />

                <section
                    id="section-5"
                    class="space-y-4 bg-primary text-primary-foreground p-8 rounded-none"
                >
                    <h2
                        class="text-xl font-accent_dark uppercase italic tracking-tight text-primary-foreground border-b border-gray-700 pb-4"
                    >
                        5. Ihre Rechte
                    </h2>
                    <ul class="space-y-3 text-sm font-medium mt-4">
                        <li class="flex items-center gap-3">
                            <span
                                class="h-1.5 w-1.5 bg-background rounded-none"
                            ></span>
                            Recht auf Auskunft, Berichtigung und Löschung
                        </li>
                        <li class="flex items-center gap-3">
                            <span
                                class="h-1.5 w-1.5 bg-background rounded-none"
                            ></span>
                            Recht auf Datenübertragbarkeit
                        </li>
                        <li class="flex items-center gap-3">
                            <span
                                class="h-1.5 w-1.5 bg-background rounded-none"
                            ></span>
                            Recht auf Widerspruch gegen die Datenverarbeitung
                        </li>
                    </ul>
                </section>

                <section id="section-6" class="space-y-4">
                    <h2
                        class="text-xl font-accent_dark uppercase italic tracking-tight"
                    >
                        6. Cookies und Tracking
                    </h2>
                    <p class="text-sm font-medium">
                        Unsere Website verwendet Cookies zur Optimierung der
                        Nutzererfahrung.
                    </p>
                    <div class="flex flex-wrap gap-2 mt-2">
                        <span
                            class="text-xs font-bold uppercase bg-muted px-3 py-1 border border-accent_dark rounded-none"
                            >Notwendige Cookies</span
                        >
                        <span
                            class="text-xs font-bold uppercase bg-muted px-3 py-1 border border-accent_dark rounded-none"
                            >Analyse-Cookies</span
                        >
                    </div>
                </section>

                <section id="section-7" class="space-y-4">
                    <h2
                        class="text-xl font-accent_dark uppercase italic tracking-tight"
                    >
                        7. Datensicherheit
                    </h2>
                    <p
                        class="text-sm font-medium leading-relaxed border-l-2 border-accent_dark pl-4"
                    >
                        Wir setzen technische und organisatorische Maßnahmen
                        ein, um Ihre Daten zu schützen
                        <span class="font-bold"
                            >(z. B. SSL-Verschlüsselung)</span
                        >.
                    </p>
                </section>

                <section class="space-y-4 text-muted-foreground">
                    <h2
                        class="text-sm font-accent_dark uppercase tracking-widest"
                    >
                        8. Änderungen
                    </h2>
                    <p class="text-xs font-medium">
                        Diese Datenschutzerklärung kann bei rechtlichen oder
                        technischen Änderungen aktualisiert werden.
                    </p>
                </section>
            </main>
        </div>

        <div
            class="mt-20 border-t border-muted pt-8 flex justify-between items-center"
        >
            <Button
                variant="ghost"
                as-child
                class="rounded-none px-0 group hover:bg-transparent"
            >
                <router-link to="/" class="flex items-center gap-2">
                    <span
                        class="text-xs font-accent_dark uppercase border-b-2 border-accent_dark group-hover:bg-accent_dark group-hover:text-primary-foreground transition-all"
                    >
                        Zurück zum Shop
                    </span>
                </router-link>
            </Button>
            <span
                class="text-[10px] font-accent_dark uppercase tracking-widest text-gray-400"
            >
                PIEPJACK LEGAL DEPT.
            </span>
        </div>
    </div>
</template>

<script setup>
import { Separator } from "@/components/ui/separator";
import { Button } from "@/components/ui/button";
import { Mail, Phone } from "lucide-vue-next";

// Data
const COMPANY_NAME = "Piepjack Clothing";
const COMPANY_EMAIL = "info@piepjack-clothing.de";
const COMPANY_PHONE = "015756421797";
const LAST_UPDATED = "01.01.2025";
</script>
