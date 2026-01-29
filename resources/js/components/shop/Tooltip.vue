<template>
   <div class="absolute top-2 md:top-0 right-10 md:right-0">
        <span class="group relative">
          <div
            class=" absolute bottom-[calc(100%+0.5rem)] left-[50%] -translate-x-[50%] hidden group-hover:block w-auto"
          >
            <div
              class="bottom-full right-0 bg-black px-4 py-1 text-xs text-white z-999 md:whitespace-nowrap"
            >
             {{ content }}
              <svg
                class="absolute left-0 top-full h-2 w-full text-black"
                x="0px"
                y="0px"
                viewBox="0 0 255 255"
                xml:space="preserve"
              >
                <polygon class="fill-current" points="0,0 127.5,127.5 255,0" />
              </svg>
            </div>
          </div>
          <span><Info size="16"/></span>
        </span>
      </div>
</template>

<script setup>
import { Info } from 'lucide-vue-next';
const props=defineProps({
    content: String
})
</script>