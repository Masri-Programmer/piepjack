<template>
  <tr
    class="border-b border-dashed last:border-b-0 border-main animate-pulse"
    v-for="row in rows"
    :key="row"
  >
    <td
      v-for="col in columns"
      :key="col"
      class="p-3 px-2 text-center"
    >
      <div :class="getColumnStyle(col)"></div>
    </td>
  </tr>
</template>

<script setup>
import { computed, defineProps } from 'vue';

const props = defineProps({
  rows: {
    type: Number,
    default: 15,
  },
  columns: {
    type: Number,
    default: 5,
  },
});

const getColumnStyle = (col) => {
  const columnStyles = {
    1: "w-6 h-4 bg-main rounded",
    2: "h-12 w-12 bg-main rounded-full mr-3",
    default: "w-full h-4 bg-main rounded grow",
  };
  return columnStyles[col] || columnStyles.default;
};
</script>
