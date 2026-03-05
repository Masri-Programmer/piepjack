<template>
    <div
        class="banner hidden bg-main text-accent text-xs md:flex justify-between gap-3 items-center h-14 px-3 md:px-12 flex-row-reverse md:flex-row"
    >
        <div
            class="flex justify-between items-center w-full grow flex-col md:flex-row sale-text-banner"
            :style="isMobile && { fontSize: '0.45em' }"
        >
            <span v-html="$t('layout.header.readyForDrop')"></span>
            <span>{{ $t("layout.header.newCollectionDropsIn") }}</span>
            <span>{{ formattedTime }}</span>
        </div>
        <router-link to="/off-the-radar">
            <button
                class="off-the-radar hoverBtn h-8 mx-2 md:mx-5 sm:max-w-40 whitespace-nowrap text-xs"
                :style="isMobile && { fontSize: '0.45em' }"
            >
                {{ $t("components.buttons.offTheRadar") }}
            </button>
        </router-link>
    </div>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from "vue";
import { useI18n } from "vue-i18n";
import { useMediaQuery } from "@vueuse/core";
import confetti from "canvas-confetti";

const { t } = useI18n();
const countdown = ref({
    days: 0,
    hours: 0,
    minutes: 0,
});

const isMobile = useMediaQuery("(max-width: 640px)");
const formattedTime = computed(() => {
    return t("layout.header.countdown", {
        days: countdown.value.days,
        hours: countdown.value.hours,
        minutes: countdown.value.minutes,
    });
});

let timer = null;
const targetDate = new Date(import.meta.env.VITE_LAUNCH_DATE || "2026-05-01T18:00:00").getTime();

const hasFiredConfetti = ref(false);

const updateCountdown = () => {
    const now = new Date().getTime();
    const distance = targetDate - now;

    if (distance < 0) {
        countdown.value = { days: 0, hours: 0, minutes: 0 };
        if (!hasFiredConfetti.value) {
            hasFiredConfetti.value = true;
            triggerConfetti();
        }
        if (timer) clearInterval(timer);
        return;
    }

    countdown.value.days = Math.floor(distance / (1000 * 60 * 60 * 24));
    countdown.value.hours = Math.floor(
        (distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60),
    );
    countdown.value.minutes = Math.floor(
        (distance % (1000 * 60 * 60)) / (1000 * 60),
    );
};

const triggerConfetti = () => {
    const end = Date.now() + 5 * 1000;

    (function frame() {
        confetti({
            particleCount: 5,
            angle: 60,
            spread: 55,
            origin: { x: 0 },
        });
        confetti({
            particleCount: 5,
            angle: 120,
            spread: 55,
            origin: { x: 1 },
        });

        if (Date.now() < end) {
            requestAnimationFrame(frame);
        }
    })();
};

onMounted(() => {
    updateCountdown();
    timer = setInterval(updateCountdown, 1000); // Update every minute
});

onUnmounted(() => {
    if (timer) clearInterval(timer);
});
</script>
