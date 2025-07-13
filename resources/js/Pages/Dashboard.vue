<template>
    <AuthenticatedLayout>
        <div class="container mx-auto p-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">
                KTS Transport Dashboard
            </h1>

            <!-- Summary Cards -->
            <div
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6"
            >
                <div
                    @click="navigateTo('tippers')"
                    class="bg-white shadow-lg rounded-lg p-6 flex items-center cursor-pointer hover:bg-blue-50 transition duration-200"
                >
                    <div class="mr-4">
                        <svg
                            class="w-10 h-10 text-blue-500"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M9 17V7h6v10H9zm6-10h6v10h-6V7zm-12 0h6v10H3V7z"
                            ></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ summary.tippers }}
                        </h3>
                        <p class="text-sm text-gray-600">Total Tippers</p>
                    </div>
                </div>
                <div
                    @click="navigateTo('drivers')"
                    class="bg-white shadow-lg rounded-lg p-6 flex items-center cursor-pointer hover:bg-green-50 transition duration-200"
                >
                    <div class="mr-4">
                        <svg
                            class="w-10 h-10 text-green-500"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                            ></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ summary.drivers }}
                        </h3>
                        <p class="text-sm text-gray-600">Total Drivers</p>
                    </div>
                </div>
                <div
                    @click="navigateTo('plants')"
                    class="bg-white shadow-lg rounded-lg p-6 flex items-center cursor-pointer hover:bg-yellow-50 transition duration-200"
                >
                    <div class="mr-4">
                        <svg
                            class="w-10 h-10 text-yellow-500"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M17.657 16.243l-4.243-4.243m0 0l-4.243 4.243m4.243-4.243l4.243-4.243m-4.243 4.243l-4.243-4.243"
                            ></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ summary.plants }}
                        </h3>
                        <p class="text-sm text-gray-600">Total Plants</p>
                    </div>
                </div>
                <div
                    @click="navigateTo('trips')"
                    class="bg-white shadow-lg rounded-lg p-6 flex items-center cursor-pointer hover:bg-red-50 transition duration-200"
                >
                    <div class="mr-4">
                        <svg
                            class="w-10 h-10 text-red-500"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            ></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ summary.trips }}
                        </h3>
                        <p class="text-sm text-gray-600">Total Trips</p>
                    </div>
                </div>
            </div>

            <!-- License Expiry Reminders -->
            <div
                v-if="expiringTippers.length"
                class="bg-red-50 border-l-4 border-red-500 p-4 mb-6"
            >
                <h2 class="text-lg font-semibold text-red-700 mb-2">
                    License Expiry Reminders
                </h2>
                <ul class="list-disc pl-5">
                    <li
                        v-for="tipper in expiringTippers"
                        :key="tipper.tipper_number"
                        class="text-red-600"
                    >
                        Tipper {{ tipper.tipper_number }} expires on
                        {{ tipper.license_expiry }}
                    </li>
                </ul>
            </div>

            <!-- Period Selector and Reports -->
            <div class="mb-6 flex items-center">
                <label class="mr-3 text-gray-700 font-medium"
                    >Select Period:</label
                >
                <select
                    v-model="period"
                    @change="fetchReports"
                    class="border rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                </select>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Deliveries by Plant Chart -->
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">
                        Deliveries by Plant
                    </h2>
                    <canvas id="plantChart" ref="plantChart"></canvas>
                </div>

                <!-- Driver Salaries Chart -->
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">
                        Driver Salaries
                    </h2>
                    <canvas id="salaryChart" ref="salaryChart"></canvas>
                </div>
            </div>

            <!-- Financial Summary -->
            <div class="mt-6 bg-white shadow-lg rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">
                    Financial Summary
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-gray-600">Total Income:</p>
                        <p class="text-xl font-semibold text-green-600">
                            ₹{{ reports.income.total_income.toFixed(2) || 0 }}
                        </p>
                    </div>
                    <div>
                        <p class="text-gray-600">Pending Payments:</p>
                        <p class="text-xl font-semibold text-red-600">
                            ₹{{ reports.income.pending.toFixed(2) || 0 }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from '../Layouts/AuthenticatedLayout.vue';
import axios from 'axios';
import { Chart, BarController, CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend } from 'chart.js';

Chart.register(BarController, CategoryScale, LinearScale, BarElement, Title, Tooltip, Legend);

export default {
  name: 'Dashboard',
  components: { AuthenticatedLayout },
  data() {
    return {
      summary: { tippers: 0, drivers: 0, plants: 0, trips: 0 },
      expiringTippers: [],
      reports: {
        deliveries_by_plant: [],
        driver_salaries: [],
        income: { total_income: 0, pending: 0 }
      },
      period: 'daily',
      plantChart: null,
      salaryChart: null,
      loading: true,
      error: null,
    };
  },
  mounted() {
    this.fetchSummary();
    this.fetchExpiringLicenses();
    this.fetchReports();
  },
  methods: {
    async fetchSummary() {
      try {
        console.log('Fetching summary data...');

        // Set headers to ensure JSON response
        const config = {
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        };

        const [tippersRes, driversRes, plantsRes, tripsRes] = await Promise.all([
          axios.get('/tippers', config),
          axios.get('/drivers', config),
          axios.get('/plants', config),
          axios.get('/trips', config),
        ]);

        console.log('API responses:', {
          tippers: tippersRes.data,
          drivers: driversRes.data,
          plants: plantsRes.data,
          trips: tripsRes.data
        });

        // Safely get array length, default to 0 if not array
        this.summary.tippers = Array.isArray(tippersRes.data) ? tippersRes.data.length : 0;
        this.summary.drivers = Array.isArray(driversRes.data) ? driversRes.data.length : 0;
        this.summary.plants = Array.isArray(plantsRes.data) ? plantsRes.data.length : 0;
        this.summary.trips = Array.isArray(tripsRes.data) ? tripsRes.data.length : 0;

        this.loading = false;
      } catch (error) {
        console.error('Error fetching summary:', error);
        this.error = 'Failed to load dashboard data';
        this.loading = false;

        // Reset to 0 on error
        this.summary = { tippers: 0, drivers: 0, plants: 0, trips: 0 };
      }
    },

    async fetchExpiringLicenses() {
      try {
        const config = {
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        };

        const response = await axios.get('/tippers/expiring', config);
        this.expiringTippers = Array.isArray(response.data) ? response.data : [];
      } catch (error) {
        console.error('Error fetching expiring licenses:', error);
        this.expiringTippers = [];
      }
    },

    async fetchReports() {
      try {
        const config = {
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        };

        const response = await axios.get(`/trips/reports?period=${this.period}`, config);

        // Safely assign reports data
        this.reports = {
          deliveries_by_plant: Array.isArray(response.data.deliveries_by_plant) ? response.data.deliveries_by_plant : [],
          driver_salaries: Array.isArray(response.data.driver_salaries) ? response.data.driver_salaries : [],
          income: {
            total_income: response.data.income?.total_income || 0,
            pending: response.data.income?.pending || 0
          }
        };

        this.renderCharts();
      } catch (error) {
        console.error('Error fetching reports:', error);
        // Keep default empty structure
      }
    },

    renderCharts() {
      // Only render if we have data
      if (this.reports.deliveries_by_plant.length > 0) {
        if (this.plantChart) this.plantChart.destroy();
        const plantCtx = this.$refs.plantChart.getContext('2d');
        this.plantChart = new Chart(plantCtx, {
          type: 'bar',
          data: {
            labels: this.reports.deliveries_by_plant.map(p => p.name),
            datasets: [{
              label: 'Trips',
              data: this.reports.deliveries_by_plant.map(p => p.trip_count),
              backgroundColor: 'rgba(54, 162, 235, 0.5)',
              borderColor: 'rgba(54, 162, 235, 1)',
              borderWidth: 1,
            }],
          },
          options: {
            scales: { y: { beginAtZero: true } },
            plugins: { legend: { display: false } },
          },
        });
      }

      if (this.reports.driver_salaries.length > 0) {
        if (this.salaryChart) this.salaryChart.destroy();
        const salaryCtx = this.$refs.salaryChart.getContext('2d');
        this.salaryChart = new Chart(salaryCtx, {
          type: 'bar',
          data: {
            labels: this.reports.driver_salaries.map(d => d.name),
            datasets: [{
              label: 'Salary (₹)',
              data: this.reports.driver_salaries.map(d => d.salary),
              backgroundColor: 'rgba(75, 192, 192, 0.5)',
              borderColor: 'rgba(75, 192, 192, 1)',
              borderWidth: 1,
            }],
          },
          options: {
            scales: { y: { beginAtZero: true } },
            plugins: { legend: { display: false } },
          },
        });
      }
    },

    navigateTo(path) {
      this.$router.push(`/${path}`);
    },
  },
};
</script>

<style scoped>
/* Add any custom styles here if needed */
</style>
