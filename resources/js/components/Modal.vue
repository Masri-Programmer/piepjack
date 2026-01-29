<template>
    <transition name="fade">
        <div
            v-show="open"
            class="fixed z-999 inset-0 flex items-center justify-center overflow-hidden"
        >
            <div class="fixed inset-0 transition-opacity">
                <div class="absolute inset-0 bg-zinc-800 opacity-75"></div>
            </div>

            <div
                class="bg-accent rounded-border text-left shadow-xl transform transition-all sm:w-11/12 md:max-w-2xl lg:max-w-3xl xl:max-w-3xl 2xl:max-w-6xl sm:max-w-full relative overflow-auto max-h-[95vh] p-4 m-4"
                ref="modalConent"
                :class="props.class"
            >
                <X
                    class="absolute right-0 top-0 h-6 w-6 bg-gray-100 hover:bg-gray-400 text-main text-lg rounded-border cursor-pointer m-3 opacity-55"
                    @click="$emit('close')"
                />
                <slot />
            </div>
        </div>
    </transition>
</template>

<script setup>
import { onClickOutside } from "@vueuse/core";
import { ref } from "vue";
import { X } from "lucide-vue-next";

const props = defineProps({
    id: { type: String, default: "exampleModal" },
    open: { type: Boolean, default: false },
    class: { type: String, default: "" },
});
const emit = defineEmits(["close"]);
const modalConent = ref(null);

onClickOutside(modalConent, () => {
    if (props.open) {
        emit("close");
    }
});
</script>
