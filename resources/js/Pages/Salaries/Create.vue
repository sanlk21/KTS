<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Create Driver Salary Configuration</h1>
        <p class="text-gray-600 mt-1">Set up salary configuration for a driver</p>
      </div>
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

    <div class="max-w-2xl mx-auto">
      <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">Salary Configuration Details</h3>
        </div>

        <form @submit.prevent="submit" class="p-6 space-y-6">
          <!-- Driver Selection -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Driver <span class="text-red-500">*</span>
            </label>
            <select
              v-model="form.driver_id"
              :class="[
                'w-full border rounded-lg px-3 py-2',
                errors.driver_id ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'
              ]"
              required
            >
              <option value="">Select a driver</option>
              <option v-for="driver in drivers" :key="driver.id" :value="driver.id">
                {{ driver.name }} (ID: {{ driver.id }})
              </option>
            </select>
            <p v-if="errors.driver_id" class="mt-1 text-sm text-red-600">{{ errors.driver_id }}</p>
          </div>

          <!-- Salary Type -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Salary Type <span class="text-red-500">*</span>
            </label>
            <div class="grid grid-cols-3 gap-4">
              <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50"
                     :class="form.salary_type === 'daily' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300'">
                <input
                  type="radio"
                  v-model="form.salary_type"
                  value="daily"
                  class="sr-only"
                >
                <div class="flex items-center">
                  <div class="w-4 h-4 border-2 rounded-full mr-2"
                       :class="form.salary_type === 'daily' ? 'border-indigo-500 bg-indigo-500' : 'border-gray-300'">
                    <div v-if="form.salary_type === 'daily'" class="w-2 h-2 bg-white rounded-full m-0.5"></div>
                  </div>
                  <span class="text-sm font-medium">Daily</span>
                </div>
              </label>

              <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50"
                     :class="form.salary_type === 'weekly' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300'">
                <input
                  type="radio"
                  v-model="form.salary_type"
                  value="weekly"
                  class="sr-only"
                >
                <div class="flex items-center">
                  <div class="w-4 h-4 border-2 rounded-full mr-2"
                       :class="form.salary_type === 'weekly' ? 'border-indigo-500 bg-indigo-500' : 'border-gray-300'">
                    <div v-if="form.salary_type === 'weekly'" class="w-2 h-2 bg-white rounded-full m-0.5"></div>
                  </div>
                  <span class="text-sm font-medium">Weekly</span>
                </div>
              </label>

              <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50"
                     :class="form.salary_type === 'monthly' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300'">
                <input
                  type="radio"
                  v-model="form.salary_type"
                  value="monthly"
                  class="sr-only"
                >
                <div class="flex items-center">
                  <div class="w-4 h-4 border-2 rounded-full mr-2"
                       :class="form.salary_type === 'monthly' ? 'border-indigo-500 bg-indigo-500' : 'border-gray-300'">
                    <div v-if="form.salary_type === 'monthly'" class="w-2 h-2 bg-white rounded-full m-0.5"></div>
                  </div>
                  <span class="text-sm font-medium">Monthly</span>
                </div>
              </label>
            </div>
            <p v-if="errors.salary_type" class="mt-1 text-sm text-red-600">{{ errors.salary_type }}</p>
          </div>

          <!-- Salary Amounts -->
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Daily Salary -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Daily Salary <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <span class="absolute left-3 top-2 text-gray-500">Rs.</span>
                <input
                  type="number"
                  step="0.01"
                  min="0"
                  v-model="form.daily_salary"
                  :class="[
                    'w-full border rounded-lg pl-12 pr-3 py-2',
                    errors.daily_salary ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'
                  ]"
                  placeholder="0.00"
                  required
                >
              </div>
              <p v-if="errors.daily_salary" class="mt-1 text-sm text-red-600">{{ errors.daily_salary }}</p>
            </div>

            <!-- Weekly Salary -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Weekly Salary</label>
              <div class="relative">
                <span class="absolute left-3 top-2 text-gray-500">Rs.</span>
                <input
                  type="number"
                  step="0.01"
                  min="0"
                  v-model="form.weekly_salary"
                  :class="[
                    'w-full border rounded-lg pl-12 pr-3 py-2',
                    errors.weekly_salary ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'
                  ]"
                  placeholder="0.00"
                >
              </div>
              <p v-if="errors.weekly_salary" class="mt-1 text-sm text-red-600">{{ errors.weekly_salary }}</p>
            </div>

            <!-- Monthly Salary -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Monthly Salary</label>
              <div class="relative">
                <span class="absolute left-3 top-2 text-gray-500">Rs.</span>
                <input
                  type="number"
                  step="0.01"
                  min="0"
                  v-model="form.monthly_salary"
                  :class="[
                    'w-full border rounded-lg pl-12 pr-3 py-2',
                    errors.monthly_salary ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'
                  ]"
                  placeholder="0.00"
                >
              </div>
              <p v-if="errors.monthly_salary" class="mt-1 text-sm text-red-600">{{ errors.monthly_salary }}</p>
            </div>
          </div>

          <!-- Advance Amount -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Advance Amount</label>
            <div class="relative">
              <span class="absolute left-3 top-2 text-gray-500">Rs.</span>
              <input
                type="number"
                step="0.01"
                min="0"
                v-model="form.advance_amount"
                :class="[
                  'w-full border rounded-lg pl-12 pr-3 py-2',
                  errors.advance_amount ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500'
                ]"
                placeholder="0.00"
              >
            </div>
            <p class="mt-1 text-sm text-gray-500">Any advance amount given to the driver</p>
            <p v-if="errors.advance_amount" class="mt-1 text-sm text-red-600">{{ errors.advance_amount }}</p>
          </div>

          <!-- Driver Salary Summary from Trips -->
          <div v-if="selectedDriverStats" class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <h4 class="text-sm font-medium text-blue-900 mb-3">Driver Performance Summary (Last 30 Days)</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
              <div>
                <p class="text-blue-600 font-medium">Total Trips</p>
                <p class="text-blue-900 font-semibold">{{ selectedDriverStats.total_trips }}</p>
              </div>
              <div>
                <p class="text-blue-600 font-medium">Total Earned</p>
                <p class="text-blue-900 font-semibold">Rs. {{ formatNumber(selectedDriverStats.total_earned) }}</p>
              </div>
              <div>
                <p class="text-blue-600 font-medium">Avg. Daily Earning</p>
                <p class="text-blue-900 font-semibold">Rs. {{ formatNumber(selectedDriverStats.avg_daily_earning) }}</p>
              </div>
              <div>
                <p class="text-blue-600 font-medium">Current Balance</p>
                <p class="font-semibold" :class="selectedDriverStats.current_balance >= 0 ? 'text-red-600' : 'text-green-600'">
                  Rs. {{ formatNumber(Math.abs(selectedDriverStats.current_balance)) }}
                  <span class="text-xs">{{ selectedDriverStats.current_balance >= 0 ? '(Owed)' : '(Owing)' }}</span>
                </p>
              </div>
            </div>
          </div>

          <!-- Salary Calculation Preview -->
          <div v-if="form.daily_salary || form.weekly_salary || form.monthly_salary" class="bg-gray-50 border border-gray-200 rounded-lg p-4">
            <h4 class="text-sm font-medium text-gray-900 mb-3">Salary Preview</h4>
            <div class="grid grid-cols-3 gap-4 text-sm">
              <div>
                <p class="text-gray-600">Daily Rate</p>
                <p class="text-gray-900 font-semibold">Rs. {{ formatNumber(calculateDailyRate()) }}</p>
              </div>
              <div>
                <p class="text-gray-600">Weekly Rate</p>
                <p class="text-gray-900 font-semibold">Rs. {{ formatNumber(calculateWeeklyRate()) }}</p>
              </div>
              <div>
                <p class="text-gray-600">Monthly Rate</p>
                <p class="text-gray-900 font-semibold">Rs. {{ formatNumber(calculateMonthlyRate()) }}</p>
              </div>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
            <Link
              :href="route('driver-salaries.index')"
              class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 flex items-center space-x-2"
            >
              <span>Cancel</span>
            </Link>
            <button
              type="submit"
              :disabled="processing"
              class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2"
            >
              <svg v-if="processing" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ processing ? 'Creating...' : 'Create Salary Configuration' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  drivers: Array,
  errors: Object
})

const processing = ref(false)
const selectedDriverStats = ref(null)

const form = useForm({
  driver_id: '',
  daily_salary: '',
  weekly_salary: '',
  monthly_salary: '',
  salary_type: 'daily',
  advance_amount: ''
})

const formatNumber = (value) => {
  return new Intl.NumberFormat('en-US', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  }).format(value || 0)
}

const calculateDailyRate = () => {
  if (form.salary_type === 'daily' && form.daily_salary) {
    return parseFloat(form.daily_salary)
  } else if (form.salary_type === 'weekly' && form.weekly_salary) {
    return parseFloat(form.weekly_salary) / 7
  } else if (form.salary_type === 'monthly' && form.monthly_salary) {
    return parseFloat(form.monthly_salary) / 30
  }
  return 0
}

const calculateWeeklyRate = () => {
  if (form.salary_type === 'weekly' && form.weekly_salary) {
    return parseFloat(form.weekly_salary)
  } else if (form.salary_type === 'daily' && form.daily_salary) {
    return parseFloat(form.daily_salary) * 7
  } else if (form.salary_type === 'monthly' && form.monthly_salary) {
    return parseFloat(form.monthly_salary) / 4.33
  }
  return 0
}

const calculateMonthlyRate = () => {
  if (form.salary_type === 'monthly' && form.monthly_salary) {
    return parseFloat(form.monthly_salary)
  } else if (form.salary_type === 'daily' && form.daily_salary) {
    return parseFloat(form.daily_salary) * 30
  } else if (form.salary_type === 'weekly' && form.weekly_salary) {
    return parseFloat(form.weekly_salary) * 4.33
  }
  return 0
}

// Watch for driver selection and fetch their stats
watch(() => form.driver_id, async (newDriverId) => {
  if (newDriverId) {
    try {
      // Fetch driver stats from trip data (you'll need to create this endpoint)
      const response = await fetch(`/api/drivers/${newDriverId}/stats`)
      if (response.ok) {
        selectedDriverStats.value = await response.json()
      }
    } catch (error) {
      console.error('Error fetching driver stats:', error)
    }
  } else {
    selectedDriverStats.value = null
  }
})

const submit = () => {
  processing.value = true

  form.post(route('driver-salaries.store'), {
    onFinish: () => {
      processing.value = false
    }
  })
}
</script>
