<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Driver Salaries</h1>
        <p class="text-gray-600 mt-1">Manage driver salary configurations and records</p>
      </div>
      <div class="flex space-x-3">
        <!-- Sync Dropdown -->
        <div class="relative">
          <button
            @click="showSyncOptions = !showSyncOptions"
            :disabled="syncing"
            class="bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white px-4 py-2 rounded-lg flex items-center space-x-2"
          >
            <svg v-if="syncing" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>{{ syncing ? 'Syncing...' : 'Sync Records' }}</span>
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </button>

          <!-- Sync Options Dropdown -->
          <div v-if="showSyncOptions" class="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg z-10 border border-gray-200">
            <div class="py-1">
              <button
                @click="syncSalaryRecords('today')"
                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
              >
                Sync Today's Records
              </button>
              <button
                @click="syncSalaryRecords('week')"
                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
              >
                Sync Last 7 Days
              </button>
              <button
                @click="syncSalaryRecords('month')"
                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
              >
                Sync Last 30 Days
              </button>
              <button
                @click="openCustomSyncModal"
                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
              >
                Custom Date Range
              </button>
            </div>
          </div>
        </div>

        <Link
          :href="createRoute"
          class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2"
        >
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
          <span>Add Driver Salary</span>
        </Link>
      </div>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="p-2 bg-blue-100 rounded-lg">
            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Total Earned</p>
            <p class="text-2xl font-semibold text-gray-900">Rs. {{ formatNumber(summaryStats?.total_earned || 0) }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="p-2 bg-green-100 rounded-lg">
            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Total Paid</p>
            <p class="text-2xl font-semibold text-gray-900">Rs. {{ formatNumber(summaryStats?.total_paid || 0) }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="p-2 bg-yellow-100 rounded-lg">
            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Outstanding Balance</p>
            <p class="text-2xl font-semibold" :class="(summaryStats?.total_balance || 0) >= 0 ? 'text-red-600' : 'text-green-600'">
              Rs. {{ formatNumber(Math.abs(summaryStats?.total_balance || 0)) }}
              <span class="text-sm">{{ (summaryStats?.total_balance || 0) >= 0 ? '(Owed)' : '(Owing)' }}</span>
            </p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="p-2 bg-purple-100 rounded-lg">
            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Total Trips</p>
            <p class="text-2xl font-semibold text-gray-900">{{ (summaryStats?.total_trips || 0).toLocaleString() }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow mb-6">
      <div class="p-6 border-b">
        <h3 class="text-lg font-medium text-gray-900">Filters</h3>
      </div>
      <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Period</label>
            <select v-model="filters.period" @change="applyFilters" class="w-full border-gray-300 rounded-lg">
              <option value="daily">Last 7 Days</option>
              <option value="weekly">This Week</option>
              <option value="monthly">This Month</option>
              <option value="today">Today</option>
              <option value="custom">Custom Range</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Driver</label>
            <select v-model="filters.driver_id" @change="applyFilters" class="w-full border-gray-300 rounded-lg">
              <option value="">All Drivers</option>
              <option v-for="driver in drivers" :key="driver.id" :value="driver.id">
                {{ driver.name }}
              </option>
            </select>
          </div>

          <div v-if="filters.period === 'custom'">
            <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
            <input
              type="date"
              v-model="filters.start_date"
              @change="applyFilters"
              class="w-full border-gray-300 rounded-lg"
            >
          </div>

          <div v-if="filters.period === 'custom'">
            <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
            <input
              type="date"
              v-model="filters.end_date"
              @change="applyFilters"
              class="w-full border-gray-300 rounded-lg"
            >
          </div>
        </div>
      </div>
    </div>

    <!-- Salary Records Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Salary Records</h3>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Driver</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trips</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Earned</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expected</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paid</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="record in (salaryRecords?.data || [])" :key="record.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap">
                <div>
                  <div class="text-sm font-medium text-gray-900">{{ record.driver?.name || 'N/A' }}</div>
                  <div class="text-sm text-gray-500">ID: {{ record.driver_id }}</div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ formatDate(record.record_date) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ record.total_trips || 0 }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                Rs. {{ formatNumber(record.earned_amount) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                Rs. {{ formatNumber(record.expected_amount) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                Rs. {{ formatNumber(record.paid_amount) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm" :class="(record.balance_amount || 0) >= 0 ? 'text-red-600' : 'text-green-600'">
                Rs. {{ formatNumber(Math.abs(record.balance_amount || 0)) }}
                <span class="text-xs">{{ (record.balance_amount || 0) >= 0 ? '(Owed)' : '(Owing)' }}</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="getStatusBadgeClass(record.payment_status)">
                  {{ record.payment_status || 'pending' }}
                </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                <Link
                  :href="getShowRoute(record.driver_id)"
                  class="text-indigo-600 hover:text-indigo-900"
                >
                  View
                </Link>
                <button
                  @click="openPaymentModal(record)"
                  class="text-green-600 hover:text-green-900"
                >
                  Pay
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6" v-if="salaryRecords?.links">
        <div class="flex items-center justify-between">
          <div class="flex-1 flex justify-between sm:hidden">
            <Link
              v-if="salaryRecords?.prev_page_url"
              :href="salaryRecords.prev_page_url"
              class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
            >
              Previous
            </Link>
            <Link
              v-if="salaryRecords?.next_page_url"
              :href="salaryRecords.next_page_url"
              class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
            >
              Next
            </Link>
          </div>
          <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
              <p class="text-sm text-gray-700">
                Showing {{ salaryRecords?.from || 0 }} to {{ salaryRecords?.to || 0 }} of {{ salaryRecords?.total || 0 }} results
              </p>
            </div>
            <div>
              <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                <Link
                  v-for="link in (salaryRecords?.links || [])"
                  :key="link.label"
                  :href="link.url || '#'"
                  :class="[
                    'relative inline-flex items-center px-4 py-2 border text-sm font-medium',
                    link.active
                      ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600'
                      : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'
                  ]"
                  v-html="link.label"
                />
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Payment Modal -->
    <div v-if="showPaymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Make Payment</h3>
          <form @submit.prevent="submitPayment">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Driver</label>
              <input
                type="text"
                :value="selectedRecord?.driver?.name"
                readonly
                class="w-full border-gray-300 rounded-lg bg-gray-50"
              >
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Amount</label>
              <input
                type="number"
                step="0.01"
                v-model="paymentForm.amount"
                required
                class="w-full border-gray-300 rounded-lg"
                placeholder="Enter amount"
              >
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Payment Date</label>
              <input
                type="date"
                v-model="paymentForm.payment_date"
                required
                class="w-full border-gray-300 rounded-lg"
              >
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Payment Type</label>
              <select v-model="paymentForm.payment_type" required class="w-full border-gray-300 rounded-lg">
                <option value="salary">Salary</option>
                <option value="advance">Advance</option>
                <option value="bonus">Bonus</option>
                <option value="deduction">Deduction</option>
                <option value="adjustment">Adjustment</option>
              </select>
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
              <input
                type="text"
                v-model="paymentForm.payment_method"
                required
                class="w-full border-gray-300 rounded-lg"
                placeholder="e.g., Cash, Bank Transfer"
              >
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
              <textarea
                v-model="paymentForm.description"
                class="w-full border-gray-300 rounded-lg"
                rows="3"
                placeholder="Optional description"
              ></textarea>
            </div>

            <div class="flex justify-end space-x-3">
              <button
                type="button"
                @click="closePaymentModal"
                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="processing"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
              >
                {{ processing ? 'Processing...' : 'Make Payment' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Custom Sync Modal -->
    <div v-if="showCustomSyncModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Custom Sync Range</h3>
          <form @submit.prevent="submitCustomSync">
            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
              <input
                type="date"
                v-model="customSyncForm.start_date"
                required
                class="w-full border-gray-300 rounded-lg"
              >
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
              <input
                type="date"
                v-model="customSyncForm.end_date"
                required
                class="w-full border-gray-300 rounded-lg"
              >
            </div>

            <div class="mb-4">
              <label class="block text-sm font-medium text-gray-700 mb-2">Driver (Optional)</label>
              <select v-model="customSyncForm.driver_id" class="w-full border-gray-300 rounded-lg">
                <option value="">All Drivers</option>
                <option v-for="driver in drivers" :key="driver.id" :value="driver.id">
                  {{ driver.name }}
                </option>
              </select>
            </div>

            <div class="flex justify-end space-x-3">
              <button
                type="button"
                @click="closeCustomSyncModal"
                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="syncing"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50"
              >
                {{ syncing ? 'Syncing...' : 'Sync Records' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'

const props = defineProps({
  salaryRecords: {
    type: Object,
    default: () => ({ data: [], links: null })
  },
  summaryStats: {
    type: Object,
    default: () => ({
      total_earned: 0,
      total_paid: 0,
      total_balance: 0,
      total_trips: 0
    })
  },
  drivers: {
    type: Array,
    default: () => []
  },
  filters: {
    type: Object,
    default: () => ({})
  }
})

const syncing = ref(false)
const showPaymentModal = ref(false)
const showCustomSyncModal = ref(false)
const showSyncOptions = ref(false)
const selectedRecord = ref(null)
const processing = ref(false)

const filters = ref({
  period: props.filters.period || 'daily',
  driver_id: props.filters.driver_id || '',
  start_date: props.filters.start_date || '',
  end_date: props.filters.end_date || ''
})

const paymentForm = ref({
  driver_id: '',
  salary_record_id: '',
  amount: '',
  payment_date: new Date().toISOString().split('T')[0],
  payment_type: 'salary',
  payment_method: 'cash',
  description: ''
})

const customSyncForm = ref({
  start_date: '',
  end_date: '',
  driver_id: ''
})

// Close dropdowns when clicking outside
const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    showSyncOptions.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

// Computed properties for routes (with fallback URLs)
const createRoute = computed(() => {
  try {
    return window.route ? window.route('Salaries.create') : '/salaries/create'
  } catch (error) {
    return '/salaries/create'
  }
})

const indexRoute = computed(() => {
  try {
    return window.route ? window.route('Salaries.index') : '/salaries'
  } catch (error) {
    return '/salaries'
  }
})

const formatNumber = (value) => {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(value || 0)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const getStatusBadgeClass = (status) => {
  const classes = {
    pending: 'bg-yellow-100 text-yellow-800',
    partial: 'bg-orange-100 text-orange-800',
    paid: 'bg-green-100 text-green-800',
    overpaid: 'bg-blue-100 text-blue-800'
  }
  return classes[status] || 'bg-gray-100 text-gray-800'
}

const getShowRoute = (driverId) => {
  try {
    return window.route ? window.route('Salaries.show', driverId) : `/salaries/${driverId}`
  } catch (error) {
    return `/salaries/${driverId}`
  }
}

const applyFilters = () => {
  try {
    if (window.route) {
      router.get(window.route('Salaries.index'), filters.value, {
        preserveState: true,
        preserveScroll: true
      })
    } else {
      // Fallback: construct URL manually
      const url = new URL('/salaries', window.location.origin)
      Object.keys(filters.value).forEach(key => {
        if (filters.value[key]) {
          url.searchParams.set(key, filters.value[key])
        }
      })
      router.get(url.toString(), {}, {
        preserveState: true,
        preserveScroll: true
      })
    }
  } catch (error) {
    console.error('Error applying filters:', error)
    // Simple fallback
    window.location.href = '/salaries'
  }
}

const syncSalaryRecords = async (period = 'today') => {
  if (syncing.value) return

  syncing.value = true
  showSyncOptions.value = false

  try {
    const syncUrl = window.route ? window.route('Salaries.sync-records') : '/salaries/sync-records'

    let params = {}

    // Set parameters based on period
    switch (period) {
      case 'today':
        params.date = new Date().toISOString().split('T')[0]
        break
      case 'week':
        params.date_range = 7
        break
      case 'month':
        params.date_range = 30
        break
    }

    // Add current driver filter if applied
    if (filters.value.driver_id) {
      params.driver_id = filters.value.driver_id
    }

    router.post(syncUrl, params, {
      onSuccess: (page) => {
        // Refresh the page data to show updated records
        router.get(window.location.href, {}, {
          preserveState: false,
          preserveScroll: true
        })
      },
      onError: (errors) => {
        console.error('Sync errors:', errors)
      },
      onFinish: () => {
        syncing.value = false
      }
    })
  } catch (error) {
    console.error('Error syncing records:', error)
    syncing.value = false
  }
}

const openCustomSyncModal = () => {
  showSyncOptions.value = false
  customSyncForm.value = {
    start_date: '',
    end_date: '',
    driver_id: filters.value.driver_id || ''
  }
  showCustomSyncModal.value = true
}

const closeCustomSyncModal = () => {
  showCustomSyncModal.value = false
  customSyncForm.value = {
    start_date: '',
    end_date: '',
    driver_id: ''
  }
}

const submitCustomSync = async () => {
  if (syncing.value) return

  syncing.value = true

  try {
    const syncUrl = window.route ? window.route('Salaries.sync-records') : '/salaries/sync-records'

    router.post(syncUrl, customSyncForm.value, {
      onSuccess: (page) => {
        closeCustomSyncModal()
        // Refresh the page data
        router.get(window.location.href, {}, {
          preserveState: false,
          preserveScroll: true
        })
      },
      onError: (errors) => {
        console.error('Custom sync errors:', errors)
      },
      onFinish: () => {
        syncing.value = false
      }
    })
  } catch (error) {
    console.error('Error with custom sync:', error)
    syncing.value = false
  }
}

const openPaymentModal = (record) => {
  selectedRecord.value = record
  paymentForm.value.driver_id = record.driver_id
  paymentForm.value.salary_record_id = record.id
  paymentForm.value.amount = record.balance_amount > 0 ? record.balance_amount : ''
  showPaymentModal.value = true
}

const closePaymentModal = () => {
  showPaymentModal.value = false
  selectedRecord.value = null
  paymentForm.value = {
    driver_id: '',
    salary_record_id: '',
    amount: '',
    payment_date: new Date().toISOString().split('T')[0],
    payment_type: 'salary',
    payment_method: 'cash',
    description: ''
  }
}

const submitPayment = async () => {
  if (processing.value) return

  processing.value = true

  try {
    const paymentUrl = window.route ? window.route('Salaries.make-payment') : '/salaries/make-payment'

    router.post(paymentUrl, paymentForm.value, {
      onSuccess: (page) => {
        closePaymentModal()
        // Refresh the page data to show updated balances
        router.get(window.location.href, {}, {
          preserveState: false,
          preserveScroll: true
        })
      },
      onError: (errors) => {
        console.error('Payment submission errors:', errors)
      },
      onFinish: () => {
        processing.value = false
      }
    })
  } catch (error) {
    console.error('Error submitting payment:', error)
    processing.value = false
  }
}
</script>
