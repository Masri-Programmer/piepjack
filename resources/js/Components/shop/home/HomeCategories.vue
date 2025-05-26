<template>
  <div class="home-categories-container">
    <template v-for="c in data?.data" :key="c.id">
      <router-link
        :to="'/collections/' + c.id + '/' + c.slug"
        class="home-categories-item"
        v-if="c.promoted"
      >
        <div class="home-categories-item-content">
          <img
            :src="logo"
            alt=""
            width="818"
            height="688"
            loading="lazy"
            sizes="(max-width: 699px) 100vw, (max-width: 1149px) 50vw, 50vw"
            class="home-categories-item-image"
            draggable="false"
          />
          <h1 class="home-categories-item-title">
            {{ c.name }}
          </h1>
        </div>
      </router-link>
    </template>
  </div>
  <!-- <hr class="text-gray" /> -->
  <!-- <div class="flex justify-center md:justify-between gap-4 md:gap-10 flex-wrap">
    <template v-for="c in data?.data" :key="c.id">
      <router-link
        :to="'/collections/' + c.id + '/' + c.slug"
        class=""
        v-if="c.promoted"
      >
        <div class="home-categories-item-content">
          <FlipCard
            description="Offers beautifully designed, washable cotton and includes custom styles and ideas by us and contributed by the community."
            :image="logo"
            rotate="y"
            subtitle="What is this Category?"
            :title="c.name"
          />
        </div>
      </router-link>
    </template>
  </div> -->
</template>

<script setup>
import logo from "@img/logo_new_gray_bg_white.jpeg";
import FlipCard from "./FlipCard.vue";
const props = defineProps({
  data: { type: Object, required: true },
});
</script>
