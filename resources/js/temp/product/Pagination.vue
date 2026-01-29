<template>
    <div v-if="links.length > 3" class="flex items-center justify-center gap-2">
        <template v-for="(link, index) in links" :key="index">
            <a
                v-if="link.url"
                :href="link.url"
                class="px-4 py-2 text-sm rounded-md"
                :class="{
                    'bg-main text-white': link.active,
                    'bg-white text-gray-700 hover:bg-gray-100': !link.active,
                }"
                v-html="link.label"
            />
            <span
                v-else
                class="px-4 py-2 text-sm text-gray-400 cursor-not-allowed"
                v-html="link.label"
            />
        </template>
    </div>
</template>

<script setup>
defineProps({
    links: { type: Array, required: true },
});
</script>