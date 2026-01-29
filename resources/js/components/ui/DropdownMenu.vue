<template>
  <DropdownMenu :class="props.class">
    <DropdownMenuTrigger :class="toggleButtonClass">
      <slot name="toggle" />
    </DropdownMenuTrigger>
    <DropdownMenuContent>
      <div
        :style="modalStyle"
        :class="dropdownContentClass"
      >
        <slot />
        <div
          v-if="clear || save"
          class="dropdown-menu w-full flex justify-between gap-2"
        >
          <slot name="buttons" />
          <button v-if="clear" :class="clear" @click="handleClear">
            {{ trans("common.configurator.CLEAR") }}
          </button>
          <button v-if="save" :class="save" @click="handleSave">
            {{ trans("common.configurator.SAVE") }}
          </button>
        </div>
      </div>
    </DropdownMenuContent>
  </DropdownMenu>
</template>

<script setup>
import { computed, ref } from "vue";
import { useMediaQuery } from "@vueuse/core";
import { onClickOutside, useElementBounding } from "@vueuse/core";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuTrigger,
} from "@components/ui/dropdown-menu";
const props = defineProps({
  open: {
    type: Boolean,
    default: false,
  },
  toggleButtonClass: {
    type: String,
    default: "",
  },
  dropdownContentClass: {
    type: String,
    default: "",
  },
  class: {
    type: String,
    default: "",
  },
  clear: {
    type: String,
    default: null,
  },
  save: {
    type: String,
    default: null,
  },
  leftInt: {
    type: Number,
    default: 50,
  },
});
const emit = defineEmits(["handle:clear", "handle:save", "close", "toggle"]);

const dropdownMenu = ref(null);
const { bottom, left } = useElementBounding(dropdownMenu);

onClickOutside(dropdownMenu, () => {
  if (props.open) {
    emit("close");
  }
});

const isMobile = useMediaQuery("(max-width: 640px)");
const modalStyle = computed(() => ({
  top: `${bottom.value + 10}px`,
  left: isMobile.value ? "150px" : `${left.value - props.leftInt}px`,
}));

const handleClear = () => {
  emit("handle:clear");
};

const handleSave = async () => {
  emit("handle:save");
};
</script>
