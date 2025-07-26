<template>
  <AuthenticatedLayout>
    <Head title="Trip Reports" />

    <div class="container mx-auto px-4 py-8">
      <!-- Header -->
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Trip Reports & Analytics</h1>
        <div class="flex space-x-4">
          <Link :href="route('trips.index')" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Back to Trips
          </Link>
          <button @click="exportReport" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
            Export Report
          </button>
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
            <input
              v-model="filters.search"
              type="text"
              placeholder="Search driver, plant..."
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Period</label>
            <select
              v-model="filters.period"
              @change="fetchReports"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="today">Today</option>
              <option value="yesterday">Yesterday</option>
              <option value="weekly">This Week</option>
              <option value="monthly">This Month</option>
              <option value="quarterly">This Quarter</option>
              <option value="yearly">This Year</option>
              <option value="custom">Custom Range</option>
            </select>
          </div>
          <div v-if="filters.period === 'custom'">
            <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
            <input
              v-model="filters.startDate"
              type="date"
              @change="fetchReports"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div v-if="filters.period === 'custom'">
            <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
            <input
              v-model="filters.endDate"
              type="date"
              @change="fetchReports"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Plant Filter</label>
            <select
              v-model="filters.plantId"
              @change="fetchReports"
              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">All Plants</option>
              <option v-for="plant in plants" :key="plant.id" :value="plant.id">
                {{ plant.name }}
              </option>
            </select>
          </div>
        </div>
      </div>

      <!-- Summary Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-blue-100 text-sm">Total Trips</p>
              <p class="text-3xl font-bold">{{ summary.totalTrips || 0 }}</p>
            </div>
            <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
              <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"/>
                <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v8a1 1 0 001 1h2a1 1 0 001-1V8a1 1 0 00-1-1h-2z"/>
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-green-100 text-sm">Total Revenue</p>
              <p class="text-3xl font-bold">Rs. {{ formatCurrency(summary.totalRevenue) }}</p>
            </div>
            <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
              <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-orange-100 text-sm">Total Expenses</p>
              <p class="text-3xl font-bold">Rs. {{ formatCurrency(summary.totalExpenses) }}</p>
            </div>
            <div class="bg-orange-400 bg-opacity-30 rounded-full p-3">
              <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-purple-100 text-sm">Net Income</p>
              <p class="text-3xl font-bold" :class="summary.netIncome >= 0 ? 'text-white' : 'text-red-200'">
                Rs. {{ formatCurrency(summary.netIncome) }}
              </p>
            </div>
            <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
              <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts Section - UPDATED WITH BETTER VISIBILITY -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Revenue Trend Chart -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Revenue Trend</h3>
          <div class="w-full" style="height: 400px; min-height: 400px;">
            <canvas ref="revenueChart" class="w-full h-full"></canvas>
          </div>
        </div>

        <!-- Plant Distribution Chart -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">Trips by Plant</h3>
          <div class="w-full" style="height: 400px; min-height: 400px;">
            <canvas ref="plantChart" class="w-full h-full"></canvas>
          </div>
        </div>
      </div>

      <!-- Driver Performance Table -->
      <div class="bg-white rounded-lg shadow-md mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900">Driver Performance</h3>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Driver</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Trips</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Salary</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg per Trip</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Performance</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="driver in filteredDriverStats" :key="driver.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                      {{ driver.name.charAt(0) }}
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">{{ driver.name }}</div>
                      <div class="text-sm text-gray-500">{{ driver.phone && driver.phone !== 'N/A' ? driver.phone : 'No contact' }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    {{ driver.total_trips }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  Rs. {{ formatCurrency(driver.total_salary) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  Rs. {{ formatCurrency(driver.avg_per_trip) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                      <div
                        class="bg-green-600 h-2 rounded-full"
                        :style="{ width: Math.min(100, (driver.total_trips / maxDriverTrips) * 100) + '%' }"
                      ></div>
                    </div>
                    <span class="text-sm text-gray-600">{{ Math.round((driver.total_trips / maxDriverTrips) * 100) }}%</span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Plant Performance Table -->
      <div class="bg-white rounded-lg shadow-md mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900">Plant Performance</h3>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plant</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Trips</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg per Trip</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Market Share</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="plant in filteredPlantStats" :key="plant.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white font-semibold">
                      {{ plant.name.charAt(0) }}
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">{{ plant.name }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    {{ plant.total_trips }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  Rs. {{ formatCurrency(plant.total_amount) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  Rs. {{ formatCurrency(plant.avg_per_trip) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                      <div
                        class="bg-blue-600 h-2 rounded-full"
                        :style="{ width: (plant.total_amount / totalRevenue) * 100 + '%' }"
                      ></div>
                    </div>
                    <span class="text-sm text-gray-600">{{ Math.round((plant.total_amount / totalRevenue) * 100) }}%</span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Daily Income Breakdown - UPDATED WITH BETTER VISIBILITY -->
      <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900">Daily Income Breakdown</h3>
        </div>
        <div class="p-6">
          <div class="w-full" style="height: 500px; min-height: 500px;">
            <canvas ref="dailyIncomeChart" class="w-full h-full"></canvas>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { computed, ref, onMounted, nextTick, watch } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  reportData: {
    type: Object,
    default: () => ({})
  },
  plants: {
    type: Array,
    default: () => []
  }
})

// Reactive data
const filters = ref({
  search: '',
  period: 'monthly',
  startDate: '',
  endDate: '',
  plantId: ''
})

const reportData = ref(props.reportData || {})
const revenueChart = ref(null)
const plantChart = ref(null)
const dailyIncomeChart = ref(null)

// Computed properties
const summary = computed(() => {
  const data = reportData.value
  return {
    totalTrips: data.summary?.total_trips || 0,
    totalRevenue: data.summary?.total_revenue || 0,
    totalExpenses: data.summary?.total_expenses || 0,
    netIncome: (data.summary?.total_revenue || 0) - (data.summary?.total_expenses || 0)
  }
})

const filteredDriverStats = computed(() => {
  const drivers = reportData.value.driver_stats || []
  if (!filters.value.search) return drivers

  return drivers.filter(driver =>
    driver.name.toLowerCase().includes(filters.value.search.toLowerCase()) ||
    (driver.phone && driver.phone !== 'N/A' && driver.phone.includes(filters.value.search))
  )
})

const filteredPlantStats = computed(() => {
  const plants = reportData.value.plant_stats || []
  if (!filters.value.search) return plants

  return plants.filter(plant =>
    plant.name.toLowerCase().includes(filters.value.search.toLowerCase())
  )
})

const maxDriverTrips = computed(() => {
  const drivers = filteredDriverStats.value
  return Math.max(...drivers.map(d => d.total_trips), 1)
})

const totalRevenue = computed(() => {
  return filteredPlantStats.value.reduce((sum, plant) => sum + plant.total_amount, 0)
})

// Methods
const formatCurrency = (value) => {
  return new Intl.NumberFormat('en-IN').format(value || 0)
}

const fetchReports = () => {
  const params = {
    period: filters.value.period,
    plant_id: filters.value.plantId
  }

  if (filters.value.period === 'custom') {
    params.start_date = filters.value.startDate
    params.end_date = filters.value.endDate
  }

  router.get(route('trips.reports'), params, {
    preserveState: true,
    onSuccess: (page) => {
      reportData.value = page.props.reportData || {}
      nextTick(() => {
        initCharts()
      })
    }
  })
}

const exportReport = () => {
  const params = new URLSearchParams({
    period: filters.value.period,
    plant_id: filters.value.plantId || '',
    export: 'pdf'
  })

  if (filters.value.period === 'custom') {
    params.append('start_date', filters.value.startDate)
    params.append('end_date', filters.value.endDate)
  }

  window.open(`${route('trips.reports')}?${params.toString()}`, '_blank')
}

const initCharts = () => {
  // Revenue Trend Chart
  if (revenueChart.value) {
    const ctx = revenueChart.value.getContext('2d')
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: reportData.value.revenue_trend?.labels || [],
        datasets: [
          {
            label: 'Revenue',
            data: reportData.value.revenue_trend?.revenue || [],
            borderColor: '#3b82f6', // Blue
            backgroundColor: 'rgba(59, 130, 246, 0.2)',
            tension: 0.4,
            fill: true
          },
          {
            label: 'Expenses',
            data: reportData.value.revenue_trend?.expenses || [],
            borderColor: '#ef4444', // Red
            backgroundColor: 'rgba(239, 68, 68, 0.2)',
            tension: 0.4,
            fill: true
          },
          {
            label: 'Profit',
            data: reportData.value.revenue_trend?.profit || [],
            borderColor: '#22c55e', // Green
            backgroundColor: 'rgba(34, 197, 94, 0.2)',
            tension: 0.4,
            fill: true
          }
        ]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: true,
            position: 'top'
          },
          tooltip: {
            mode: 'index',
            intersect: false
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            title: {
              display: true,
              text: 'Amount (Rs.)'
            }
          }
        }
      }
    })
  }

  // Plant Distribution Chart
  if (plantChart.value) {
    const ctx = plantChart.value.getContext('2d')
    const plants = reportData.value.plant_stats || []
    new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: plants.map(p => p.name),
        datasets: [{
          data: plants.map(p => p.total_trips),
          backgroundColor: [
            '#ef4444', '#f97316', '#eab308', '#22c55e',
            '#3b82f6', '#8b5cf6', '#ec4899', '#10b981',
            '#6366f1', '#f59e0b'
          ],
          borderColor: '#ffffff',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: true,
            position: 'right'
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                return `${context.label}: ${context.raw} trips`;
              }
            }
          }
        }
      }
    })
  }

  // Daily Income Chart
  if (dailyIncomeChart.value) {
    const ctx = dailyIncomeChart.value.getContext('2d')
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: reportData.value.daily_income?.labels || [],
        datasets: [{
          label: 'Income',
          data: reportData.value.daily_income?.data || [],
          backgroundColor: '#22c55e', // Green
          borderColor: '#ffffff',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: true
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            title: {
              display: true,
              text: 'Income (Rs.)'
            }
          }
        }
      }
    })
  }
}

// Watchers
watch(() => filters.value.search, () => {
  // Search is handled by computed properties
})

// Lifecycle
onMounted(() => {
  // Load Chart.js
  if (!window.Chart) {
    const script = document.createElement('script')
    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js'
    script.onload = () => {
      nextTick(() => {
        initCharts()
      })
    }
    document.head.appendChild(script)
  } else {
    nextTick(() => {
      initCharts()
    })
  }
})
</script>
