<template>
  <div class="p-6">
    <div class="flex justify-between items-center mb-6">
      <div>
        <h1 class="text-3xl font-bold text-gray-900">Edit Driver Salary Configuration</h1>
        <p class="text-gray-600 mt-1">Update salary configuration for {{ driverSalary.driver.name }}</p>
      </div>
      <div class="flex space-x-3">
        <Link
          :href="route('driver-salaries.show', driverSalary.driver_id)"
          class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2"
        >
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
          </svg>
          <span>View Details</span>
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

    <div class="max-w-4xl mx-auto">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Form -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
              <h3 class="text-lg font-medium text-gray-900">Salary Configuration</h3>
            </div>

            <form @submit.prevent="submit" class="p-6 space-y-6">
              <!-- Driver Info (Read-only) -->
              <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-900 mb-2">Driver Information</h4>
                <div class="grid grid-cols-2 gap-4 text-sm">
                  <div>
                    <p class="text-gray-600">Name</p>
                    <p class="text-gray-900 font-medium">{{ driverSalary.driver.name }}</p>
                  </div>
                  <div>
                    <p class="text-gray-600">Driver ID</p>
                    <p class="text-gray-900 font-medium">{{ driverSalary.driver.id }}</p>
                  </div>
                </div>
              </div>

              <!-- Status Toggle -->
              <div>
                <label class="flex items-center space-x-3">
                  <input
                    type="checkbox"
                    v-model="form.is_active"
                    class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                  >
                  <div>
                    <span class="text-sm font-medium text-gray-700">Active Configuration</span>
                    <p class="text-xs text-gray-500">Uncheck to deactivate this salary configuration</p>
                  </div>
                </label>
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

              <!-- Salary Calculation Preview -->
              <div v-if="form.daily_salary || form.weekly_salary || form.monthly_salary" class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <h4 class="text-sm font-medium text-gray-900 mb-3">Updated Salary Preview</h4>
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
                  <span>{{ processing ? 'Updating...' : 'Update Configuration' }}</span>
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Sidebar - Current Stats & History -->
        <div class="lg:col-span-1 space-y-6">
          <!-- Current Configuration -->
          <div class="bg-white rounded-lg shadow">
            <div class="px-4 py-3 border-b border-gray-200">
              <h4 class="text-sm font-medium text-gray-900">Current Configuration</h4>
            </div>
            <div class="p-4 space-y-3 text-sm">
              <div class="flex justify-between">
                <span class="text-gray-600">Salary Type:</span>
                <span class="font-medium capitalize">{{ driverSalary.salary_type }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Daily:</span>
                <span class="font-medium">Rs. {{ formatNumber(driverSalary.daily_salary) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Weekly:</span>
                <span class="font-medium">Rs. {{ formatNumber(driverSalary.weekly_salary) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Monthly:</span>
                <span class="font-medium">Rs. {{ formatNumber(driverSalary.monthly_salary) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Advance:</span>
                <span class="font-medium">Rs. {{ formatNumber(driverSalary.advance_amount) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Status:</span>
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                      :class="driverSalary.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                  {{ driverSalary.is_active ? 'Active' : 'Inactive' }}
                </span>
              </div>
            </div>
          </div>

          <!-- Recent Performance -->
          <div class="bg-white rounded-lg shadow">
            <div class="px-4 py-3 border-b border-gray-200">
              <h4 class="text-sm font-medium text-gray-900">Recent Performance</h4>
              <p class="text-xs text-gray-500">Last 7 days</p>
            </div>
            <div class="p-4 space-y-3 text-sm">
              <div class="flex justify-between">
                <span class="text-gray-600">Total Trips:</span>
                <span class="font-medium">{{ recentStats.total_trips || 0 }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Total Earned:</span>
                <span class="font-medium">Rs. {{ formatNumber(recentStats.total_earned || 0) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Avg. Per Trip:</span>
                <span class="font-medium">Rs. {{ formatNumber(recentStats.avg_per_trip || 0) }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-gray-600">Balance:</span>
                <span class="font-medium" :class="(recentStats.current_balance || 0) >= 0 ? 'text-red-600' : 'text-green-600'">
                  Rs. {{ formatNumber(Math.abs(recentStats.current_balance || 0)) }}
                </span>
              </div>
            </div>
          </div>

          <!-- Configuration History -->
          <div class="bg-white rounded-lg shadow">
            <div class="px-4 py-3 border-b border-gray-200">
              <h4 class="text-sm font-medium text-gray-900">Configuration History</h4>
            </div>
            <div class="p-4 text-sm">
              <div class="space-y-2">
                <div class="flex justify-between items-center text-xs">
                  <span class="text-gray-500">Created:</span>
                  <span>{{ formatDate(driverSalary.created_at) }}</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                  <span class="text-gray-500">Last Updated:</span>
                  <span>{{ formatDate(driverSalary.updated_at) }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Quick Actions -->
          <div class="bg-white rounded-lg shadow">
            <div class="px-4 py-3 border-b border-gray-200">
              <h4 class="text-sm font-medium text-gray-900">Quick Actions</h4>
            </div>
            <div class="p-4 space-y-2">
              <Link
                :href="route('driver-salaries.show', driverSalary.driver_id)"
                class="w-full text-left px-3 py-2 text-sm text-blue-600 hover:bg-blue-50 rounded border border-blue-200 hover:border-blue-300 transition-colors"
              >
                View Full Details
              </Link>
              <button
                @click="syncDriverSalary"
                class="w-full text-left px-3 py-2 text-sm text-green-600 hover:bg-green-50 rounded border border-green-200 hover:border-green-300 transition-colors"
              >
                Sync Latest Records
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
  driverSalary: Object,
  errors: Object
})

const processing = ref(false)
const recentStats = ref({
  total_trips: 0,
  total_earned: 0,
  avg_per_trip: 0,
  current_balance: 0
})

const form = useForm({
  daily_salary: props.driverSalary.daily_salary,
  weekly_salary: props.driverSalary.weekly_salary,
  monthly_salary: props.driverSalary.monthly_salary,
  salary_type: props.driverSalary.salary_type,
  advance_amount: props.driverSalary.advance_amount,
  is_active: props.driverSalary.is_active
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
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
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

const fetchRecentStats = async () => {
  try {
    // You'll need to create an endpoint for this or integrate with existing trip data
    const response = await fetch(`/api/drivers/${props.driverSalary.driver_id}/recent-stats`)
    if (response.ok) {
      const data = await response.json()
      recentStats.value = data
    }
  } catch (error) {
    console.error('Error fetching recent stats:', error)
  }
}

const syncDriverSalary = () => {
  router.post(route('driver-salaries.sync-records'), {
    driver_id: props.driverSalary.driver_id,
    date: new Date().toISOString().split('T')[0]
  })
}

const submit = () => {
  processing.value = true

  form.put(route('driver-salaries.update', props.driverSalary.id), {
    onFinish: () => {
      processing.value = false
    }
  })
}

onMounted(() => {
  fetchRecentStats()
})
</script>
