<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    status: Number,
});

const title = computed(() => {
    return {
        503: "Service Unavailable",
        500: "Server Error",
        404: "Page Not Found",
        403: "Forbidden",
    }[props.status];
});

const description = computed(() => {
    return {
        503: "Sorry, we are doing some maintenance. Please check back soon.",
        500: "Whoops, something went wrong on our servers.",
        404: "Sorry, the page you are looking for could not be found.",
        403: "Sorry, you are forbidden from accessing this page.",
    }[props.status];
});
</script>

<template>
    <div
        class="flex items-center justify-center min-h-screen p-5 text-center bg-gray-100"
    >
        <div class="max-w-md">
            <h1 class="text-6xl font-bold text-gray-700">{{ status }}</h1>
            <p class="mt-4 text-2xl font-semibold text-gray-800">{{ title }}</p>
            <p class="mt-2 text-gray-600">{{ description }}</p>
            <div class="mt-6">
                <Link
                    href="/"
                    class="px-4 py-2 font-semibold text-white bg-blue-500 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    Go back home
                </Link>
            </div>
        </div>
    </div>
</template>
