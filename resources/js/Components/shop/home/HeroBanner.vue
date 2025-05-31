<template>
    <section class="hero">
        <div id="piepjack-hero" class="hero-container">
            <TransitionGroup tag="div" name="list-zoom" class="hero-content">
                <template v-if="showCategories">
                    <div
                        v-for="(collection, index) in displayedCollections"
                        :key="collection.id"
                        class="category-item"
                        :style="{ transitionDelay: `${index * 650}ms` }"
                    >
                        <router-link
                            :to="
                                '/collections/' +
                                collection.id +
                                '/' +
                                collection.slug
                            "
                            class="hero-btn"
                        >
                            <strong>{{ collection.name }}</strong>
                        </router-link>
                    </div>
                </template>
            </TransitionGroup>
        </div>
    </section>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from "vue";
import { apiQuery } from "@lib/helpers";
import { useSessionStorage } from "@vueuse/core";

const { data } = apiQuery("categories").useGet({});

const showCategories = useSessionStorage("home-animation", false);
const displayedCollections = computed(() => {
    return data.value?.data?.slice(0, 6) || [];
});

onMounted(() => {
    nextTick(() => {
        if (!showCategories.value) {
            showCategories.value = true;
        }
    });
});
</script>

<style scoped>
.list-zoom-enter-from {
    opacity: 0;
    transform: scale(0.5);
}

.list-zoom-enter-active {
    transition: all 3.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.list-zoom-leave-active {
    transition: all 0.4s ease-out;
    position: absolute;
}

.list-zoom-leave-to {
    opacity: 0;
    transform: scale(0.5);
}

.list-zoom-move {
    transition: transform 0.5s ease;
}
</style>
