<template>
  <div class="my-3" v-if="!Updating">
    <div class="bg-gray p-24 md:mx-auto">
      <svg viewBox="0 0 24 24" class="text-green-600 w-16 h-16 mx-auto mb-6">
        <path
          fill="currentColor"
          d="M12,0A12,12,0,1,0,24,12,12.014,12.014,0,0,0,12,0Zm6.927,8.2-6.845,9.289a1.011,1.011,0,0,1-1.43.188L5.764,13.769a1,1,0,1,1,1.25-1.562l4.076,3.261,6.227-8.451A1,1,0,1,1,18.927,8.2Z"
        />
      </svg>
      <div class="text-center text-accent_dark">
        <h3 class="md:text-3xl text-base text-accent font-semibold text-center">
          # {{ orderId }} <br />
          {{ $t("validation.success.title") }}
        </h3>
        <p class="my-2">{{ $t("validation.success.thankYou") }}</p>
        <p>{{ $t("validation.success.checkEmail", { payment:$t('validation.success.order')}) }}</p>
      </div>
    </div>
  </div>
  <div v-else><Spinner /></div>

  <template v-if="data?.data">
    <template v-for="c in data.data">
      <HomeCarousel
        v-if="c.promoted"
        :name="c.name"
        :slug="c.slug"
        :id="c.id"
        :title="c.name"
      />
    </template>
  </template>
</template>

<script setup>
import { onMounted } from "vue";
import { useRoute } from "vue-router";
import Spinner from "@components/ui/Spinner.vue";
import HomeCarousel from "@components/shop/home/HomeCarousel.vue";
import { apiQuery } from "@lib/helpers";
import { cartState } from "@lib/store/shop/index.js";

const route = useRoute();
const orderId = route.query.order_number;
const { data, error, isLoading } = apiQuery("categories").useGet({});

onMounted(() => {
  if (orderId) {
 cartState.value.cartItems = [];
  }
});
</script>
