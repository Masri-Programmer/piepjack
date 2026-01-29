<template>
  <PageLayout :data="links" :title="$t('admin.dashboard.title')">
    <div class="dashboard grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
      <div class="card text-accent grid">
        <h1 class="text-base font-bold mb-3">
          {{ $t('admin.dashboard.users') }} <span>{{ Dashboard?.user_counts }}</span>
        </h1>
        <p v-if="isLoadingDashboard"><Spinner class="w-8" /></p>
      </div>
      <div class="card text-accent grid">
        <h1 class="text-base font-bold mb-3">
          {{ $t('admin.dashboard.products') }} <span>{{ Dashboard?.product_counts }}</span>
        </h1>
        <p v-if="isLoadingDashboard"><Spinner class="w-8" /></p>
      </div>
      <div class="card text-accent grid">
        <h1 class="text-base font-bold mb-3">
          {{ $t('admin.dashboard.orders') }} <span>{{ Dashboard?.order_status_counts }}</span>
        </h1>
        <p v-if="isLoadingDashboard"><Spinner class="w-8" /></p>
      </div>
      <div class="card text-accent grid">
        <h1 class="text-base font-bold mb-3">
          {{ $t('admin.dashboard.categories') }} <span>{{ Dashboard?.category_counts }}</span>
        </h1>
        <p v-if="isLoadingDashboard"><Spinner class="w-8" /></p>
      </div>
      <div class="card text-accent grid">
        <h1 class="text-base font-bold mb-3">
          {{ $t('admin.dashboard.totalIncome') }} <span>{{ Dashboard?.total_income }} €</span>
        </h1>
        <p v-if="isLoadingDashboard"><Spinner class="w-8" /></p>
      </div>
      <div class="card text-accent grid">
        <h1 class="text-base font-bold mb-3">{{ $t('admin.dashboard.noData') }}</h1>
      </div>
      <div class="card text-accent grid">
        <h1 class="text-base font-bold mb-3">{{ $t('admin.dashboard.bestSellingProducts') }}</h1>
        <p v-if="isLoadingDashboard"><Spinner class="w-8" /></p>
        <div>
          <Pie
            :data="Dashboard?.best_selling_products ?? data"
            :options="options"
          />
        </div>
      </div>
      <div class="card text-accent grid">
        <h1 class="text-base font-bold mb-3">{{ $t('admin.dashboard.incomeLast3Months') }}</h1>
        <p v-if="isLoadingDashboard"><Spinner class="w-8" /></p>
        <div>
          <Line
            :data="Dashboard?.sorted_total_income ?? data"
            :options="options"
          />
        </div>
      </div>
      <div class="card text-accent grid">
        <h1 class="text-base font-bold mb-3">{{ $t('admin.dashboard.latestCompletedOrders') }}</h1>
        <p v-if="isLoadingDashboard"><Spinner class="w-8" /></p>
        <div>
          <Bar
            id="Latest_Orders"
            :options="options"
            :data="Dashboard?.latest_completed_orders ?? data"
            :name="$t('admin.dashboard.latestOrders')"
          />
        </div>
      </div>
    </div>
  </PageLayout>
</template>

<script setup>
import { computed } from "vue";
import { useI18n } from 'vue-i18n';
import { Bar, Pie, Line } from "vue-chartjs";
import {
  Title,
  Legend,
  Tooltip,
  BarElement,
  ArcElement,
  LinearScale,
  LineElement,
  PointElement,
  CategoryScale,
  Chart as ChartJS,
} from "chart.js";
import PageLayout from "@layouts/admin/PageLayout.vue";
import Spinner from"@components/ui/Spinner.vue";
import { apiQuery } from "@lib/helpers";

ChartJS.register(
  Title,
  Legend,
  Tooltip,
  BarElement,
  LinearScale,
  ArcElement,
  LineElement,
  CategoryScale,
  PointElement
);

const { t } = useI18n();

const {
  data: Dashboard,
  error: errorDashboard,
  isLoading: isLoadingDashboard,
} = apiQuery("dashboard").useGet();

const links = computed(() => [
  {
    title: t('common.menu.home'),
    link: "",
  },
  {
    title: t('admin.dashboard.title'),
    current: true,
    link: "",
  },
]);

const data = {
  labels: [],
  datasets: [
    {
      label: "",
      backgroundColor: "",
      data: [],
    },
  ],
};

const options = {
  responsive: true,
  maintainAspectRatio: false,
};
</script>
