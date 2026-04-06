<template>
    <div
        class="max-w-3xl mx-auto px-6 py-16 sm:py-24 selection:bg-accent_dark selection:text-primary-foreground"
    >
        <header class="mb-12 border-b-4 border-accent_dark pb-6">
            <h1
                class="text-4xl sm:text-6xl font-accent_dark uppercase tracking-tighter italic"
            >
                IMPRESSUM
            </h1>
        </header>

        <div class="space-y-12">
            <section class="space-y-4">
                <p
                    class="text-xs font-bold uppercase tracking-widest text-muted-foreground"
                >
                    Angaben gemäß § 5 TMG
                </p>
                <div
                    class="text-xl sm:text-2xl font-accent_dark uppercase tracking-tight"
                >
                    PIEPJACK Clothing
                </div>
                <div
                    class="text-lg leading-relaxed text-gray-800 dark:text-gray-300"
                >
                    Inhaber: Marvin Pieprzyk<br />
                    Schollendamm 122a<br />
                    27751 Delmenhorst<br />
                    Deutschland
                </div>
            </section>

            <Separator class="bg-accent_dark h-[1px]" />

            <section class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <h2
                        class="text-xs font-bold uppercase tracking-widest text-muted-foreground italic underline decoration-2 underline-offset-4"
                    >
                        Kontakt
                    </h2>
                    <div class="flex flex-col space-y-1">
                        <a
                            href="mailto:info@piepjack-clothing.de"
                            class="text-lg font-bold hover:bg-accent_dark hover:text-primary-foreground transition-colors w-fit px-1 -ml-1"
                        >
                            info@piepjack-clothing.de
                        </a>
                        <a
                            href="https://www.piepjack-clothing.de"
                            target="_blank"
                            class="text-lg font-bold hover:bg-accent_dark hover:text-primary-foreground transition-colors w-fit px-1 -ml-1"
                        >
                            www.piepjack-clothing.de
                        </a>
                    </div>
                </div>

                <div class="space-y-2">
                    <h2
                        class="text-xs font-bold uppercase tracking-widest text-muted-foreground italic underline decoration-2 underline-offset-4"
                    >
                        Verantwortlich (§ 55 RStV)
                    </h2>
                    <p class="text-lg font-bold uppercase leading-tight">
                        Marvin Pieprzyk<br />
                        Schollendamm 122a<br />
                        27751 Delmenhorst
                    </p>
                </div>
            </section>

            <Separator class="bg-accent_dark h-[1px]" />

            <section
                class="bg-accent_light border-l-8 border-accent_dark p-8 rounded-none"
            >
                <h2 class="text-xs font-bold uppercase tracking-widest mb-3">
                    Markenrechtlicher Hinweis
                </h2>
                <p class="text-lg leading-snug font-medium italic">
                    PIEPJACK ist eine eingetragene Marke
                    <span class="font-accent_dark">(Nr. 30 2025 105 590)</span>
                    beim Deutschen Patent- und Markenamt.
                </p>
            </section>
        </div>

        <div class="mt-20">
            <Button
                variant="ghost"
                as-child
                class="rounded-none px-0 hover:bg-transparent group"
            >
                <router-link to="/" class="flex items-center gap-2">
                    <span
                        class="text-xs font-accent_dark uppercase tracking-tighter border-b-2 border-accent_dark group-hover:bg-accent_dark group-hover:text-primary-foreground transition-all"
                    >
                        Zurück zur Startseite
                    </span>
                </router-link>
            </Button>
        </div>
    </div>
</template>

<script setup>
import { Separator } from "@/components/ui/separator";
import { Button } from "@/components/ui/button";
</script>
