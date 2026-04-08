<template>
    <PageLayout
        title="Home"
        description="Piepjack Clothing - Modern apparel for those who believe in progress."
    >
        <template #fullWidth>
            <HeroBanner />
        </template>

        <div class="space-y-24 md:space-y-32">
            <section>
                <HomeProducts />
            </section>

            <hr class="border-t border-border/50" />

            <template v-if="data?.data">
                <template v-for="c in data.data" :key="c.id">
                    <section v-if="c.promoted" class="my-12">
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
    </PageLayout>
</template>

<script setup>
import PageLayout from "@components/shop/general/PageLayout.vue";
import HeroBanner from "@components/shop/home/HeroBanner.vue";
import HomeProducts from "@components/shop/home/HomeProducts.vue";
import HomeCarousel from "@components/shop/home/HomeCarousel.vue";
import { apiQuery } from "@lib/helpers";

const { data, error, isLoading } = apiQuery("categories").useGet({});
</script>
