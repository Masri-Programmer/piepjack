<template>
    <div
        class="max-w-5xl mx-auto px-6 py-16 sm:py-24 selection:bg-main selection:text-accent"
    >
        <header
            class="mb-20 border-b-[12px] border-border pb-10 text-center md:text-left"
        >
            <p
                class="mb-4 text-xs font-bold uppercase tracking-[0.3em] text-muted-foreground"
            >
                The Vision // The Story
            </p>
            <h1
                class="text-6xl sm:text-7xl md:text-9xl font-bold uppercase tracking-tighter leading-[0.8] italic text-foreground"
            >
                ABOUT US
            </h1>
        </header>

        <div class="space-y-24">
            <section
                class="bg-main text-accent p-8 sm:p-12 lg:p-16 border-4 border-border rounded-none relative"
            >
                <h2
                    class="text-3xl sm:text-5xl lg:text-6xl font-bold uppercase italic leading-[0.9] tracking-tighter mb-8"
                >
                    At
                    <span class="underline decoration-4 underline-offset-8"
                        >Piepjack</span
                    >, we believe in the power of progress.
                </h2>
                <div
                    class="space-y-6 text-lg sm:text-2xl font-bold uppercase tracking-widest text-accent/80"
                >
                    <p>
                        Every step, every challenge, every moment is an
                        opportunity to grow.
                    </p>
                    <p class="text-accent">
                        Don’t just dream - make it happen.
                    </p>
                    <p>We’re here to help you reach new heights.</p>
                    <p class="italic pt-4 border-t-2 border-accent/20">
                        Keep pushing. The journey starts now.
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
                        Vor über 10 Jahren habe ich
                        <span class="font-bold uppercase tracking-widest italic"
                            >Piepjack</span
                        >
                        ins Leben gerufen – nicht als Business, sondern als
                        Vision. Während andere ihren Weg gegangen sind, habe ich
                        mein Logo auf ein Blatt Papier gezeichnet – wieder und
                        wieder.
                    </p>
                    <p class="text-xl sm:text-2xl">
                        Es war mehr als nur eine Skizze. Es war ein Versprechen
                        an mich selbst:<br />
                        <span
                            class="font-bold italic uppercase tracking-wider text-main border-b-4 border-main pb-1 inline-block mt-2"
                        >
                            "Ich will jemand sein, an den man sich erinnert."
                        </span>
                    </p>
                    <p>
                        Die Zeit verging, und ich konnte damals nicht das zu
                        Ende bringen, womit ich begonnen habe – aber das werde
                        ich jetzt. Denn Piepjack ist nicht nur eine Marke. Es
                        ist ein Statement:
                    </p>

                    <ul
                        class="py-8 space-y-6 font-bold uppercase text-xl sm:text-3xl tracking-tighter italic border-y-2 border-border"
                    >
                        <li class="flex items-center gap-6">
                            <div
                                class="p-2 bg-main text-accent rounded-none shrink-0"
                            >
                                <Zap
                                    class="w-6 h-6 sm:w-8 sm:h-8"
                                    stroke-width="2"
                                />
                            </div>
                            Gib niemals auf.
                        </li>
                        <li class="flex items-center gap-6">
                            <div
                                class="p-2 bg-main text-accent rounded-none shrink-0"
                            >
                                <Zap
                                    class="w-6 h-6 sm:w-8 sm:h-8"
                                    stroke-width="2"
                                />
                            </div>
                            Behalte ein positives Mindset.
                        </li>
                        <li class="flex items-center gap-6 text-main">
                            <div
                                class="p-2 bg-main text-accent rounded-none shrink-0"
                            >
                                <Zap
                                    class="w-6 h-6 sm:w-8 sm:h-8"
                                    stroke-width="2"
                                />
                            </div>
                            CHASE YOUR PASSION.
                        </li>
                    </ul>

                    <p>
                        Piepjack steht für all die Träumer, die Macher, die
                        Kämpfer da draußen. Für Menschen, die an sich glauben –
                        auch wenn der Weg hart ist. Für die, die etwas
                        hinterlassen wollen.
                    </p>
                    <p
                        class="font-bold uppercase tracking-widest text-2xl pt-4"
                    >
                        Willkommen bei Piepjack. <br class="hidden sm:block" />
                        Lass uns gemeinsam Geschichte schreiben.
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
                        JOIN THE MOVEMENT
                        <ArrowRight
                            class="w-8 h-8 transition-transform duration-300 group-hover:translate-x-4"
                            stroke-width="2.5"
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
