<template>
    <div
        class="launch-countdown min-h-dvh bg-accent_black text-foreground flex flex-col items-center justify-center p-4 relative overflow-x-hidden font-manrope selection:bg-primary/20 selection:text-primary-foreground"
    >
        <router-link
            to="/"
            class="logo-link absolute top-4 left-4 md:top-6 md:left-6 z-20 animate-in fade-in duration-700"
        >
            <img
                :src="logoCircle"
                alt="Piepjack Logo"
                class="h-10 md:h-16 rounded-none"
            />
        </router-link>

        <div
            class="absolute top-0 left-0 w-full h-full pointer-events-none overflow-hidden"
        >
            <div
                class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-primary/5 blur-[90px] rounded-none"
            ></div>
            <div
                class="absolute -bottom-[10%] -right-[10%] w-[40%] h-[40%] bg-secondary/10 blur-[90px] rounded-none"
            ></div>
        </div>

        <div
            class="relative z-10 w-full max-w-4xl text-center space-y-12 my-auto pt-24 pb-12"
        >
            <div
                class="space-y-4 animate-in slide-in-from-bottom-4 fade-in duration-700"
            >
                <div
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-none border border-border bg-secondary/50 backdrop-blur-md text-foreground/80 text-xs font-semibold uppercase tracking-widest animate-pulse"
                >
                    <span class="w-2 h-2 rounded-none bg-primary"></span>
                    {{ isLaunched ? "Kollektion ist Live" : "Demnächst" }}
                </div>
                <h1
                    class="text-5xl md:text-8xl font-bold text-primary tracking-tighter uppercase"
                >
                    P I E P J A C K
                </h1>
                <p
                    class="text-muted-foreground text-sm md:text-lg max-w-lg mx-auto font-medium"
                >
                    {{
                        isLaunched
                            ? "Das Warten hat ein Ende. Entdecke die Kollektion."
                            : "Die neue Kollektion ist fast da. Bereite dich auf den Drop vor."
                    }}
                </p>
            </div>

            <div
                v-if="!isLaunched"
                class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-8 pt-8 px-4 animate-in slide-in-from-bottom-8 fade-in duration-1000"
                :class="{ 'heartbeat-active': isHeartbeat }"
            >
                <div
                    v-for="(val, unit) in timeGroups"
                    :key="unit"
                    class="group"
                >
                    <div
                        class="relative bg-muted/20 border border-border rounded-none p-6 md:p-8 backdrop-blur-xl transition-all duration-500 group-hover:bg-muted/40 group-hover:border-primary/50"
                    >
                        <div
                            class="text-4xl md:text-6xl font-bold text-foreground group-hover:scale-105 transition-transform duration-500"
                        >
                            {{ val.toString().padStart(2, "0") }}
                        </div>
                        <div
                            class="text-[10px] md:text-xs font-bold text-muted-foreground uppercase tracking-[0.2em] mt-2 group-hover:text-primary transition-colors"
                        >
                            {{ translateUnit(unit) }}
                        </div>
                    </div>
                </div>
            </div>

            <div
                v-if="!isLaunched"
                class="max-w-md mx-auto pt-10 space-y-6 animate-in fade-in duration-1000 delay-150"
            >
                <div v-if="!isRegistered" class="space-y-4 px-4 md:px-0">
                    <p
                        class="text-muted-foreground text-xs font-bold tracking-[0.2em] uppercase"
                    >
                        Registrieren, um benachrichtigt zu werden
                    </p>
                    <form
                        @submit.prevent="register"
                        class="space-y-4"
                        novalidate
                    >
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <Input
                                v-model="form.name"
                                type="text"
                                placeholder="Name"
                                class="rounded-none bg-background/50 border-border px-5 py-3 text-foreground text-sm placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-primary focus-visible:ring-offset-0 transition-all h-12"
                                required
                            />
                            <Input
                                v-model="form.email"
                                type="email"
                                placeholder="E-Mail"
                                class="rounded-none bg-background/50 border-border px-5 py-3 text-foreground text-sm placeholder:text-muted-foreground focus-visible:ring-1 focus-visible:ring-primary focus-visible:ring-offset-0 transition-all h-12"
                                required
                            />
                        </div>
                        <Button
                            type="submit"
                            :disabled="isSubmitting"
                            class="w-full rounded-none bg-primary text-primary-foreground h-12 font-bold uppercase tracking-widest hover:bg-primary/90 transition-all disabled:opacity-50 active:scale-[0.98]"
                        >
                            {{
                                isSubmitting
                                    ? "SENDEN..."
                                    : "BEI DROP BENACHRICHTIGEN"
                            }}
                        </Button>
                    </form>
                    <p
                        v-if="errorMsg"
                        class="text-destructive text-[10px] font-bold uppercase tracking-wider text-left"
                    >
                        {{ errorMsg }}
                    </p>
                </div>

                <div
                    v-else
                    class="bg-secondary/30 border border-border rounded-none p-8 backdrop-blur-xl animate-in zoom-in-95 duration-500 mx-4 md:mx-0"
                >
                    <div
                        class="w-12 h-12 bg-primary/10 border border-primary/20 rounded-none flex items-center justify-center mx-auto mb-4"
                    >
                        <Check class="w-6 h-6 text-primary" stroke-width="3" />
                    </div>
                    <h3 class="text-foreground font-bold mb-2">
                        Du bist dabei!
                    </h3>
                    <p class="text-muted-foreground text-xs font-medium">
                        Wir haben deine Anmeldung erhalten und melden uns zum
                        Launch.
                    </p>
                </div>
            </div>

            <div class="pt-8 animate-in fade-in duration-1000 delay-300">
                <router-link
                    v-if="isLaunched"
                    to="/"
                    class="inline-flex items-center gap-3 bg-primary text-primary-foreground px-10 py-5 rounded-none text-base font-bold hover:bg-primary/90 transition-all duration-500 group shadow-2xl active:scale-95 uppercase tracking-widest"
                >
                    ZUM SHOP
                    <ArrowRight
                        class="w-5 h-5 transition-transform group-hover:translate-x-1"
                        stroke-width="2.5"
                    />
                </router-link>

                <div
                    v-else
                    class="text-muted-foreground text-[10px] md:text-xs font-bold uppercase tracking-[0.2em]"
                >
                    {{
                        new Date(targetDate).toLocaleDateString("de-DE", {
                            day: "numeric",
                            month: "long",
                            year: "numeric",
                        })
                    }}
                    •
                    {{
                        new Date(targetDate).toLocaleTimeString("de-DE", {
                            hour: "2-digit",
                            minute: "2-digit",
                        })
                    }}
                    CET
                </div>
            </div>
        </div>

        <div
            class="w-full px-6 md:px-12 flex flex-col md:flex-row justify-between items-center gap-6 text-muted-foreground text-[10px] font-bold uppercase tracking-[0.3em] z-10 relative mt-auto pb-6"
        >
            <div>© 2026 PIEPJACK CLOTHING</div>
            <div class="flex gap-8">
                <a
                    href="https://www.instagram.com/piepjack"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="hover:text-primary transition-colors"
                    aria-label="Besuche uns auf Instagram"
                >
                    <Instagram class="w-5 h-5" />
                </a>

                <a
                    href="https://www.tiktok.com/@piepjack"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="hover:text-primary transition-colors"
                    aria-label="Besuche uns auf TikTok"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="20"
                        height="20"
                        fill="currentColor"
                        viewBox="0 0 16 16"
                    >
                        <path
                            d="M9 0h1.98c.144.715.54 1.617 1.235 2.512C12.895 3.389 13.797 4 15 4v2c-1.753 0-3.07-.814-4-1.829V11a5 5 0 1 1-5-5v2a3 3 0 1 0 3 3z"
                        />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useRouter } from "vue-router";
import confetti from "canvas-confetti";
import axios from "axios";
import { Instagram, Check, ArrowRight } from "lucide-vue-next";

// Helper & Assets
import logoCircle from "@img/logo-new.png";
import { apiRequest } from "@lib/helpers";

// shadcn-vue component imports (adjust path as needed for your specific setup)
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";

const router = useRouter();
const targetDate = new Date(
    import.meta.env.VITE_LAUNCH_DATE || "2026-05-01T18:00:00",
).getTime();

const countdown = ref({
    days: 0,
    hours: 0,
    minutes: 0,
    seconds: 0,
});

const isLaunched = ref(false);
const isHeartbeat = ref(false);
const isSubmitting = ref(false);
const isRegistered = ref(false);
const errorMsg = ref("");
const form = ref({
    name: "",
    email: "",
});

let timer = null;

const timeGroups = computed(() => ({
    days: countdown.value.days,
    hours: countdown.value.hours,
    minutes: countdown.value.minutes,
    seconds: countdown.value.seconds,
}));

const translateUnit = (unit) => {
    const map = {
        days: "Tage",
        hours: "Stunden",
        minutes: "Minuten",
        seconds: "Sekunden",
    };
    return map[unit] || unit;
};

const isValidEmail = (email) => {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
};

const register = async () => {
    errorMsg.value = "";

    if (!form.value.name.trim() || !form.value.email.trim()) {
        errorMsg.value = "Bitte fülle alle Felder aus.";
        return;
    }

    if (!isValidEmail(form.value.email)) {
        errorMsg.value = "Bitte gib eine gültige E-Mail-Adresse ein.";
        return;
    }

    isSubmitting.value = true;
    try {
        await axios.post("/api/V1/shop/launch-registration", form.value);
        isRegistered.value = true;
    } catch (error) {
        if (error.response?.data?.errors?.email) {
            errorMsg.value = error.response.data.errors.email[0];
        } else {
            errorMsg.value =
                "Etwas ist schiefgelaufen. Bitte versuche es später erneut.";
        }
    } finally {
        isSubmitting.value = false;
    }
};

const updateCountdown = () => {
    const now = new Date().getTime();
    const distance = targetDate - now;

    if (distance <= 0) {
        if (!isLaunched.value) {
            isLaunched.value = true;
            triggerConfetti();
            apiRequest("post", "/trigger-online-notification").catch((e) =>
                console.error("Notification trigger failed", e),
            );
        }
        isHeartbeat.value = false;
        countdown.value = { days: 0, hours: 0, minutes: 0, seconds: 0 };
        if (timer) clearInterval(timer);
        return;
    }

    isHeartbeat.value = distance <= 10000;

    countdown.value.days = Math.floor(distance / (1000 * 60 * 60 * 24));
    countdown.value.hours = Math.floor(
        (distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60),
    );
    countdown.value.minutes = Math.floor(
        (distance % (1000 * 60 * 60)) / (1000 * 60),
    );
    countdown.value.seconds = Math.floor((distance % (1000 * 60)) / 1000);
};

const triggerConfetti = () => {
    const duration = 7 * 1000;
    const animationEnd = Date.now() + duration;
    const defaults = {
        startVelocity: 30,
        spread: 360,
        ticks: 60,
        zIndex: 99999,
    };

    const randomInRange = (min, max) => Math.random() * (max - min) + min;

    const interval = setInterval(() => {
        const timeLeft = animationEnd - Date.now();

        if (timeLeft <= 0) {
            return clearInterval(interval);
        }

        const particleCount = 50 * (timeLeft / duration);

        confetti({
            ...defaults,
            particleCount,
            origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 },
        });
        confetti({
            ...defaults,
            particleCount,
            origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 },
        });
    }, 250);
};

onMounted(() => {
    const isPastLaunch = Date.now() >= targetDate;

    if (isPastLaunch) {
        isLaunched.value = true;
    } else {
        updateCountdown();
        timer = setInterval(updateCountdown, 1000);
    }
});

onUnmounted(() => {
    if (timer) clearInterval(timer);
});
</script>

<style scoped>
@keyframes heartbeat {
    0% {
        transform: scale(1);
    }
    15% {
        transform: scale(1.05);
    }
    30% {
        transform: scale(1);
    }
    45% {
        transform: scale(1.05);
    }
    100% {
        transform: scale(1);
    }
}

.heartbeat-active .group > div {
    animation: heartbeat 1s ease-in-out infinite;
    border-color: hsl(var(--destructive) / 0.5);
    box-shadow: 0 0 30px hsl(var(--destructive) / 0.15);
}

.heartbeat-active .group > div > div:first-child {
    color: hsl(var(--destructive));
    text-shadow: 0 0 20px hsl(var(--destructive) / 0.4);
}
</style>
