<template>
    <div
        class="launch-countdown min-h-screen bg-neutral-950 flex flex-col items-center justify-center p-4 relative overflow-hidden font-manrope selection:bg-main/20 selection:text-white"
    >
        <!-- Branding Logo -->
        <router-link
            to="/"
            class="logo-link"
            v-motion-fade
            style="position: absolute; top: 18px; left: 18px; z-index: 20"
        >
            <img :src="logoCircle" alt="Piepjack Logo" class="h-16" />
        </router-link>

        <!-- Background Accents -->
        <div
            class="absolute top-0 left-0 w-full h-full pointer-events-none overflow-hidden"
        >
            <div
                class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-main/5 blur-[90px] rounded-full"
            ></div>
            <div
                class="absolute -bottom-[10%] -right-[10%] w-[40%] h-[40%] bg-accent/5 blur-[90px] rounded-full"
            ></div>
            <div
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-full h-full bg-[radial-gradient(circle_at_center,rgba(255,255,255,0.02)_0,transparent_100%)]"
            ></div>
        </div>

        <!-- Content -->
        <div class="relative z-10 w-full max-w-4xl text-center space-y-12">
            <div class="space-y-4" v-motion-fade>
                <div
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full border border-white/10 bg-white/5 backdrop-blur-md text-white/60 text-xs font-semibold uppercase tracking-widest animate-pulse"
                >
                    <span class="w-2 h-2 rounded-full bg-main"></span>
                    {{ isLaunched ? "Kollektion ist Live" : "Demnächst" }}
                </div>
                <h1
                    class="text-5xl md:text-8xl font-bold text-white tracking-tighter"
                >
                    P I E P J A C K
                </h1>
                <p
                    class="text-white/40 text-sm md:text-lg max-w-lg mx-auto font-medium"
                >
                    {{
                        isLaunched
                            ? "Das Warten hat ein Ende. Entdecke die Kollektion."
                            : "Die neue Kollektion ist fast da. Bereite dich auf den Drop vor."
                    }}
                </p>
            </div>

            <!-- Countdown Grid -->
            <div
                v-if="!isLaunched"
                class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-8 pt-8 px-4"
                :class="{ 'heartbeat-active': isHeartbeat }"
                v-motion-slide-bottom
            >
                <div
                    v-for="(val, unit) in timeGroups"
                    :key="unit"
                    class="group"
                >
                    <div
                        class="relative bg-white/[0.03] border border-white/10 rounded-2xl md:rounded-3xl p-6 md:p-8 backdrop-blur-xl transition-all duration-500 group-hover:bg-white/[0.05] group-hover:border-white/20"
                    >
                        <div
                            class="text-4xl md:text-6xl font-black text-white group-hover:scale-105 transition-transform duration-500"
                        >
                            {{ val.toString().padStart(2, "0") }}
                        </div>
                        <div
                            class="text-[10px] md:text-xs font-bold text-white/30 uppercase tracking-[0.2em] mt-2 group-hover:text-main/60 transition-colors"
                        >
                            {{ translateUnit(unit) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Registration Form Area -->
            <div
                v-if="!isLaunched"
                v-motion-fade
                class="max-w-md mx-auto pt-10 space-y-6"
            >
                <div v-if="!isRegistered" class="space-y-4">
                    <p
                        class="text-white/40 text-xs font-bold tracking-[0.2em] uppercase"
                    >
                        Registrieren, um benachrichtigt zu werden
                    </p>
                    <form @submit.prevent="register" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <input
                                v-model="form.name"
                                type="text"
                                placeholder="Name"
                                class="bg-white/5 border border-white/10 rounded-xl px-5 py-3 text-white text-sm placeholder:text-white/20 focus:outline-none focus:border-main transition-colors"
                                required
                            />
                            <input
                                v-model="form.email"
                                type="email"
                                placeholder="E-Mail"
                                class="bg-white/5 border border-white/10 rounded-xl px-5 py-3 text-white text-sm placeholder:text-white/20 focus:outline-none focus:border-main transition-colors"
                                required
                            />
                        </div>
                        <button
                            type="submit"
                            :disabled="isSubmitting"
                            class="w-full bg-white text-neutral-950 py-3 rounded-xl font-black text-sm hover:bg-main hover:text-white transition-all disabled:opacity-50 active:scale-[0.98]"
                        >
                            {{
                                isSubmitting
                                    ? "SENDEN..."
                                    : "BEI DROP BENACHRICHTIGEN"
                            }}
                        </button>
                    </form>
                    <p
                        v-if="errorMsg"
                        class="text-rose-500 text-[10px] font-bold uppercase tracking-wider"
                    >
                        {{ errorMsg }}
                    </p>
                </div>
                <div
                    v-else
                    class="bg-white/5 border border-white/10 rounded-2xl p-8 backdrop-blur-xl animate-in fade-in zoom-in duration-500"
                >
                    <div
                        class="w-12 h-12 bg-main/20 rounded-full flex items-center justify-center mx-auto mb-4"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="24"
                            height="24"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="3"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="text-main"
                        >
                            <path d="M20 6 9 17l-5-5" />
                        </svg>
                    </div>
                    <h3 class="text-white font-bold mb-2">Du bist dabei!</h3>
                    <p class="text-white/40 text-xs font-medium">
                        Wir haben deine Anmeldung erhalten und melden uns zum
                        Launch.
                    </p>
                </div>
            </div>

            <!-- Action Button (Visible when launched) -->
            <div class="pt-8" v-motion-fade>
                <router-link
                    v-if="isLaunched"
                    to="/"
                    class="inline-flex items-center gap-3 bg-white text-neutral-950 px-10 py-5 rounded-full text-base font-black hover:bg-main hover:text-white transition-all duration-500 group shadow-2xl shadow-white/5 active:scale-95"
                >
                    ZUM SHOP
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="20"
                        height="20"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="transition-transform group-hover:translate-x-1"
                    >
                        <path d="M5 12h14" />
                        <path d="m12 5 7 7-7 7" />
                    </svg>
                </router-link>

                <div
                    v-else
                    class="text-white/20 text-[10px] md:text-xs font-bold uppercase tracking-[0.2em]"
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
        <!-- Footer Stats -->
        <div
            class="absolute bottom-12 left-0 w-full px-12 flex flex-col md:flex-row justify-between items-center gap-4 text-white/20 text-[10px] font-bold uppercase tracking-[0.3em] z-10"
        >
            <div>© 2026 PIEPJACK CLOTHING</div>
            <div class="flex gap-8">
                <a
                    href="https://www.instagram.com/piepjack"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="social-media-button"
                    aria-label="Besuche uns auf Instagram"
                >
                    <Instagram />
                </a>

                <a
                    href="https://www.tiktok.com/@piepjack"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="social-media-button"
                    aria-label="Besuche uns auf TikTok"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="20"
                        height="20"
                        fill="currentColor"
                        class="bi bi-tiktok"
                        viewBox="0 0 16 16"
                    >
                        <path
                            d="M9 0h1.98c.144.715.54 1.617 1.235 2.512C12.895 3.389 13.797 4 15 4v2c-1.753 0-3.07-.814-4-1.829V11a5 5 0 1 1-5-5v2a3 3 0 1 0 3 3z"
                        />
                    </svg>
                </a>

                <a
                    href="https://Facebook.com/piepjack"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="social-media-button"
                    aria-label="Besuche uns auf Facebook"
                >
                    <Facebook />
                </a>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { useRouter } from "vue-router";
import confetti from "canvas-confetti";
import logoCircle from "@img/logo-new.png";
import { Facebook, Instagram } from "lucide-vue-next";
import axios from "axios";
import { apiRequest } from "@lib/helpers";

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

const register = async () => {
    isSubmitting.value = true;
    errorMsg.value = "";
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
            // Trigger backend notification emails
            apiRequest("post", "/trigger-online-notification")
                .catch((e) => console.error("Notification trigger failed", e));
        }
        isHeartbeat.value = false;
        countdown.value = { days: 0, hours: 0, minutes: 0, seconds: 0 };
        if (timer) clearInterval(timer);
        return;
    }

    if (distance <= 10000) {
        isHeartbeat.value = true;
    } else {
        isHeartbeat.value = false;
    }

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
    console.log("Triggering Confetti...");
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
    const isLaunchEnabled = import.meta.env.VITE_LAUNCH_PAGE_ENABLED === "true";

    // If we are past launch or launch is disabled, we should redirect to home
    // EXCEPT if we are on the /launch page itself and just want to show the celebration
    if (isPastLaunch) {
        isLaunched.value = true;
        // Don't auto-redirect if we just hit zero, let user see the button
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
.launch-countdown {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 9999;
}

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
    border-color: rgba(244, 63, 94, 0.5); /* rose-500 */
    box-shadow: 0 0 30px rgba(244, 63, 94, 0.15);
}

.heartbeat-active .group > div > div:first-child {
    color: #f43f5e; /* rose-500 */
    text-shadow: 0 0 20px rgba(244, 63, 94, 0.4);
}
</style>
