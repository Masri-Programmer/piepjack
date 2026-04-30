<template>
    <img
        :src="computedSrc"
        :alt="alt || 'Product Image'"
        :loading="lazy ? 'lazy' : 'eager'"
        :decoding="lazy ? 'async' : 'auto'"
        :class="cn('transition-opacity duration-300 transform-gpu backface-hidden', fitClass, customClass)"
        @error="handleError"
    />
</template>

<style scoped>
.transform-gpu {
    transform: translateZ(0);
    will-change: transform;
}
.backface-hidden {
    backface-visibility: hidden;
    -webkit-backface-visibility: hidden;
}
</style>

<script setup>
import { computed, ref } from "vue";
import NoImg from "@img/no-image.jpg";
import { cn } from "@/lib/utils";

const props = defineProps({
    src: { type: String, default: null },
    alt: { type: String, default: "" },
    lazy: { type: Boolean, default: true },
    fallback: { type: String, default: null },
    fit: { type: String, default: "cover" },
    customClass: { type: String, default: "" },
});

const hasError = ref(false);

const computedSrc = computed(() => {
    if (hasError.value) return props.fallback || NoImg;
    return props.src || props.fallback || NoImg;
});

const fitClass = computed(() => {
    const maps = {
        cover: "object-cover",
        contain: "object-contain",
        fill: "object-fill",
        "scale-down": "object-scale-down",
    };
    return maps[props.fit] || "object-cover";
});

const handleError = () => {
    hasError.value = true;
};
</script>
