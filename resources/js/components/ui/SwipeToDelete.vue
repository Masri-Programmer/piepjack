<template>
    <div
        ref="container"
        class="relative overflow-hidden w-full bg-destructive select-none"
    >
        <!-- Background Layer (The Delete Action) -->
        <div
            class="absolute inset-0 flex items-center justify-end px-6 text-destructive-foreground font-black uppercase italic tracking-tighter"
        >
            <div class="flex flex-col items-center animate-pulse">
                <Trash2 class="w-6 h-6 mb-1" />
                <span class="text-[10px]">{{ $t("common.remove") }}</span>
            </div>
        </div>

        <!-- Foreground Layer (The Content) -->
        <div
            ref="target"
            class="relative z-10 bg-background transition-transform duration-200 ease-out"
            :style="containerStyle"
        >
            <slot />

            <!-- Hint Animation for Unavailable Items -->
            <div
                v-if="showHint && isUnavailable"
                class="absolute inset-0 pointer-events-none border-2 border-destructive/20 animate-in fade-in duration-500"
            >
                <div
                    class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center gap-2 text-destructive animate-bounce-horizontal"
                >
                    <span class="text-[10px] font-black uppercase tracking-widest">{{ $t('pages.checkout.swipeToRemove') }}</span>
                    <ArrowLeft class="w-4 h-4" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { usePointerSwipe } from '@vueuse/core';
import { Trash2, ArrowLeft } from 'lucide-vue-next';

const props = defineProps({
    isUnavailable: {
        type: Boolean,
        default: false
    },
    threshold: {
        type: Number,
        default: 0.4 // 40% of width to trigger delete
    }
});

const emit = defineEmits(['delete']);

const container = ref(null);
const target = ref(null);
const showHint = ref(true);

const { distanceX, isSwiping } = usePointerSwipe(target, {
    disableTextSelect: true,
    onSwipeEnd(e, direction) {
        if (container.value) {
            const width = container.value.offsetWidth;
            // If swiped left more than threshold
            if (distanceX.value > width * props.threshold) {
                emit('delete');
            }
        }
    },
});

const containerStyle = computed(() => {
    if (!isSwiping.value) return { transform: 'translateX(0px)' };
    // Only allow swiping to the left (positive distanceX)
    const translateX = distanceX.value > 0 ? -distanceX.value : 0;
    return {
        transform: `translateX(${translateX}px)`,
    };
});

// Hint animation: slightly nudge the card to show it's swipeable
onMounted(() => {
    if (props.isUnavailable) {
        setTimeout(() => {
            if (target.value) {
                target.value.style.transition = 'transform 0.6s cubic-bezier(0.16, 1, 0.3, 1)';
                target.value.style.transform = 'translateX(-40px)';
                setTimeout(() => {
                    target.value.style.transform = 'translateX(0px)';
                    setTimeout(() => {
                        target.value.style.transition = 'transform 0.2s ease-out';
                    }, 600);
                }, 800);
            }
        }, 1000);
    }
});

// Hide hint once user starts interacting
watch(isSwiping, (val) => {
    if (val) showHint.value = false;
});
</script>

<style scoped>
@keyframes bounce-horizontal {
    0%, 100% { transform: translateX(0) translateY(-50%); }
    50% { transform: translateX(-10px) translateY(-50%); }
}
.animate-bounce-horizontal {
    animation: bounce-horizontal 2s infinite;
}
</style>
