<template>
  <HeroBanner />
  <div class="container grid gap-8 md:gap-16">
    <HomeProducts />
    <!-- <HomeCategories :data="data" /> -->
    <hr class="text-gray" />
    <template v-if="data?.data">
      <template v-for="c in data.data">
        <HomeCarousel
          v-if="c.promoted"
          :name="c.name"
          :slug="c.slug"
          :id="c.id"
          :title="c.name"
        />
      </template>
    </template>
  </div>
</template>

<script setup>
import HeroBanner from "../components/home/HeroBanner.vue";
import HomeProducts from "../components/home/HomeProducts.vue";
import HomeCategories from "../components/home/HomeCategories.vue";
import HomeCarousel from "../components/home/HomeCarousel.vue";
import { apiQuery } from "@lib/helpers";

const { data, error, isLoading } = apiQuery("categories").useGet({});
</script>
w
