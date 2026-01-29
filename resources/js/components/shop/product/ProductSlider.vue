<template>
  <!-- <div class="overflow-hidden"> -->
  <!-- <Carousel v-bind="settings" :breakpoints="breakpoints" class="vue-carousel"> -->
  <!-- <Slide
        v-for="color in variants[1].options"
        :key="color.id"
        class="carousel__item"
      > -->
  <div
    v-for="color in variants.find(
      (variant) => variant.name.toLowerCase() === 'color'
    )?.options"
    :key="color.id"
    class="flex items-center"
  >
    <button
      @click="handlePick('color', color.value)"
      :class="[
        selectedColor === color.value && 'border border-main p-2',
        !getAvailableColors.includes(color.value) && 'opacity-50',
      ]"
      class="flex items-center flex-col justify-center space-y-1"
    >
      <!-- <p class="text-center text-sm">{{ color.value }}</p> -->
      {{ color.value }}
      <div
        class="w-6 h-6 rounded-full"
        :style="{ backgroundColor: color.value }"
      ></div>
    </button>
    <!-- </Slide> -->
    <!-- <template #addons>
      <navigation />
    </template> -->
  </div>
  <!-- </Carousel> -->
  <!-- </div> -->
</template>
<script setup>
import { Carousel, Navigation, Slide } from "vue3-carousel";
import "vue3-carousel/dist/carousel.css";
const props = defineProps({
  variants: Array,
  selectedColor: Object,
  getAvailableColors: Array,
});
const emit = defineEmits("hanlde:pick");
const handlePick = (type, value) => {
  emit("handle:pick", type, value);
};
const settings = {
  itemsToShow: 3,
  itemsToScroll: 4,
  snapAlign: "center",
};

const breakpoints = {
  640: {
    itemsToShow: 1,
    itemsToScroll: 1,
  },
  768: {
    itemsToShow: 2,
    itemsToScroll: 2,
  },
  1024: {
    itemsToShow: 4,
    itemsToScroll: 4,
  },
};
</script>
<style scoped>
.carousel__prev,
.carousel__next {
  opacity: 1;
  z-index: 10;
  height: 1rem;
  padding: 1rem;
  display: block;
  font-size: 1rem;
  border: 0 !important;
  background-size: 0.5em;
  border-radius: 50%;
  background-repeat: no-repeat;
  background-position: center center;
}

.carousel__prev::before,
.carousel__next::before {
  top: 50%;
  left: 50%;
  opacity: 0;
  content: "";
  width: 2rem;
  z-index: -1;
  height: 2rem;
  position: absolute;
  border-radius: 50%;
  background-color: white;
  transform: translate(-50%, -50%);
  background-position: center;
  background-repeat: no-repeat;
  transition: transform 0.5s ease-in-out;
  box-shadow: 2px 4px 15px 13px rgba(0, 0, 0, 0.17);
  -webkit-box-shadow: 2px 4px 15px 13px rgba(0, 0, 0, 0.17);
}
.carousel__prev::after,
.carousel__next::after {
  opacity: 1;
}
.vue-carousel:hover .carousel__prev::before,
.vue-carousel:hover .carousel__next::before,
.vue-carousel:hover .carousel__prev,
.vue-carousel:hover .carousel__next {
  opacity: 1;
}
.carousel__prev::before {
  background-image: url(data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9ImN1cnJlbnRDb2xvciIgc3Ryb2tlLXdpZHRoPSIxIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiIGNsYXNzPSJsdWNpZGUgbHVjaWRlLWNoZXZyb24tbGVmdCI+PHBhdGggZD0ibTE1IDE4LTYtNiA2LTYiLz48L3N2Zz4=);
}
.carousel__next::before {
  background-image: url(data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSJub25lIiBzdHJva2U9ImN1cnJlbnRDb2xvciIgc3Ryb2tlLXdpZHRoPSIxIiBzdHJva2UtbGluZWNhcD0icm91bmQiIHN0cm9rZS1saW5lam9pbj0icm91bmQiIGNsYXNzPSJsdWNpZGUgbHVjaWRlLWNoZXZyb24tcmlnaHQiPjxwYXRoIGQ9Im05IDE4IDYtNi02LTYiLz48L3N2Zz4=);
}

.carousel__next:hover::before {
  animation: move-bg 0.4s ease-in-out 1;
  background-position: center;
}
.carousel__prev:hover::before {
  animation: move-bg-left 0.4s ease-in-out 1;
  background-position: center;
}
@keyframes move-bg {
  0% {
    background-position: 0 center;
  }
  50% {
    background-position: 200% center;
  }
  51% {
    background-position: -200% center;
  }
  100% {
    background-position: 50% center;
  }
}
@keyframes move-bg-left {
  0% {
    background-position: 0 center;
  }
  50% {
    background-position: -200% center;
  }
  51% {
    background-position: 200% center;
  }
  100% {
    background-position: 50% center;
  }
}

.carousel__icon {
  display: none;
}
.carousel__viewport {
  overflow: hidden !important;
}

.carousel__slide {
  max-height: 400px;
  padding: 0.5rem;
}
b
.carousel__next--disabled,
.carousel__prev--disabled {
  display: none;
}
</style>
