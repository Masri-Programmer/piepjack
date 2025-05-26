<template>
  <div class="flex bg-transparent text-main absolute">
    <aside
      class="flex h-screen w-12 md:w-20 flex-col items-center border-r border-main bg-main"
    >
      <div
        class="flex h-18 w-full items-center justify-center border-b border-main p-2"
      >
        <router-link to="/" class="block h-12 md:h-16">
          <img
            :src="logoCircle"
            alt="Logo"
            class="h-full object-contain mt-2"
          />
        </router-link>
      </div>
      <nav class="flex flex-1 flex-col gap-y-4 pt-10">
        <router-link
          to="/"
          :class="[
            'text-accent',
            'group',
            'relative',
            'rounded-xl',
            'p-2',
            { 'bg-accent_light text-main': $route.path === '/' },
            'hover:bg-accent hover:text-white',
          ]"
        >
          <Home class="h-6 w-6 stroke-current" />
          <div
            class="absolute inset-y-0 left-12 hidden items-center group-hover:flex"
          >
            <div
              class="relative whitespace-nowrap z-10 rounded-md bg-accent_light px-4 py-2 text-xs text-main drop-shadow-lg"
            >
              <div class="absolute inset-0 -left-1 flex items-center">
                <div class="h-2 w-2 rotate-45 bg-accent"></div>
              </div>
              Dashboard
            </div>
          </div>
        </router-link>

        <router-link
          to="/products"
          :class="[
            'text-accent',
            'group',
            'relative',
            'rounded-xl',
            'p-2',
            'hover:bg-accent hover:text-white',
            { 'bg-accent_light text-main': $route.path === '/products' },
          ]"
        >
          <ChartNoAxesGantt class="h-6 w-6 stroke-current" />
          <div
            class="absolute inset-y-0 left-12 hidden items-center group-hover:flex"
          >
            <div
              class="relative whitespace-nowrap z-10 rounded-md bg-accent_light px-4 py-2 text-xs font-semibold text-main drop-shadow-lg"
            >
              <div class="absolute inset-0 -left-1 flex items-center">
                <div class="h-2 w-2 rotate-45 bg-accent"></div>
              </div>
              Products
            </div>
          </div>
        </router-link>

        <router-link
          to="/categories"
          :class="[
            'text-accent',
            'group',
            'relative',
            'rounded-xl',
            'p-2',
            'hover:bg-accent hover:text-white',
            { 'bg-accent_light text-main': $route.path === '/categories' },
          ]"
        >
          <Layers class="h-6 w-6 stroke-current" />
          <div
            class="absolute inset-y-0 left-12 hidden items-center group-hover:flex"
          >
            <div
              class="relative whitespace-nowrap z-10 rounded-md bg-accent_light px-4 py-2 text-xs font-semibold text-main drop-shadow-lg"
            >
              <div class="absolute inset-0 -left-1 flex items-center">
                <div class="h-2 w-2 rotate-45 bg-accent"></div>
              </div>
              Categories
            </div>
          </div>
        </router-link>

        <router-link
          to="/Orders"
          :class="[
            'text-accent',
            'group',
            'relative',
            'rounded-xl',
            'p-2',
            'hover:bg-accent hover:text-white',
            { 'bg-accent_light text-main': $route.path === '/Orders' },
          ]"
        >
          <Box class="h-6 w-6 stroke-current" />
          <div
            class="absolute inset-y-0 left-12 hidden items-center group-hover:flex"
          >
            <div
              class="relative whitespace-nowrap z-10 rounded-md bg-accent_light px-4 py-2 text-xs font-semibold text-main drop-shadow-lg"
            >
              <div class="absolute inset-0 -left-1 flex items-center">
                <div class="h-2 w-2 rotate-45 bg-accent"></div>
              </div>
              Orders
            </div>
          </div>
        </router-link>

        <router-link
          to="/Returns"
          :class="[
            'text-accent',
            'group',
            'relative',
            'rounded-xl',
            'p-2',
            'hover:bg-accent hover:text-white',
            { 'bg-accent_light text-main': $route.path === '/Returns' },
          ]"
        >
          <SendToBack class="h-6 w-6 stroke-current" />
          <div
            class="absolute inset-y-0 left-12 hidden items-center group-hover:flex"
          >
            <div
              class="relative whitespace-nowrap z-10 rounded-md bg-accent_light px-4 py-2 text-xs font-semibold text-main drop-shadow-lg"
            >
              <div class="absolute inset-0 -left-1 flex items-center">
                <div class="h-2 w-2 rotate-45 bg-accent"></div>
              </div>
              Returns
            </div>
          </div>
        </router-link>

        <router-link
          to="/Users"
          :class="[
            'text-accent',
            'group',
            'relative',
            'rounded-xl',
            'p-2',
            'hover:bg-accent hover:text-white',
            { 'bg-accent_light text-main': $route.path === '/Users' },
          ]"
        >
          <Users class="h-6 w-6 stroke-current" />
          <div
            class="absolute inset-y-0 left-12 hidden items-center group-hover:flex"
          >
            <div
              class="relative whitespace-nowrap z-10 rounded-md bg-accent_light px-4 py-2 text-xs font-semibold text-main drop-shadow-lg"
            >
              <div class="absolute inset-0 -left-1 flex items-center">
                <div class="h-2 w-2 rotate-45 bg-accent"></div>
              </div>
              Users
            </div>
          </div>
        </router-link>

        <router-link
          to="/Reports"
          :class="[
            'text-accent',
            'group',
            'relative',
            'rounded-xl',
            'p-2',
            'hover:bg-accent hover:text-white',
            { 'bg-accent_light text-main': $route.path === '/Reports' },
          ]"
        >
          <ChartBar class="h-6 w-6 stroke-current" />
          <div
            class="absolute inset-y-0 left-12 hidden items-center group-hover:flex"
          >
            <div
              class="relative whitespace-nowrap z-10 rounded-md bg-accent_light px-4 py-2 text-xs font-semibold text-main drop-shadow-lg"
            >
              <div class="absolute inset-0 -left-1 flex items-center">
                <div class="h-2 w-2 rotate-45 bg-accent"></div>
              </div>
              Reports
            </div>
          </div>
        </router-link>

        <router-link
          to="/Settings"
          :class="[
            'text-accent',
            'group',
            'relative',
            'rounded-xl',
            'p-2',
            'hover:bg-accent hover:text-white',
            { 'bg-accent_light text-main': $route.path === '/Settings' },
          ]"
        >
          <Settings class="h-6 w-6 stroke-current" />
          <div
            class="absolute inset-y-0 left-12 hidden items-center group-hover:flex"
          >
            <div
              class="relative whitespace-nowrap z-10 rounded-md bg-accent_light px-4 py-2 text-xs font-semibold text-main drop-shadow-lg"
            >
              <div class="absolute inset-0 -left-1 flex items-center">
                <div class="h-2 w-2 rotate-45 bg-accent"></div>
              </div>
              Settings
            </div>
          </div>
        </router-link>
      </nav>

      <div
        class="flex flex-col items-center gap-y-4 py-10 relative"
        ref="logoutRef"
      >
        <button class="mt-2 rounded-full bg-main" @click="toggle(!value)">
          <img
            class="h-10 w-10 rounded-full"
            src="https://avatars.githubusercontent.com/u/35387401?v=4"
            alt=""
          />
        </button>
        <div
          v-if="value"
          class="bg-main text-accent shadow-lg absolute left-16 bottom-10 rounded-lg p-2 text-xs z-10"
        >
          <button class="w-full mb-3">
            <a :href="SHOP_LINK" target="_blank" rel="noopener noreferrer">
              Shop</a
            >
          </button>
          <button :disabled="loading" class="" @click="logout">Logout</button>
        </div>
      </div>
    </aside>
  </div>
</template>

<script setup>
import { onClickOutside } from "@vueuse/core";
import { useToggle } from "@vueuse/core";
import { useRouter } from "vue-router";
import { useLogout } from "@lib/user";
import { ref } from "vue";
import { SHOP_LINK } from "@lib/constants.js";

import {
  ChartNoAxesGantt,
  Home,
  Box,
  Users,
  ChartBar,
  Settings,
  Layers,
  SendToBack,
} from "lucide-vue-next";
import logoCircle from "@img/logo-new.png";
const props = defineProps({
  nav: Boolean,
});
const NavSidebar = ref(null);
const emit = defineEmits(["close"]);
onClickOutside(NavSidebar, (event) => {
  if (props.nav) emit("closeNav");
});
const router = useRouter();
const { mutate: logout, isLoading: loading } = useLogout();

const [value, toggle] = useToggle();
const logoutRef = ref();
onClickOutside(logoutRef, () => toggle(false));
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
