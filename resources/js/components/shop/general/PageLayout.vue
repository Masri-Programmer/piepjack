<template>
    <div
        :class="[
            'page-container w-full min-h-screen font-avenir selection:bg-main selection:text-accent animate-in fade-in duration-500',
            customClass,
        ]"
    >
        <!-- Full Width Content (e.g. Hero Banners) -->
        <div v-if="$slots.fullWidth" class="w-full">
            <slot name="fullWidth" />
        </div>

        <!-- Standardized Page Wrapper -->
        <div
            :class="[
                !noContainer
                    ? 'max-w-[1440px] mx-auto px-6 sm:px-10 lg:px-16 xl:px-20 py-14 md:py-20 lg:py-24'
                    : '',
            ]"
        >
            <!-- Optional Page Header Slot (for titles, breadcrumbs etc) -->
            <header v-if="$slots.header" class="mb-12 md:mb-16">
                <slot name="header" />
            </header>

            <!-- Main Content Slot -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import { onMounted, watch } from "vue";
import { useTitle } from "@vueuse/core";

const props = defineProps({
    title: {
        type: String,
        default: "",
    },
    description: {
        type: String,
        default: "",
    },
    customClass: {
        type: String,
        default: "",
    },
    noContainer: {
        type: Boolean,
        default: false,
    },
});

const APP_NAME = "Piepjack";

/**
 * Dynamically update the meta description tag
 */
const updateMetaDescription = (desc) => {
    if (!desc) return;
    let meta = document.querySelector('meta[name="description"]');
    if (!meta) {
        meta = document.createElement("meta");
        meta.name = "description";
        document.head.appendChild(meta);
    }
    meta.setAttribute("content", desc);
};

onMounted(() => {
    // Set Document Title
    if (props.title) {
        useTitle(`${props.title} - ${APP_NAME}`);
    }

    // Set Meta Description
    if (props.description) {
        updateMetaDescription(props.description);
    }
});

// Watch for changes (useful for dynamic pages like products)
watch(
    () => props.title,
    (newTitle) => {
        if (newTitle) useTitle(`${newTitle} - ${APP_NAME}`);
    },
);

watch(
    () => props.description,
    (newDesc) => {
        updateMetaDescription(newDesc);
    },
);
</script>

<style scoped>
.font-avenir {
    font-family: "AvenirNext-Regular", "Inter", sans-serif;
}

/* Ensure smooth transitions between pages */
.page-container {
    opacity: 1;
    transition: opacity 0.3s ease-in-out;
}
</style>
