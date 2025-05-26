<template>
  <PageLayout :data="links" title="Dashboard">
    <div class="dashboard grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
      <div class="card text-accent grid">
        <h1 class="text-base font-bold mb-3">
          Users: <span>{{ Dashboard?.customer_counts }}</span>
        </h1>
        <p v-if="isLoadingDashboard"><Spinner class="w-8" /></p>
      </div>
      <div class="card text-accent grid">
        <h1 class="text-base font-bold mb-3">
          Products: <span>{{ Dashboard?.product_counts }}</span>
        </h1>
        <p v-if="isLoadingDashboard"><Spinner class="w-8" /></p>
      </div>
      <div class="card text-accent grid">
        <h1 class="text-base font-bold mb-3">
          Orders: <span>{{ Dashboard?.order_status_counts }}</span>
        </h1>
        <p v-if="isLoadingDashboard"><Spinner class="w-8" /></p>
      </div>
      <div class="card text-accent grid">
        <h1 class="text-base font-bold mb-3">
          Categories <span>{{ Dashboard?.category_counts }}</span>
        </h1>
        <p v-if="isLoadingDashboard"><Spinner class="w-8" /></p>
      </div>
      <div class="card text-accent grid">
        <h1 class="text-base font-bold mb-3">
          Total Income: <span>{{ Dashboard?.total_income }} €</span>
        </h1>
        <p v-if="isLoadingDashboard"><Spinner class="w-8" /></p>
      </div>
      <div class="card text-accent grid">
        <h1 class="text-base font-bold mb-3">No Data...</h1>
      </div>
      <div class="card text-accent grid">
        <h1 class="text-base font-bold mb-3">Best Selling Products</h1>
        <p v-if="isLoadingDashboard"><Spinner class="w-8" /></p>
        <div>
          <Pie
            :data="Dashboard?.best_selling_products ?? data"
            :options="options"
          />
        </div>
      </div>
      <div class="card text-accent grid">
        <h1 class="text-base font-bold mb-3">Total Income Last 3 Months</h1>
        <p v-if="isLoadingDashboard"><Spinner class="w-8" /></p>
        <div>
          <Line
            :data="Dashboard?.sorted_total_income ?? data"
            :options="options"
          />
        </div>
      </div>
      <div class="card text-accent grid">
        <h1 class="text-base font-bold mb-3">Latest Completed Orders</h1>
        <p v-if="isLoadingDashboard"><Spinner class="w-8" /></p>
        <div>
          <Bar
            id="Latest_Orders"
            :options="options"
            :data="Dashboard?.latest_completed_orders ?? data"
            :name="'Latest Orders'"
          />
        </div>
      </div>
    </div>
  </PageLayout>
</template>

<script setup>
import { ref } from "vue";
import { Bar } from "vue-chartjs";
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
import { Pie } from "vue-chartjs";
import { Line } from "vue-chartjs";

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
import PageLayout from "@layouts/admin/PageLayout.vue";
import Spinner from"@components/ui/Spinner.vue";
import { apiQuery } from "@lib/helpers";
const {
  data: Dashboard,
  error: errorDashboard,
  isLoading: isLoadingDashboard,
} =   apiQuery("dashboard").useGet();

const links = ref([
  {
    title: "Home",
    link: "",
  },
  {
    title: "Dashboard",
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
