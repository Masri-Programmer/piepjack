<template>
  <div class="stepper-container">
    <div class="stepper-background">
      <svg class="stepper-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 875 48" fill="none">
        <path
          d="M2 29C20.2154 33.6961 38.9915 35.1324 57.6111 37.5555C80.2065 40.496 102.791 43.3231 125.556 44.5555C163.184 46.5927 201.26 45 238.944 45C312.75 45 385.368 30.7371 458.278 20.6666C495.231 15.5627 532.399 11.6429 569.278 6.11109C589.515 3.07551 609.767 2.09927 630.222 1.99998C655.606 1.87676 681.208 1.11809 706.556 2.44442C739.552 4.17096 772.539 6.75565 805.222 11.5C828 14.8064 850.34 20.2233 873 24"
          :class="['stepper-path']"
        />
      </svg>
    </div>
    <div class="stepper-grid">
      <div
        v-for="step in steps"
        :key="step.number"
        class="stepper-step"
        @click="$emit('stepClick', step.number)"
      >
        <div
          :class="[ 
            'stepper-step-inner',
            step.number === activeStep
              ? 'stepper-step-active'
              : 'stepper-step-inactive',
          ]"
        >
          <span
            :class="[ 
              'stepper-step-number',
              step.number === activeStep ? 'stepper-step-number-active' : 'stepper-step-number-inactive',
            ]"
          >
            {{ step.number }}
          </span>
        </div>
        <h3 class="stepper-title">
          {{ step.title }}
        </h3>
        <p class="stepper-description">
          {{ step.description }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, defineProps } from "vue";
import '@assets/css/order/stepper.css'

const props = defineProps({
  activeStep: Number,
});

const steps = computed(() => [
  {
    number: 1,
    title: "Initiate Return",
    description: "Start the return process by providing the order details.",
  },
  {
    number: 2,
    title: "Provide Reason",
    description: "Select a reason for returning the product.",
  },
  {
    number: 3,
    title: "Complete Return",
    description: "Confirm your request and submit it for processing.",
  },
]);
</script>

<style scoped>
.relative {
  position: relative;
}
</style>
