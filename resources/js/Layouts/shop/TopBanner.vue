<template>
  <div
    class="banner hidden bg-main text-accent text-xs md:flex justify-between gap-3 items-center h-14 px-3 md:px-12 flex-row-reverse md:flex-row"
  >
    <div
      class="flex justify-between items-center w-full grow flex-col md:flex-row sale-text-banner"
      :style="isMobile && { fontSize: '0.45em' }"
    >
      <span v-html="$t('layout.header.readyForDrop')"></span>
      <span>{{ $t("layout.header.newCollectionDropsIn") }}</span>
      <span>{{ formattedTime }}</span>
    </div>
    <router-link to="/off-the-radar">
      <button
        class="off-the-radar hoverBtn h-8 mx-2 md:mx-5 sm:max-w-40 whitespace-nowrap text-xs"
        :style="isMobile && { fontSize: '0.45em' }"
      >
        {{ $t("components.buttons.offTheRadar") }}
      </button>
    </router-link>
  </div>
</template>

<script setup>
import { computed, ref } from "vue";
import { useI18n } from "vue-i18n";
import { useMediaQuery } from "@vueuse/core";

const { t } = useI18n();
const countdown = ref({
  days: 4,
  hours: 20,
  minutes: 59,
});

const isMobile = useMediaQuery("(max-width: 640px)");
const formattedTime = computed(() => {
  return t("layout.header.countdown", {
    days: countdown.value.days,
    hours: countdown.value.hours,
    minutes: countdown.value.minutes,
  });
});
</script>
