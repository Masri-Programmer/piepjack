<template>
    <HeroBanner />
    <div class="container">
        <section>
            <HomeProducts />
        </section>

        <hr class="border-t border-border/50" />

        <template v-if="data?.data">
            <template v-for="(c, index) in data.data">
                <section v-if="c.promoted" :key="c.id" class="my-12">
                    <HomeCarousel
                        :name="c.name"
                        :slug="c.slug"
                        :id="c.id"
                        :title="c.name"
                    />
                </section>
            </template>
        </template>
    </div>
</template>

<script setup>
import HeroBanner from "@components/shop/home/HeroBanner.vue";
import HomeProducts from "@components/shop/home/HomeProducts.vue";
import HomeCarousel from "@components/shop/home/HomeCarousel.vue";
import { apiQuery } from "@lib/helpers";

const { data, error, isLoading } = apiQuery("categories").useGet({});
</script>
