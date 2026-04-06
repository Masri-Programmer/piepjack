<template>
    <div
        class="max-w-6xl mx-auto px-6 py-16 sm:py-24 selection:bg-accent_dark selection:text-primary-foreground"
    >
        <header class="mb-16 border-b-[12px] border-accent_dark pb-8">
            <h1
                class="text-4xl sm:text-6xl md:text-7xl font-accent_dark uppercase tracking-tighter leading-none italic break-words"
            >
                WIDERRUFS-<br />BELEHRUNG
            </h1>
            <p
                class="mt-4 text-sm font-bold uppercase tracking-[0.2em] text-muted-foreground"
            >
                Right of Withdrawal / Cancellation Policy
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
                        href="#recht"
                        class="hover:underline underline-offset-4 flex items-center gap-2"
                    >
                        <Undo2 class="w-3 h-3" /> 1. Widerrufsrecht
                    </a>
                    <a
                        href="#folgen"
                        class="hover:underline underline-offset-4 flex items-center gap-2"
                    >
                        <RefreshCcw class="w-3 h-3" /> 2. Folgen des Widerrufs
                    </a>
                    <a
                        href="#kosten"
                        class="hover:underline underline-offset-4 flex items-center gap-2"
                    >
                        <AlertCircle class="w-3 h-3" /> 3. Rücksendekosten
                    </a>
                    <a
                        href="#wertersatz"
                        class="hover:underline underline-offset-4 flex items-center gap-2"
                    >
                        <Box class="w-3 h-3" /> 4. Wertersatzpflicht
                    </a>
                </nav>
            </aside>

            <main class="lg:col-span-3 space-y-12 text-pretty">
                <div
                    class="p-6 bg-accent_light border border-muted rounded-none"
                >
                    <p
                        class="text-xs font-accent_dark uppercase tracking-widest text-muted-foreground mb-2"
                    >
                        Widerrufsrecht für Verbraucher
                    </p>
                    <p
                        class="text-sm font-medium leading-relaxed italic text-gray-700"
                    >
                        Verbraucher ist jede natürliche Person, die ein
                        Rechtsgeschäft zu Zwecken abschließt, die überwiegend
                        weder ihrer gewerblichen noch ihrer selbständigen
                        beruflichen Tätigkeit zugerechnet werden können.
                    </p>
                </div>

                <section id="recht" class="space-y-4">
                    <h2
                        class="text-xl font-accent_dark uppercase bg-primary text-primary-foreground px-4 py-2 w-fit rounded-none"
                    >
                        1. Widerrufsrecht
                    </h2>
                    <div
                        class="space-y-4 text-gray-800 dark:text-gray-300 font-medium leading-relaxed"
                    >
                        <p>
                            Sie haben das Recht, binnen
                            <span
                                class="font-accent_dark underline decoration-2 underline-offset-2"
                                >vierzehn Tagen</span
                            >
                            ohne Angabe von Gründen diesen Vertrag zu
                            widerrufen. Die Widerrufsfrist beträgt vierzehn Tage
                            ab dem Tag, an dem Sie oder ein von Ihnen benannter
                            Dritter, der nicht der Beförderer ist, die Waren in
                            Besitz genommen haben bzw. hat.
                        </p>
                        <p>
                            Um Ihr Widerrufsrecht auszuüben, müssen Sie uns
                            mittels einer eindeutigen Erklärung (z. B. ein mit
                            der Post versandter Brief oder E-Mail) über Ihren
                            Entschluss, diesen Vertrag zu widerrufen,
                            informieren.
                        </p>
                        <p class="text-sm border-l-2 border-accent_dark pl-4">
                            Zur Wahrung der Widerrufsfrist reicht es aus, dass
                            Sie die Mitteilung über die Ausübung des
                            Widerrufsrechts vor Ablauf der Widerrufsfrist
                            absenden.
                        </p>
                    </div>
                </section>

                <Separator class="bg-gray-200" />

                <section id="folgen" class="space-y-4">
                    <h2
                        class="text-xl font-accent_dark uppercase italic tracking-tight"
                    >
                        2. Folgen des Widerrufs
                    </h2>
                    <div
                        class="space-y-4 text-gray-800 dark:text-gray-300 font-medium leading-relaxed"
                    >
                        <p>
                            Wenn Sie diesen Vertrag widerrufen, haben wir Ihnen
                            alle Zahlungen, die wir von Ihnen erhalten haben,
                            einschließlich der Lieferkosten (mit Ausnahme der
                            zusätzlichen Kosten, die sich daraus ergeben, dass
                            Sie eine andere Art der Lieferung als die von uns
                            angebotene, günstigste Standardlieferung gewählt
                            haben), unverzüglich und spätestens binnen vierzehn
                            Tagen ab dem Tag zurückzuzahlen, an dem die
                            Mitteilung über Ihren Widerruf dieses Vertrags bei
                            uns eingegangen ist.
                        </p>
                        <p class="bg-muted p-4 border border-gray-300 text-sm">
                            Wir verwenden dasselbe Zahlungsmittel, das Sie bei
                            der ursprünglichen Transaktion eingesetzt haben. Wir
                            können die Rückzahlung verweigern, bis wir die Waren
                            wieder zurückerhalten haben oder bis Sie den
                            Nachweis erbracht haben, dass Sie die Waren
                            zurückgesandt haben.
                        </p>
                        <p>
                            Sie haben die Waren unverzüglich und in jedem Fall
                            spätestens binnen
                            <span class="font-accent_dark">vierzehn Tagen</span>
                            ab dem Tag, an dem Sie uns über den Widerruf dieses
                            Vertrags unterrichten, an uns zurückzusenden oder zu
                            übergeben. Die Frist ist gewahrt, wenn Sie die Waren
                            vor Ablauf der Frist absenden.
                        </p>
                    </div>
                </section>

                <Separator class="bg-gray-200" />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <section id="kosten" class="space-y-4">
                        <h2
                            class="text-xl font-accent_dark uppercase italic tracking-tight text-red-600"
                        >
                            3. Kosten der Rücksendung
                        </h2>
                        <div
                            class="border-l-8 border-red-600 bg-red-50 p-6 rounded-none"
                        >
                            <p
                                class="text-base font-accent_dark text-red-900 uppercase"
                            >
                                Der Kunde trägt die unmittelbaren Kosten der
                                Rücksendung der Waren.
                            </p>
                        </div>
                    </section>

                    <section id="wertersatz" class="space-y-4">
                        <h2
                            class="text-xl font-accent_dark uppercase italic tracking-tight"
                        >
                            4. Wertersatzpflicht
                        </h2>
                        <p class="text-sm font-medium leading-relaxed">
                            Der Kunde muss für einen etwaigen Wertverlust der
                            Waren nur aufkommen, wenn dieser auf einen zur
                            Prüfung der Beschaffenheit, Eigenschaften und
                            Funktionsweise der Waren
                            <span class="font-bold underline"
                                >nicht notwendigen Umgang</span
                            >
                            zurückzuführen ist.
                        </p>
                    </section>
                </div>
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
import { Undo2, RefreshCcw, AlertCircle, Box } from "lucide-vue-next";
</script>
