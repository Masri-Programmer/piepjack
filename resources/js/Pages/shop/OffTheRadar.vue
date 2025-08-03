<template>
  <div class="relative -m-4 md:-m-8 2xl:-m-16 pb-4 md:pb-8 2xl:pb-16 h-[88vh]">
    <!-- <img
            alt=""
            width="4480"
            height="1613"
            sizes="100vw"
            class="filter brightness-75 h-full w-full object-cover"
            style="
                z-index: -1;
                transform: scale(var(--motion-scale));
                opacity: 1;
                --motion-scale: 1;
            "
        />
        <div
            class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-10 text-accent font-bold text-center grid gap-8"
        >
            {{ $t("components.buttons.offTheRadar") }}
            <div
                class="flex items-start justify-center w-full gap-4 count-down-main text-sm"
            >
                <div class="timer w-16">
                    <div>
                        <h3
                            class="countdown-element days font-manrope font-semibold text-2xl text-accent text-center"
                        >
                            {{ days }}
                        </h3>
                    </div>
                    <p
                        class="text-sm font-normal text-gray-900 mt-1 text-center w-full"
                    >
                        {{ $t("layout.header.days") }}
                    </p>
                </div>

                <h3 class="font-manrope font-semibold text-2xl text-gray-900">
                    :
                </h3>

                <div class="timer w-16">
                    <div>
                        <h3
                            class="countdown-element hours font-manrope font-semibold text-2xl text-accent text-center"
                        >
                            {{ hours }}
                        </h3>
                    </div>
                    <p
                        class="text-sm font-normal text-gray-900 mt-1 text-center w-full"
                    >
                        {{ $t("layout.header.hours") }}
                    </p>
                </div>

                <h3 class="font-manrope font-semibold text-2xl text-gray-900">
                    :
                </h3>

                <div class="timer w-16">
                    <div>
                        <h3
                            class="countdown-element minutes font-manrope font-semibold text-2xl text-accent text-center"
                        >
                            {{ minutes }}
                        </h3>
                    </div>
                    <p
                        class="text-sm font-normal text-gray-900 mt-1 text-center w-full"
                    >
                        {{ $t("layout.header.minutes") }}
                    </p>
                </div>

                <h3 class="font-manrope font-semibold text-2xl text-gray-900">
                    :
                </h3>

                <div class="timer w-16">
                    <div>
                        <h3
                            class="countdown-element seconds font-manrope font-semibold text-2xl text-accent text-center"
                        >
                            {{ seconds }}
                        </h3>
                    </div>
                    <p
                        class="text-sm font-normal text-gray-900 mt-1 text-center w-full"
                    >
                        {{ $t("layout.header.seconds") }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div
        class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 md:gap-3 lg:gap-5 xl:gap-7"
    >
        <ProductCard />
        <ProductCard />
        <ProductCard />
        <ProductCard />
        <ProductCard />
        <ProductCard /> -->
  </div>
</template>
<script setup>
import { useI18n } from "vue-i18n";
import { ref, onMounted } from "vue";
import ProductCard from "@components/shop/product/ProductCard.vue";

const { t } = useI18n();

const days = ref(0);
const hours = ref(0);
const minutes = ref(0);
const seconds = ref(0);

const countdownDate = new Date("2024-12-31T23:59:59").getTime();

const updateCountdown = () => {
  const now = new Date().getTime();
  const timeRemaining = countdownDate - now;

  days.value = Math.floor(timeRemaining / (1000 * 60 * 60 * 24));
  hours.value = Math.floor(
    (timeRemaining % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)
  );
  minutes.value = Math.floor((timeRemaining % (1000 * 60 * 60)) / (1000 * 60));
  seconds.value = Math.floor((timeRemaining % (1000 * 60)) / 1000);
};

onMounted(() => {
  updateCountdown(); // Initial call
  setInterval(updateCountdown, 1000); // Update every second
});
</script>
<style scoped>
.countdown-element {
  min-width: 50px;
}
</style>
