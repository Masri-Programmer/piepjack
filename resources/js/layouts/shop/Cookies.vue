<template>
    <transition name="fade-slide">
        <div
            v-if="!cookiesAccepted"
            class="fixed bottom-0 left-0 right-0 flex flex-col md:flex-row items-center justify-between gap-4 px-6 py-4 z-[100] bg-main text-white shadow-2xl border-t border-white/10"
            role="status"
            aria-live="polite"
        >
            <div
                class="text-accent text-[10px] md:text-xs font-black uppercase tracking-widest leading-relaxed max-w-2xl text-center md:text-left"
            >
                {{ $t("common.cookieNotification.message") }}
                <router-link
                    to="/datenschutzerklarung"
                    class="underline hover:text-white transition-colors ml-1"
                >
                    {{ $t("common.cookieNotification.learnMore") }}
                </router-link>
            </div>
            <Button
                @click="acceptCookies"
                variant="secondary"
                size="sm"
                class="view-all"
            >
                {{ $t("common.cookieNotification.understood") }}
            </Button>
        </div>
    </transition>
</template>

<script setup>
import { useStorage } from "@vueuse/core";
import { onMounted, ref } from "vue";
import { Button } from "@/components/ui/button";

const storageAccepted = useStorage("cookiesAccepted", false);
const cookiesAccepted = ref(true);

const acceptCookies = () => {
    storageAccepted.value = true;
    cookiesAccepted.value = true;
};

onMounted(() => {
    // Check storage and show with a small delay for better UX
    setTimeout(() => {
        cookiesAccepted.value = storageAccepted.value;
    }, 1000);
});
</script>

<style scoped>
.fade-slide-enter-active,
.fade-slide-leave-active {
    transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
}

.fade-slide-enter-from {
    opacity: 0;
    transform: translateY(100%);
}

.fade-slide-leave-to {
    opacity: 0;
    transform: translateY(20px);
}
</style>
