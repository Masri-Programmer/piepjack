<template>
  <div class="accordion-table-container">
    <div class="accordion-table table-scroller overflow-auto">
      <table>
        <thead>
          <tr>
            <th v-for="(header, index) in headers" :key="index">
              {{ header }}
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(row, rowIndex) in rows" :key="rowIndex">
            <td v-for="(cell, cellIndex) in row" :key="cellIndex">
              <template v-if="cell.image">
                <span>
                  <img :src="cell.image" :alt="cell.alt" class="country-flag" />
                  {{ cell.text }}
                </span>
              </template>
              <template v-else>
                {{ cell }}
              </template>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-if="returnsInfo" class="accordion-table-container">
      <h3>{{ returnsInfo.title }}</h3>
      <p class="accordion-description">
        {{ returnsInfo.text }}
        <a class="accordion-link" :href="returnsInfo.link.url">{{ returnsInfo.link.text }}</a>.
      </p>
    </div>
  </div>
</template>

<script setup>
import "@assets/css/table.css";

const props = defineProps({
  headers: Array,
  rows: Array,
  returnsInfo: Object,
});
</script>