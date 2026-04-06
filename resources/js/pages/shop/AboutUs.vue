<template>
    <div
        class="max-w-6xl mx-auto px-6 py-16 sm:py-24 selection:bg-accent_dark selection:text-[#f2f0a1]"
    >
        <header class="mb-16 border-b-[12px] border-accent_dark pb-8">
            <h1
                class="text-5xl sm:text-7xl md:text-8xl font-accent_dark uppercase tracking-tighter leading-none italic"
            >
                ABOUT US
            </h1>
            <p
                class="mt-4 text-sm font-bold uppercase tracking-[0.2em] text-muted-foreground"
            >
                The Vision // The Story
            </p>
        </header>

        <div class="space-y-16 sm:space-y-24">
            <section
                class="bg-primary text-primary-foreground p-8 sm:p-12 lg:p-16 rounded-none shadow-[8px_8px_0px_0px_#f2f0a1] border-2 border-accent_dark"
            >
                <h2
                    class="text-2xl sm:text-4xl lg:text-5xl font-accent_dark uppercase italic leading-tight tracking-tight"
                >
                    At <span class="text-[#f2f0a1]">Piepjack</span>, we believe
                    in the power of progress.
                </h2>
                <div
                    class="mt-8 space-y-4 text-lg sm:text-xl lg:text-2xl font-bold uppercase tracking-wide text-gray-300"
                >
                    <p>
                        Every step, every challenge, every moment is an
                        opportunity to grow.
                    </p>
                    <p class="text-primary-foreground">
                        Don’t just dream - make it happen.
                    </p>
                    <p>We’re here to help you reach new heights.</p>
                    <p class="text-[#f2f0a1] italic">
                        Keep pushing. The journey starts now.
                    </p>
                </div>
            </section>

            <section
                class="border-l-[12px] border-accent_dark pl-6 sm:pl-12 lg:pr-24"
            >
                <div
                    class="space-y-6 text-lg sm:text-xl font-medium leading-relaxed text-gray-800 dark:text-gray-300 text-pretty"
                >
                    <p>
                        Vor über 10 Jahren habe ich
                        <span class="font-accent_dark uppercase text-main"
                            >Piepjack</span
                        >
                        ins Leben gerufen – nicht als Business, sondern als
                        Vision. Während andere ihren Weg gegangen sind, habe ich
                        mein Logo auf ein Blatt Papier gezeichnet – wieder und
                        wieder.
                    </p>
                    <p>
                        Es war mehr als nur eine Skizze. Es war ein Versprechen
                        an mich selbst:
                        <span
                            class="font-bold italic underline decoration-2 underline-offset-4"
                            >Ich will jemand sein, an den man sich
                            erinnert.</span
                        >
                    </p>
                    <p>
                        Die Zeit verging, und ich konnte damals nicht das zu
                        Ende bringen, womit ich begonnen habe – aber das werde
                        ich jetzt. Denn Piepjack ist nicht nur eine Marke. Es
                        ist ein Statement:
                    </p>

                    <ul
                        class="py-6 space-y-4 font-accent_dark uppercase text-xl sm:text-2xl tracking-tighter"
                    >
                        <li class="flex items-center gap-4">
                            <Zap class="w-6 h-6 shrink-0" /> Gib niemals auf.
                        </li>
                        <li class="flex items-center gap-4">
                            <Zap class="w-6 h-6 shrink-0" /> Behalte ein
                            positives Mindset.
                        </li>
                        <li class="flex items-center gap-4">
                            <Zap
                                class="w-6 h-6 shrink-0 text-main fill-accent_dark"
                            />
                            CHASE YOUR PASSION.
                        </li>
                    </ul>

                    <p>
                        Piepjack steht für all die Träumer, die Macher, die
                        Kämpfer da draußen. Für Menschen, die an sich glauben –
                        auch wenn der Weg hart ist. Für die, die etwas
                        hinterlassen wollen.
                    </p>
                    <p class="font-accent_dark uppercase text-2xl pt-4">
                        Willkommen bei Piepjack. <br class="hidden sm:block" />
                        Lass uns gemeinsam Geschichte schreiben.
                    </p>
                </div>
            </section>

            <section class="pt-8">
                <Button
                    as-child
                    class="rounded-none bg-[#f2f0a1] text-main hover:bg-accent_dark hover:text-primary-foreground border-4 border-accent_dark px-8 py-10 sm:px-12 sm:py-12 text-sm sm:text-lg font-accent_dark uppercase tracking-widest transition-all group w-full sm:w-auto"
                >
                    <a
                        href="https://www.piepjack-clothing.de/"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center justify-center gap-4"
                    >
                        JOIN THE MOVEMENT
                        <ArrowRight
                            class="w-6 h-6 transition-transform group-hover:translate-x-2"
                            stroke-width="3"
                        />
                    </a>
                </Button>
            </section>
        </div>
    </div>
</template>

<script setup>
import { Button } from "@/components/ui/button";
import { Zap, ArrowRight } from "lucide-vue-next";
</script>
