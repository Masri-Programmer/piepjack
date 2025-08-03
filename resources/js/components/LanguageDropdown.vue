<template>
  <DropdownMenu
    :open="props.open"
    :close="$emit('handle:close')"
    :toggle="$emit('handle:toggle')"
    :dropdownContentClass="'bg-accent h-full'"
  >
    <template v-slot:toggle>
      <img class="h-6 w-6 object-contain hover:opacity-75" :src="currentFlag" />
    </template>
    <template v-slot>
      <button
        v-if="locale !== 'de'"
        @click="setLang('de')"
        class="rounded-none! border-0 flex items-center gap-2 text-primary text-decoration-none text-xs p-2 shadow-md font-bold"
      >
        <img class="h-4 w-5 object-contain" :src="de" />
        <span>{{ "DE" }}</span>
      </button>
      <button
        v-if="locale === 'de'"
        @click="setLang('en')"
        class="rounded-none! border-0 flex items-center gap-2 text-primary text-decoration-none text-xs p-2 shadow-md font-bold"
      >
        <img class="h-4 w-5 object-contain" :src="en" />
        <span>{{ "EN" }}</span>
      </button>
    </template>
  </DropdownMenu>
</template>

<script setup>
import { computed, ref } from "vue";
import de from "@img/lang/de_DE.svg";
import en from "@img/lang/en_GB.svg";
import DropdownMenu from "@components/ui/DropdownMenu.vue";
import { useI18n } from "vue-i18n";
import { changeLanguage } from "@lib/config.js";
const { t, locale } = useI18n();

const setLang = (lang) => {
  changeLanguage(lang);
};

const props = defineProps({
  open: { type: Boolean },
});

const currentFlag = computed(() => (locale.value === "de" ? de : en));
</script>
