<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">{{ driver.name }} - Salary Details</h1>
        <p class="text-gray-600 mt-1">Driver ID: {{ driver.id }} | Salary management overview</p>
      </div>
      <div class="flex space-x-3">
        <button
          @click="openPaymentModal"
          class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2"
        >
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
          </svg>
          <span>Make Payment</span>
        </button>
        <Link
          :href="route('driver-salaries.edit', driver.driver_salary.id)"
          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2"
        >
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
          </svg>
          <span>Edit Configuration</span>
        </Link>
        <Link
          :href="route('driver-salaries.index')"
          class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2"
        >
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
          </svg>
          <span>Back to List</span>
        </Link>
      </div>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="p-2 bg-blue-100 rounded-lg">
            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Total Earned</p>
            <p class="text-2xl font-semibold text-gray-900">Rs. {{ formatNumber(stats.total_earned) }}</p>
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
            <p class="text-2xl font-semibold text-gray-900">Rs. {{ formatNumber(stats.total_paid) }}</p>
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
            <p class="text-sm font-medium text-gray-500">Current Balance</p>
            <p class="text-2xl font-semibold" :class="stats.current_balance >= 0 ? 'text-red-600' : 'text-green-600'">
              Rs. {{ formatNumber(Math.abs(stats.current_balance)) }}
              <span class="text-sm">{{ stats.current_balance >= 0 ? '(Owed)' : '(Owing)' }}</span>
            </p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="p-2 bg-purple-100 rounded-lg">
            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Avg. Daily</p>
            <p class="text-2xl font-semibold text-gray-900">Rs. {{ formatNumber(stats.avg_daily_earning) }}</p>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center">
          <div class="p-2 bg-indigo-100 rounded-lg">
            <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-500">Total Trips</p>
            <p class="text-2xl font-semibold text-gray-900">{{ stats.total_trips.toLocaleString() }}</p>
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Main Content -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Salary Records -->
        <div class="bg-white rounded-lg shadow">
          <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900">Recent Salary Records (Last 30 Days)</h3>
            <button
              @click="syncRecords"
              :disabled="syncing"
              class="text-sm bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded"
            >
              {{ syncing ? 'Syncing...' : 'Sync Latest' }}
            </button>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
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
                <tr v-for="record in salaryRecords" :key="record.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ formatDate(record.record_date) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ record.total_trips }}
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
                  <td class="px-6 py-4 whitespace-nowrap text-sm" :class="record.balance_amount >= 0 ? 'text-red-600' : 'text-green-600'">
                    Rs. {{ formatNumber(Math.abs(record.balance_amount)) }}
                    <span class="text-xs">{{ record.balance_amount >= 0 ? '(Owed)' : '(Owing)' }}</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="getStatusBadgeClass(record.payment_status)">
                      {{ record.payment_status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
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
        </div>

        <!-- Recent Payments -->
        <div class="bg-white rounded-lg shadow">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Recent Payments</h3>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="payment in recentPayments" :key="payment.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ formatDate(payment.payment_date) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    Rs. {{ formatNumber(payment.amount) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" :class="getPaymentTypeBadgeClass(payment.payment_type)">
                      {{ payment.payment_type }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ payment.payment_method }}
                  </td>
                  <td class="px-6 py-4 text-sm text-gray-900">
                    {{ payment.description || '-' }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="lg:col-span-1 space-y-6">
        <!-- Salary Configuration -->
        <div class="bg-white rounded-lg shadow">
          <div class="px-4 py-3 border-b border-gray-200">
            <h4 class="text-sm font-medium text-gray-900">Salary Configuration</h4>
          </div>
          <div class="p-4 space-y-3 text-sm">
            <div class="flex justify-between">
              <span class="text-gray-600">Salary Type:</span>
              <span class="font-medium capitalize">{{ driver.driver_salary.salary_type }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Daily:</span>
              <span class="font-medium">Rs. {{ formatNumber(driver.driver_salary.daily_salary) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Weekly:</span>
              <span class="font-medium">Rs. {{ formatNumber(driver.driver_salary.weekly_salary) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Monthly:</span>
              <span class="font-medium">Rs. {{ formatNumber(driver.driver_salary.monthly_salary) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Advance:</span>
              <span class="font-medium">Rs. {{ formatNumber(driver.driver_salary.advance_amount) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Status:</span>
              <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                    :class="driver.driver_salary.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                {{ driver.driver_salary.is_active ? 'Active' : 'Inactive' }}
              </span>
            </div>
          </div>
        </div>

        <!-- Trip Summary from Trip Controller -->
        <div class="bg-white rounded-lg shadow">
          <div class="px-4 py-3 border-b border-gray-200">
            <h4 class="text-sm font-medium text-gray-900">Trip Performance (30 Days)</h4>
          </div>
          <div class="p-4 space-y-3 text-sm">
            <div class="flex justify-between">
              <span class="text-gray-600">Working Days:</span>
              <span class="font-medium">{{ stats.total_days }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Total Trips:</span>
              <span class="font-medium">{{ stats.total_trips }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Avg. Trips/Day:</span>
              <span class="font-medium">{{ formatNumber(stats.total_days > 0 ? stats.total_trips / stats.total_days : 0) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Total Salary:</span>
              <span class="font-medium">Rs. {{ formatNumber(stats.total_earned) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Expected:</span>
              <span class="font-medium">Rs. {{ formatNumber(stats.total_expected) }}</span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600">Performance:</span>
              <span class="font-medium" :class="getPerformanceColor(stats.total_earned, stats.total_expected)">
                {{ calculatePerformancePercentage(stats.total_earned, stats.total_expected) }}%
              </span>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow">
          <div class="px-4 py-3 border-b border-gray-200">
            <h4 class="text-sm font-medium text-gray-900">Quick Actions</h4>
          </div>
          <div class="p-4 space-y-2">
            <button
              @click="openPaymentModal"
              class="w-full text-left px-3 py-2 text-sm text-green-600 hover:bg-green-50 rounded border border-green-200 hover:border-green-300 transition-colors"
            >
              Make Payment
            </button>
            <Link
              :href="route('driver-salaries.edit', driver.driver_salary.id)"
              class="block w-full text-left px-3 py-2 text-sm text-blue-600 hover:bg-blue-50 rounded border border-blue-200 hover:border-blue-300 transition-colors"
            >
              Edit Configuration
            </Link>
            <button
              @click="syncRecords"
              class="w-full text-left px-3 py-2 text-sm text-purple-600 hover:bg-purple-50 rounded border border-purple-200 hover:border-purple-300 transition-colors"
            >
              Sync Records
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Payment Modal -->
    <div v-if="showPaymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Make Payment to {{ driver.name }}</h3>
          <form @submit.prevent="submitPayment">
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
  </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
  driver: Object,
  salaryRecords: Array,
  recentPayments: Array,
  stats: Object
})

const syncing = ref(false)
const processing = ref(false)
const showPaymentModal = ref(false)
const selectedRecord = ref(null)

const paymentForm = ref({
  driver_id: props.driver.id,
  salary_record_id: '',
  amount: '',
  payment_date: new Date().toISOString().split('T')[0],
  payment_type: 'salary',
  payment_method: 'cash',
  description: ''
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

const getPaymentTypeBadgeClass = (type) => {
  const classes = {
    salary: 'bg-blue-100 text-blue-800',
    advance: 'bg-purple-100 text-purple-800',
    bonus: 'bg-green-100 text-green-800',
    deduction: 'bg-red-100 text-red-800',
    adjustment: 'bg-gray-100 text-gray-800'
  }
  return classes[type] || 'bg-gray-100 text-gray-800'
}

const getPerformanceColor = (earned, expected) => {
  if (expected === 0) return 'text-gray-500'
  const percentage = (earned / expected) * 100
  if (percentage >= 100) return 'text-green-600'
  if (percentage >= 80) return 'text-yellow-600'
  return 'text-red-600'
}

const calculatePerformancePercentage = (earned, expected) => {
  if (expected === 0) return 0
  return Math.round((earned / expected) * 100)
}

const syncRecords = () => {
  syncing.value = true

  router.post(route('driver-salaries.sync-records'), {
    driver_id: props.driver.id,
    date: new Date().toISOString().split('T')[0]
  }, {
    onFinish: () => {
      syncing.value = false
    }
  })
}

const openPaymentModal = (record = null) => {
  selectedRecord.value = record
  paymentForm.value.salary_record_id = record ? record.id : ''
  paymentForm.value.amount = record && record.balance_amount > 0 ? record.balance_amount : ''
  showPaymentModal.value = true
}

const closePaymentModal = () => {
  showPaymentModal.value = false
  selectedRecord.value = null
  paymentForm.value = {
    driver_id: props.driver.id,
    salary_record_id: '',
    amount: '',
    payment_date: new Date().toISOString().split('T')[0],
    payment_type: 'salary',
    payment_method: 'cash',
    description: ''
  }
}

const submitPayment = () => {
  processing.value = true

  router.post(route('driver-salaries.make-payment'), paymentForm.value, {
    onSuccess: () => {
      closePaymentModal()
    },
    onFinish: () => {
      processing.value = false
    }
  })
}
</script>
