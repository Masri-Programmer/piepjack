<template>
    <img
        :src="computedSrc"
        :alt="alt || 'Product Image'"
        :loading="lazy ? 'lazy' : 'eager'"
        :decoding="lazy ? 'async' : 'auto'"
        :class="[
            'transition-opacity duration-300',
            fitClass,
            customClass
        ]"
        @error="handleError"
    />
</template>

<script setup>
import { computed, ref } from 'vue';
import NoImg from "@img/no-image.jpg";

const props = defineProps({
    src: { type: String, default: null },
    alt: { type: String, default: '' },
    lazy: { type: Boolean, default: true },
    fallback: { type: String, default: null },
    fit: { type: String, default: 'cover' }, // cover, contain, fill, scale-down
    customClass: { type: String, default: '' }
});

const hasError = ref(false);

const computedSrc = computed(() => {
    if (hasError.value) return props.fallback || NoImg;
    return props.src || props.fallback || NoImg;
});

const fitClass = computed(() => {
    const maps = {
        'cover': 'object-cover',
        'contain': 'object-contain',
        'fill': 'object-fill',
        'scale-down': 'object-scale-down'
    };
    return maps[props.fit] || 'object-cover';
});

const handleError = () => {
    hasError.value = true;
};
</script>
