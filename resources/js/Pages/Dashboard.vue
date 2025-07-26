<template>
    <AuthenticatedLayout>
        <Head title="Dashboard" />

        <div class="container mx-auto p-6">
            <h1 class="text-3xl font-bold text-gray-800 mb-6">
                KTS Transport Dashboard
            </h1>

            <!-- Loading State -->
            <div v-if="loading" class="flex justify-center items-center py-12">
                <div class="inline-flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-lg text-gray-600">Loading dashboard data...</span>
                </div>
            </div>

            <div v-else>
                <!-- Summary Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <Link :href="route('tippers.index')"
                        class="bg-white shadow-lg rounded-lg p-6 flex items-center cursor-pointer hover:bg-blue-50 hover:shadow-xl transition duration-200 transform hover:-translate-y-1">
                        <div class="mr-4">
                            <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">
                                {{ summary.tippers }}
                            </h3>
                            <p class="text-sm text-gray-600">Total Tippers</p>
                        </div>
                    </Link>

                    <Link :href="route('drivers.index')"
                        class="bg-white shadow-lg rounded-lg p-6 flex items-center cursor-pointer hover:bg-green-50 hover:shadow-xl transition duration-200 transform hover:-translate-y-1">
                        <div class="mr-4">
                            <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">
                                {{ summary.drivers }}
                            </h3>
                            <p class="text-sm text-gray-600">Total Drivers</p>
                        </div>
                    </Link>

                    <Link :href="route('plants.index')"
                        class="bg-white shadow-lg rounded-lg p-6 flex items-center cursor-pointer hover:bg-yellow-50 hover:shadow-xl transition duration-200 transform hover:-translate-y-1">
                        <div class="mr-4">
                            <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">
                                {{ summary.plants }}
                            </h3>
                            <p class="text-sm text-gray-600">Total Plants</p>
                        </div>
                    </Link>

                    <Link :href="route('trips.index')"
                        class="bg-white shadow-lg rounded-lg p-6 flex items-center cursor-pointer hover:bg-purple-50 hover:shadow-xl transition duration-200 transform hover:-translate-y-1">
                        <div class="mr-4">
                            <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900">
                                {{ summary.trips }}
                            </h3>
                            <p class="text-sm text-gray-600">Total Trips</p>
                        </div>
                    </Link>
                </div>

                <!-- License Expiry Reminders -->
                <div v-if="expiringTippers.length" class="bg-red-50 border-l-4 border-red-500 p-6 mb-6 rounded-r-lg">
                    <div class="flex items-center mb-3">
                        <svg class="w-6 h-6 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.084 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <h2 class="text-lg font-semibold text-red-700">
                            License Expiry Reminders ({{ expiringTippers.length }})
                        </h2>
                    </div>
                    <div class="space-y-2">
                        <div v-for="tipper in expiringTippers" :key="tipper.tipper_number"
                            class="flex items-center justify-between bg-white p-3 rounded border-l-2 border-red-400">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-red-600 font-semibold text-sm">{{ tipper.tipper_number.charAt(0) }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-red-800">Tipper {{ tipper.tipper_number }}</p>
                                    <p class="text-sm text-red-600">Expires: {{ formatDate(tipper.license_expiry) }}</p>
                                </div>
                            </div>
                            <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">
                                {{ getDaysUntilExpiry(tipper.license_expiry) }} days
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Period Selector and Reports -->
                <div class="mb-6 flex items-center justify-between">
                    <div class="flex items-center">
                        <label class="mr-3 text-gray-700 font-medium">Select Period:</label>
                        <select v-model="period" @change="fetchReports"
                            class="border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="daily">Daily</option>
                            <option value="weekly">Weekly</option>
                            <option value="monthly">Monthly</option>
                        </select>
                    </div>
                    <Link :href="route('trips.reports')"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                        View Detailed Reports
                    </Link>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <!-- Deliveries by Plant Chart -->
                    <div class="bg-white shadow-lg rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Deliveries by Plant
                        </h2>
                        <div class="relative" style="height: 300px;">
                            <canvas ref="plantChart" class="w-full h-full"></canvas>
                        </div>
                    </div>

                    <!-- Driver Salaries Chart -->
                    <div class="bg-white shadow-lg rounded-lg p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Driver Salaries ({{ period.charAt(0).toUpperCase() + period.slice(1) }})
                        </h2>
                        <div class="relative" style="height: 300px;">
                            <canvas ref="salaryChart" class="w-full h-full"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Financial Summary -->
                <div class="bg-white shadow-lg rounded-lg p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                        <svg class="w-5 h-5 text-purple-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                        Financial Summary ({{ period.charAt(0).toUpperCase() + period.slice(1) }})
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-green-100 text-sm">Total Revenue</p>
                                    <p class="text-2xl font-bold">₹{{ formatCurrency(reports.income.total_revenue) }}</p>
                                </div>
                                <div class="bg-green-400 bg-opacity-30 rounded-full p-2">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-blue-100 text-sm">Total Expenses</p>
                                    <p class="text-2xl font-bold">₹{{ formatCurrency(reports.income.total_expenses) }}</p>
                                </div>
                                <div class="bg-blue-400 bg-opacity-30 rounded-full p-2">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-4 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-purple-100 text-sm">Net Profit</p>
                                    <p class="text-2xl font-bold">₹{{ formatCurrency(reports.income.net_profit) }}</p>
                                </div>
                                <div class="bg-purple-400 bg-opacity-30 rounded-full p-2">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg p-4 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-orange-100 text-sm">Pending Payments</p>
                                    <p class="text-2xl font-bold">₹{{ formatCurrency(reports.income.pending) }}</p>
                                </div>
                                <div class="bg-orange-400 bg-opacity-30 rounded-full p-2">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import { computed, ref, onMounted, nextTick } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    dashboardData: {
        type: Object,
        default: () => ({})
    }
})

// Reactive data
const loading = ref(false)
const period = ref('monthly')
const plantChart = ref(null)
const salaryChart = ref(null)
const error = ref(null)

let chartInstances = {}

// Initialize data from props
const dashboardData = ref(props.dashboardData || {})

// Computed properties
const summary = computed(() => ({
    tippers: dashboardData.value.summary?.tippers || 0,
    drivers: dashboardData.value.summary?.drivers || 0,
    plants: dashboardData.value.summary?.plants || 0,
    trips: dashboardData.value.summary?.trips || 0
}))

const expiringTippers = computed(() => dashboardData.value.expiring_tippers || [])

const reports = computed(() => ({
    deliveries_by_plant: dashboardData.value.reports?.deliveries_by_plant || [],
    driver_salaries: dashboardData.value.reports?.driver_salaries || [],
    income: {
        total_revenue: dashboardData.value.reports?.income?.total_revenue || 0,
        total_expenses: dashboardData.value.reports?.income?.total_expenses || 0,
        net_profit: dashboardData.value.reports?.income?.net_profit || 0,
        pending: dashboardData.value.reports?.income?.pending || 0
    }
}))

// Methods
const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-IN').format(value || 0)
}

const formatDate = (dateString) => {
    if (!dateString) return 'N/A'
    return new Date(dateString).toLocaleDateString('en-IN', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const getDaysUntilExpiry = (dateString) => {
    if (!dateString) return 0
    const today = new Date()
    const expiryDate = new Date(dateString)
    const diffTime = expiryDate - today
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))
    return Math.max(0, diffDays)
}

const fetchReports = () => {
    loading.value = true

    router.get(route('dashboard'), { period: period.value }, {
        preserveState: true,
        onSuccess: (page) => {
            dashboardData.value = page.props.dashboardData || {}
            loading.value = false
            nextTick(() => {
                destroyCharts()
                renderCharts()
            })
        },
        onError: () => {
            loading.value = false
            error.value = 'Failed to load dashboard data'
        }
    })
}

const destroyCharts = () => {
    Object.values(chartInstances).forEach(chart => {
        if (chart) {
            chart.destroy()
        }
    })
    chartInstances = {}
}

const renderCharts = () => {
    if (!window.Chart) return

    // Plant Deliveries Chart
    if (plantChart.value && reports.value.deliveries_by_plant.length > 0) {
        const ctx = plantChart.value.getContext('2d')
        chartInstances.plant = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: reports.value.deliveries_by_plant.map(p => p.name),
                datasets: [{
                    label: 'Trips',
                    data: reports.value.deliveries_by_plant.map(p => p.trip_count),
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.label}: ${context.raw} trips`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Trips'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Plants'
                        }
                    }
                }
            }
        })
    }

    // Driver Salaries Chart
    if (salaryChart.value && reports.value.driver_salaries.length > 0) {
        const ctx = salaryChart.value.getContext('2d')
        chartInstances.salary = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: reports.value.driver_salaries.map(d => d.name),
                datasets: [{
                    label: 'Salary (₹)',
                    data: reports.value.driver_salaries.map(d => d.salary),
                    backgroundColor: 'rgba(34, 197, 94, 0.8)',
                    borderColor: 'rgba(34, 197, 94, 1)',
                    borderWidth: 1,
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.label}: ₹${new Intl.NumberFormat('en-IN').format(context.raw)}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Salary Amount (₹)'
                        },
                        ticks: {
                            callback: function(value) {
                                return '₹' + new Intl.NumberFormat('en-IN').format(value);
                            }
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Drivers'
                        }
                    }
                }
            }
        })
    }
}

// Lifecycle
onMounted(() => {
    // Load Chart.js
    if (!window.Chart) {
        const script = document.createElement('script')
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js'
        script.onload = () => {
            nextTick(() => {
                renderCharts()
            })
        }
        document.head.appendChild(script)
    } else {
        nextTick(() => {
            renderCharts()
        })
    }
})
</script>
