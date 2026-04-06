<script setup>
defineProps({
    type: {
        type: String,
        default: "button",
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center border border-gray-300 bg-background px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 shadow-md transition duration-150 ease-in-out hover:bg-accent_light focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25"
    >
        <slot />
    </button>
</template>
