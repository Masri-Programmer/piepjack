<template>
    <PageLayout
        :title="$t('common.return.success_title')"
        :description="$t('common.return.success_message')"
    >
        <div v-if="!isLoading" class="space-y-24 md:space-y-32 animate-in fade-in zoom-in-95 duration-700">
            <div class="border-[12px] border-main bg-background p-12 sm:p-20 text-center relative overflow-hidden">
                <!-- Decorative background element -->
                <div class="absolute -right-8 -top-8 w-32 h-32 bg-accent_light rotate-12 opacity-50"></div>
                
                <div class="relative z-10">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-main text-accent mb-8 rounded-none">
                        <Check class="w-10 h-10" stroke-width="3" />
                    </div>
                    
                    <h1 class="text-4xl sm:text-6xl font-bold uppercase tracking-tighter italic leading-none text-foreground mb-6">
                        {{ $t("common.return.success_title") }}
                    </h1>
                    
                    <p class="text-xl font-bold uppercase tracking-widest text-main mb-2">
                        #{{ returnNumber }}
                    </p>
                    
                    <div class="w-24 h-2 bg-border mx-auto mb-8"></div>
                    
                    <p class="text-lg font-medium text-muted-foreground max-w-md mx-auto leading-relaxed">
                        {{ $t("common.return.success_message") }}
                    </p>
                </div>
            </div>

            <div v-if="data?.data" class="space-y-12">
                <div class="flex items-center gap-4">
                    <div class="h-px grow bg-border"></div>
                    <h2 class="text-2xl font-bold uppercase italic tracking-tight shrink-0">
                        {{ $t("common.titles.peopleAlsoLiked") }}
                    </h2>
                    <div class="h-px grow bg-border"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <template v-for="c in data.data" :key="c.id">
                        <HomeCarousel
                            v-if="c.promoted"
                            :name="c.name"
                            :slug="c.slug"
                            :id="c.id"
                            :title="c.name"
                        />
                    </template>
                </div>
            </div>
        </div>
        
        <div v-else class="flex justify-center items-center min-h-[60vh]">
            <Spinner size="lg" />
        </div>
    </PageLayout>
</template>

<script setup>
import { onMounted, computed } from "vue";
import { useRoute } from "vue-router";
import { Check } from "lucide-vue-next";
import PageLayout from "@components/shop/general/PageLayout.vue";
import Spinner from "@components/ui/Spinner.vue";
import HomeCarousel from "@components/shop/home/HomeCarousel.vue";
import { apiQuery } from "@lib/helpers";
import { cartState } from "@lib/store/shop/index.js";

const route = useRoute();
const returnNumber = computed(() => route.query.return_number);

// We fetch categories to show recommendations (promoted ones)
const { data, isLoading } = apiQuery("categories").useGet({});

onMounted(() => {
    // Clear cart on success
    cartState.value.cartItems = [];
});
</script>
