<template>
  <div
    class="w-full px-4 md:px-8 2xl:px-16 py-10 border-t border-gray-400 shadow-md tracking-normal text-xs sm:text-sm flex justify-center items-center gap-4 bg-[#f2f0a1] text-accent_dark"
  >
    <a
      href="https://www.instagram.com/piepjack"
      target="_blank"
      rel="noopener noreferrer"
      class="social-media-button"
      aria-label="Visit our Instagram"
    >
      <Instagram />
    </a>

    <a
      href="https://www.tiktok.com/@piepjack?_t=ZN-8zbZSkrIpz5&_r=1"
      target="_blank"
      rel="noopener noreferrer"
      class="social-media-button"
      aria-label="Visit our TikTok"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        width="20"
        height="20"
        fill="currentColor"
        class="bi bi-tiktok"
        viewBox="0 0 16 16"
      >
        <path
          d="M9 0h1.98c.144.715.54 1.617 1.235 2.512C12.895 3.389 13.797 4 15 4v2c-1.753 0-3.07-.814-4-1.829V11a5 5 0 1 1-5-5v2a3 3 0 1 0 3 3z"
        />
      </svg>
    </a>

    <a
      href="https://Facebook.com/piepjack"
      target="_blank"
      rel="noopener noreferrer"
      class="social-media-button"
      aria-label="Visit our Facebook"
    >
      <Facebook />
    </a>
  </div>
  <footer
    class="px-4 md:px-8 2xl:px-16 py-10 border-t border-gray shadow-md tracking-normal bg-accent_dark text-xs sm:text-sm"
  >
    <div
      class="footer grid grid-cols-2 md:grid-cols-3 lg:grid-cols-3 gap-5 md:gap-9 xl:gap-5 pb-6 md:pb-14 lg:pb-14 2xl:pb-18 3xl:pb-22 lg:mb-0.5 2xl:mb-0 3xl:-mb-1 container"
    >
      <div>
        <h5 class="text-sm sm:text-base font-bold uppercase mb-3">
          {{ $t("layout.footer.productOverview") }}
        </h5>
        <div class="grid grid-cols-2 gap-2">
          <RouterLink class="text-gray capitalize" to="/collections">
            {{ $t("layout.footer.shopAll") }}</RouterLink
          >
          <div
            v-if="data?.data"
            v-for="category in data.data"
            :key="category.id"
            class="text-gray capitalize"
          >
            <router-link
              :to="'/collections/' + category.id + '/' + category.slug"
              class="hover:text-white"
            >
              {{ category.name }}
            </router-link>
          </div>
        </div>
      </div>

      <div>
        <h5 class="text-sm sm:text-base font-bold uppercase">
          {{ $t("layout.footer.support") }}
        </h5>
        <p>
          <RouterLink to="/faq">{{ $t("layout.footer.faq") }}</RouterLink>
        </p>
        <p>
          <RouterLink to="/shipping">{{
            $t("layout.footer.shipping")
          }}</RouterLink>
        </p>
        <p>
          <RouterLink to="/return-order">{{
            $t("layout.footer.makeReturn")
          }}</RouterLink>
        </p>
        <p>
          <RouterLink to="/track-order">{{
            $t("layout.footer.trackOrder")
          }}</RouterLink>
        </p>
      </div>

      <div>
        <h5 class="text-sm sm:text-base font-bold uppercase">
          {{ $t("layout.footer.information") }}
        </h5>
        <p>
          <RouterLink to="/impressum">{{
            $t("layout.footer.impressum")
          }}</RouterLink>
        </p>
        <p>
          <RouterLink to="/terms-of-service">{{
            $t("layout.footer.agb")
          }}</RouterLink>
        </p>
        <p>
          <RouterLink to="/datenschutzerklarung">{{
            $t("layout.footer.privacyPolicy")
          }}</RouterLink>
        </p>
      </div>
    </div>

    <div class="flex flex-col gap-8 container">
      <div class="text-xs opacity-55 flex justify-start gap-14">
        <!-- Social media icons go here -->
      </div>
      <div class="text-xs opacity-75">{{ copyrightText }}</div>
    </div>
  </footer>
</template>
<script setup>
import { computed } from "vue";
import { useI18n } from "vue-i18n";
import { useRouter } from "vue-router";
import { apiQuery } from "@lib/helpers";
import { Instagram, Facebook, Twitter } from "lucide-vue-next";
const { t } = useI18n();
const copyrightText = computed(() =>
  t("layout.footer.copyright", { year: new Date().getFullYear() })
);
const { data, error, isLoading } = apiQuery("categories").useGet({});
</script>
