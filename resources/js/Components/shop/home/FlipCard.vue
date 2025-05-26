<template>
  <div :class="cn('group h-72 w-56 perspective-[1000px]', props.class)">
    <div
      :class="
        cn(
          'relative h-full transition-all duration-500 transform-3d',
          rotation[0],
        )
      "
    >
      <!-- Front -->
      <div class="absolute size-full backface-hidden">
        <img
          :src="props.image"
          alt="image"
          class="size-full object-cover shadow-2xl shadow-black/40"
        />
        <div class="absolute bottom-4 left-4 text-xl font-bold text-accent">
          {{ props.title }}
        </div>
      </div>

      <!-- Back -->
      <div
        :class="
          cn(
            'absolute h-full w-full bg-black/80 p-4 text-main backface-hidden',
            rotation[1],
          )
        "
      >
        <div class="flex min-h-full flex-col">
          <h1 class="text-xl font-bold text-main">{{ props.subtitle }}</h1>
          <p
            class="mt-1 border-t border-t-gray py-4 text-base font-medium leading-normal text-gray"
          >
            {{ props.description }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { cn } from "@lib/utils.ts";
import { computed } from "vue";
interface FlipCardProps {
  image: string;
  title: string;
  subtitle?: string;
  description: string;
  rotate?: "x" | "y";
  class?: string;
}

const props = withDefaults(defineProps<FlipCardProps>(), {
  rotate: "y",
});
const rotationClass = {
  x: ["group-hover:[transform:rotateX(180deg)]", "[transform:rotateX(180deg)]"],
  y: ["group-hover:[transform:rotateY(180deg)]", "[transform:rotateY(180deg)]"],
};

const rotation = computed(() => rotationClass[props.rotate]);
</script>