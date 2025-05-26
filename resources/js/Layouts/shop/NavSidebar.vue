<template>
  <div v-show="nav" class="fixed inset-0 z-50 overflow-hidden text-xs">
    <!-- Overlay Background -->
    <transition name="fade">
      <div
        v-show="nav"
        class="absolute inset-0 bg-zinc-900 bg-opacity-55"
      ></div>
    </transition>
    <!-- NavSidebar Content -->
    <section class="absolute inset-y-0 left-0 max-w-full flex">
      <transition name="slide">
        <div
          v-show="nav"
          class="w-screen max-w-md h-full flex flex-col py-6 bg-accent shadow-xl me-24"
          ref="NavSidebar"
        >
          <!-- NavSidebar Header -->
          <div class="flex items-center justify-between px-4">
            <button @click="$emit('closeNav')">
              <span class="sr-only">{{ $t("common.menu.close") }}</span>
              <svg
                class="h-8 w-8 opacity-55"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                aria-hidden="true"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="1"
                  d="M6 18L18 6M6 6l12 12"
                />
              </svg>
            </button>
          </div>

          <div
            class="flex flex-col h-1/2 uppercase relative p-4"
            v-if="openTab === 1"
          >
            <div
              class="flex justify-between items-center h-full border-animate border-gray border-b cursor-pointer px-1"
            >
              <router-link
                to="/collections"
                @click="$emit('closeNav')"
                class="flex items-center h-full border-animate cursor-pointer px-1"
              >
                <p class="cursor-pointer text-xs">
                  {{ $t("common.menu.shopAll") }}
                </p>
              </router-link>
            </div>
            <router-link
              to="/"
              @click="$emit('closeNav')"
              class="flex items-center h-full border-animate border-gray border-b cursor-pointer px-1"
            >
              <span class="cursor-pointer text-xs">{{
                $t("common.menu.home")
              }}</span>
            </router-link>
            <div
              class="flex justify-between items-center h-full border-animate border-gray border-b cursor-pointer px-1"
              @click="openTab = 3"
            >
              <p class="cursor-pointer text-xs">
                {{ $t("common.menu.collections") }}
              </p>
              <ChevronRight strokeWidth="1" size="20" />
            </div>

            <router-link
              to="/about"
              @click="$emit('closeNav')"
              class="flex items-center h-full border-animate border-gray border-b cursor-pointer px-1"
            >
              <span class="cursor-pointer text-xs">{{
                $t("common.menu.aboutUs")
              }}</span>
            </router-link>
          </div>

          <div
            class="flex flex-col h-1/2 uppercase relative p-4"
            v-if="openTab === 3"
          >
            <div
              class="flex justify-between items-center border-animate border-gray border-b cursor-pointer px-1 py-4"
              @click="openTab = 1"
            >
              <ChevronLeft strokeWidth="1" size="20" />
              <p class="text-xs">{{ $t("common.menu.collections") }}</p>
            </div>
            <div class="flex flex-col">
              <transition-group
                name="fade-up"
                tag="div"
                class="flex flex-col justify-center"
              >
                <router-link
                  v-for="(collection, index) in collections"
                  :key="index"
                  @click="$emit('closeNav')"
                  :to="'/collections/' + collection.id + '/' + collection.slug"
                  class="flex flex-col py-4 border-b border-gray"
                >
                  {{ collection.name }}
                </router-link>
              </transition-group>
            </div>
          </div>
        </div>
      </transition>
    </section>
  </div>
</template>

<script setup>
import { onClickOutside } from "@vueuse/core";
import { ChevronLeft, ChevronRight } from "lucide-vue-next";
import { ref } from "vue";
const props = defineProps({
  nav: Boolean,
  shopAllCategories: Array,
  collections: Array,
});

const NavSidebar = ref(null);

const openTab = ref(1);

const emit = defineEmits(["close"]);
onClickOutside(NavSidebar, (event) => {
  if (props.nav) emit("closeNav");
});
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
.slide-enter-active,
.slide-leave-active {
  transition: transform 0.3s ease;
}
.slide-enter-from,
.slide-leave-to {
  transform: translateX(-90%);
}
</style>
