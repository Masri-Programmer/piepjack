<template>
  <div
    class="flex justify-center items-center flex-col w-full h-full min-h-screen bg-gray"
  >
    <h1
      class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-extrabold text-center mb-6 sm:mb-8 md:mb-10 lg:mb-12"
    >
      {{ $t("validation.error.notFound") }}
    </h1>
    <p class="text-pretty w-1/2 text-center">
      {{ $t("validation.error.message") }}
    </p>
    <div class="flex flex-col justify-center items-center">
      <div class="view-all text-xs mt-12">
        {{ $t("validation.error.continueShopping") }}
      </div>
    </div>
  </div>
</template>

<script setup></script>
