<template>
    <form
        @submit.prevent="$emit('submit')"
        class="w-full max-w-2xl mx-auto space-y-8 animate-in fade-in duration-500"
    >
        <slot name="header">
            <h1
                v-if="title"
                class="text-2xl font-bold uppercase tracking-tight text-foreground"
            >
                {{ title }}
            </h1>
        </slot>

        <div class="space-y-6 sm:space-y-8">
            <slot />
        </div>

        <slot name="footer" v-if="!hideFooter">
            <div
                class="flex flex-col-reverse sm:flex-row justify-between items-center pt-8 border-t border-border gap-4 mt-8"
            >
                <div class="w-full sm:w-auto">
                    <Button
                        v-if="prevRoute"
                        as-child
                        variant="ghost"
                        class="w-full sm:w-auto rounded-none hover:bg-transparent px-0 group"
                        :disabled="nextLoading"
                    >
                        <router-link
                            :to="prevRoute"
                            class="flex items-center justify-center gap-2"
                        >
                            <ChevronLeft
                                :size="20"
                                class="transition-transform group-hover:-translate-x-1"
                            />
                            <span class="text-xs font-bold uppercase">{{
                                prevLabel
                            }}</span>
                        </router-link>
                    </Button>

                    <Button
                        v-else-if="prevLabel"
                        type="button"
                        variant="ghost"
                        class="w-full sm:w-auto rounded-none hover:bg-transparent px-0 group"
                        :disabled="nextLoading"
                        @click="$emit('prev')"
                    >
                        <ChevronLeft
                            :size="20"
                            class="transition-transform group-hover:-translate-x-1"
                        />
                        <span class="text-xs font-bold uppercase">{{
                            prevLabel
                        }}</span>
                    </Button>
                </div>

                <Button
                    type="submit"
                    :disabled="nextDisabled || nextLoading"
                    class="view-all w-full sm:w-auto"
                >
                    <span v-if="nextLoading">{{
                        $t("common.forms.processing") || "Processing..."
                    }}</span>
                    <span v-else>{{ nextLabel }}</span>
                </Button>
            </div>
        </slot>
    </form>
</template>

<script setup>
import { ChevronLeft } from "lucide-vue-next";
import { Button } from "@/components/ui/button";

defineProps({
    title: { type: String, default: "" },
    prevLabel: { type: String, default: "" },
    nextLabel: { type: String, default: "Next" },
    prevRoute: { type: [String, Object], default: null }, // If passed, uses router-link instead of emitting prev
    nextDisabled: { type: Boolean, default: false },
    nextLoading: { type: Boolean, default: false },
    hideFooter: { type: Boolean, default: false },
});

defineEmits(["prev", "submit"]);
</script>
