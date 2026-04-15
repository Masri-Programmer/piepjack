<template>
    <div class="breadcrumb-container">
        <div class="readcrumb flex flex-wrap items-center">
            <template v-for="(crumb, index) in data" :key="index">
                <div class="breadcrumb-item">
                    <router-link
                        v-if="crumb.link && !crumb.disabled"
                        :to="crumb.link"
                        :class="[
                            crumb.current ? 'text-gray underline' : 'text-main',
                            'hover:text-gray transition-colors',
                        ]"
                    >
                        {{ crumb.title }}
                    </router-link>
                    <button
                        v-else-if="crumb.action && !crumb.disabled"
                        @click="crumb.action"
                        :class="[
                            crumb.current ? 'text-gray underline' : 'text-main',
                            'hover:text-gray transition-colors cursor-pointer border-none bg-transparent p-0 font-inherit',
                        ]"
                    >
                        {{ crumb.title }}
                    </button>
                    <span
                        v-else
                        :class="[
                            crumb.current
                                ? 'text-main font-bold'
                                : 'text-gray-400',
                            'cursor-default',
                        ]"
                    >
                        {{ crumb.title }}
                    </span>
                </div>
                <ChevronRight
                    v-if="index < data.length - 1"
                    size="20"
                    class="mx-2 text-gray-300"
                />
            </template>
        </div>
    </div>
</template>

<script setup>
import { ChevronRight } from "lucide-vue-next";
const props = defineProps({ data: { type: Array } });
</script>
