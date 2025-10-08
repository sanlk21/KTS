<template>
  <AuthenticatedLayout>
    <div class="container mx-auto px-4 py-8">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Create New Trip(s)</h1>
        <Link :href="route('trips.index')" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
          Back to Trips
        </Link>
      </div>

      <div class="bg-white rounded-lg shadow-md p-6">
        <!-- Driver Balance Alert -->
        <div v-if="driverBalance !== 0 && form.driver_id" class="mb-6">
          <div :class="balanceAlertClass" class="p-4 rounded-lg flex items-center justify-between">
            <div>
              <h3 class="font-semibold text-lg">Driver Balance: {{ formatCurrency(Math.abs(driverBalance)) }}</h3>
              <p class="text-sm mt-1">{{ balanceMessage }}</p>
            </div>
            <i :class="balanceIconClass" class="text-3xl"></i>
          </div>
        </div>

        <form @submit.prevent="submitForm">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Tipper Selection -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Tipper Number *</label>
              <select
                v-model="form.tipper_number"
                @change="onTipperChange"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-500': form.errors.tipper_number }"
              >
                <option value="">Select Tipper</option>
                <option v-for="tipper in tippers" :key="tipper.tipper_number" :value="tipper.tipper_number">
                  {{ tipper.tipper_number }} - {{ tipper.size }} Ton
                </option>
              </select>
              <p v-if="form.errors.tipper_number" class="mt-1 text-sm text-red-600">{{ form.errors.tipper_number }}</p>
            </div>

            <!-- Driver Selection -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Driver *</label>
              <select
                v-model="form.driver_id"
                @change="onDriverChange"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-500': form.errors.driver_id }"
              >
                <option value="">Select Driver</option>
                <option
                  v-for="driver in drivers"
                  :key="driver.id"
                  :value="driver.id"
                  :class="{ 'bg-blue-50 text-blue-700 font-medium': isPreferredDriver(driver) }"
                >
                  {{ driver.name }} - {{ driver.phone }}
                </option>
              </select>
              <p v-if="form.errors.driver_id" class="mt-1 text-sm text-red-600">{{ form.errors.driver_id }}</p>
            </div>

            <!-- Plant Selection -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Plant *</label>
              <select
                v-model="form.plant_id"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-500': form.errors.plant_id }"
              >
                <option value="">Select Plant</option>
                <option v-for="plant in plants" :key="plant.id" :value="plant.id">
                  {{ plant.name }} - {{ plant.location }}
                </option>
              </select>
              <p v-if="form.errors.plant_id" class="mt-1 text-sm text-red-600">{{ form.errors.plant_id }}</p>
            </div>

            <!-- Total Trips -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Total Trips *</label>
              <select
                v-model="form.total_trips"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-500': form.errors.total_trips }"
              >
                <option value="">Select Number of Trips</option>
                <option v-for="n in 7" :key="n" :value="n">{{ n }} Trip{{ n > 1 ? 's' : '' }}</option>
              </select>
              <p v-if="form.errors.total_trips" class="mt-1 text-sm text-red-600">{{ form.errors.total_trips }}</p>
            </div>

            <!-- Delivery Date -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Date *</label>
              <input
                v-model="form.delivery_date"
                type="date"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-500': form.errors.delivery_date }"
              />
              <p v-if="form.errors.delivery_date" class="mt-1 text-sm text-red-600">{{ form.errors.delivery_date }}</p>
            </div>

            <!-- Delivery Time -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Time</label>
              <input
                v-model="form.delivery_time"
                type="time"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
              />
            </div>

            <!-- Trip Amount Per Trip -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Trip Amount (Per Trip) *</label>
              <div class="relative">
                <span class="absolute left-3 top-2 text-gray-500">Rs</span>
                <input
                  v-model="form.trip_amount_per_trip"
                  type="number"
                  step="0.01"
                  min="0"
                  class="w-full pl-12 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  :class="{ 'border-red-500': form.errors.trip_amount_per_trip }"
                  placeholder="0.00"
                />
              </div>
              <p v-if="form.errors.trip_amount_per_trip" class="mt-1 text-sm text-red-600">{{ form.errors.trip_amount_per_trip }}</p>
            </div>

            <!-- Driver Salary Per Trip -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Driver Salary (Per Trip) *</label>
              <div class="relative">
                <span class="absolute left-3 top-2 text-gray-500">Rs</span>
                <input
                  v-model="form.driver_salary_per_trip"
                  type="number"
                  step="0.01"
                  min="0"
                  class="w-full pl-12 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  :class="{ 'border-red-500': form.errors.driver_salary_per_trip }"
                  placeholder="0.00"
                />
              </div>
              <p v-if="form.errors.driver_salary_per_trip" class="mt-1 text-sm text-red-600">{{ form.errors.driver_salary_per_trip }}</p>
            </div>
          </div>

          <!-- Advanced Payment Section -->
          <div v-if="form.driver_id && form.driver_salary_per_trip && form.total_trips" class="mt-8">
            <div class="bg-yellow-50 rounded-lg p-6 border border-yellow-200">
              <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-hand-holding-usd text-yellow-600 mr-2"></i>
                Advanced Payment Management
              </h3>

              <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-4">
                <!-- Give Advance -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Give Advance Payment
                  </label>
                  <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-500">Rs</span>
                    <input
                      v-model="form.advance_amount"
                      type="number"
                      step="0.01"
                      min="0"
                      :max="totalDriverSalary"
                      class="w-full pl-12 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-yellow-500"
                      placeholder="0.00"
                    />
                  </div>
                  <p class="text-xs text-gray-500 mt-1">Amount given in advance</p>
                </div>

                <!-- Deduct from Salary -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Deduct from Salary
                    <span v-if="driverBalance < 0" class="text-xs text-red-600">(Owes: {{ formatCurrency(Math.abs(driverBalance)) }})</span>
                  </label>
                  <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-500">Rs</span>
                    <input
                      v-model="form.deduction_amount"
                      type="number"
                      step="0.01"
                      min="0"
                      :max="Math.min(totalDriverSalary, Math.abs(driverBalance))"
                      class="w-full pl-12 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                      placeholder="0.00"
                    />
                  </div>
                  <div class="flex gap-2 mt-2">
                    <button
                      v-if="driverBalance < 0"
                      type="button"
                      @click="setDeductionFull"
                      class="text-xs px-2 py-1 bg-red-100 text-red-700 rounded hover:bg-red-200"
                    >
                      Deduct Full ({{ formatCurrency(Math.min(totalDriverSalary, Math.abs(driverBalance))) }})
                    </button>
                    <button
                      v-if="driverBalance < 0"
                      type="button"
                      @click="setDeductionHalf"
                      class="text-xs px-2 py-1 bg-orange-100 text-orange-700 rounded hover:bg-orange-200"
                    >
                      Deduct Half ({{ formatCurrency(Math.min(totalDriverSalary / 2, Math.abs(driverBalance))) }})
                    </button>
                  </div>
                </div>

                <!-- Payment Notes -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Payment Notes
                  </label>
                  <textarea
                    v-model="form.payment_notes"
                    rows="2"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Optional payment notes..."
                  ></textarea>
                </div>
              </div>

              <!-- Payment Calculation Summary -->
              <div class="bg-white rounded-lg p-4 border-2 border-yellow-300">
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4 text-sm">
                  <div>
                    <div class="text-gray-600">Total Salary</div>
                    <div class="font-bold text-green-600">{{ formatCurrency(totalDriverSalary) }}</div>
                  </div>
                  <div v-if="form.advance_amount > 0">
                    <div class="text-gray-600">+ Advance</div>
                    <div class="font-bold text-yellow-600">{{ formatCurrency(parseFloat(form.advance_amount || 0)) }}</div>
                  </div>
                  <div v-if="form.deduction_amount > 0">
                    <div class="text-gray-600">- Deduction</div>
                    <div class="font-bold text-red-600">{{ formatCurrency(parseFloat(form.deduction_amount || 0)) }}</div>
                  </div>
                  <div>
                    <div class="text-gray-600">= Actually Paid</div>
                    <div class="font-bold text-blue-600">{{ formatCurrency(actuallyPaidAmount) }}</div>
                  </div>
                  <div>
                    <div class="text-gray-600">New Balance</div>
                    <div class="font-bold" :class="newBalanceClass">{{ formatCurrency(Math.abs(newDriverBalance)) }}</div>
                    <div class="text-xs" :class="newBalanceTextClass">{{ newBalanceText }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Trip Calculations Summary -->
          <div v-if="form.trip_amount_per_trip && form.driver_salary_per_trip && form.total_trips" class="mt-8">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-200">
              <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-calculator text-blue-600 mr-2"></i>
                Trip Calculations Summary
              </h3>

              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Per Trip Calculations -->
                <div class="bg-white rounded-lg p-4 shadow-sm">
                  <h4 class="text-sm font-medium text-gray-600 mb-2">Per Trip</h4>
                  <div class="space-y-2">
                    <div class="flex justify-between">
                      <span class="text-sm text-gray-600">Amount:</span>
                      <span class="text-sm font-medium text-green-600">{{ formatCurrency(parseFloat(form.trip_amount_per_trip || 0)) }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-sm text-gray-600">Driver Salary:</span>
                      <span class="text-sm font-medium text-red-600">{{ formatCurrency(parseFloat(form.driver_salary_per_trip || 0)) }}</span>
                    </div>
                    <div class="flex justify-between border-t pt-2">
                      <span class="text-sm font-medium text-gray-900">Your Income:</span>
                      <span :class="incomePerTripClass" class="text-sm font-bold">
                        {{ formatCurrency(incomePerTrip) }}
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Total Calculations -->
                <div class="bg-white rounded-lg p-4 shadow-sm">
                  <h4 class="text-sm font-medium text-gray-600 mb-2">Total ({{ form.total_trips }} trips)</h4>
                  <div class="space-y-2">
                    <div class="flex justify-between">
                      <span class="text-sm text-gray-600">Total Amount:</span>
                      <span class="text-sm font-medium text-green-600">{{ formatCurrency(totalTripAmount) }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-sm text-gray-600">Total Salary:</span>
                      <span class="text-sm font-medium text-red-600">{{ formatCurrency(totalDriverSalary) }}</span>
                    </div>
                    <div class="flex justify-between border-t pt-2">
                      <span class="text-sm font-medium text-gray-900">Total Income:</span>
                      <span :class="totalIncomeClass" class="text-sm font-bold">
                        {{ formatCurrency(totalIncome) }}
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Profit Analysis -->
                <div class="bg-white rounded-lg p-4 shadow-sm">
                  <h4 class="text-sm font-medium text-gray-600 mb-2">Profit Analysis</h4>
                  <div class="space-y-2">
                    <div class="flex justify-between">
                      <span class="text-sm text-gray-600">Profit Margin:</span>
                      <span :class="profitMarginClass" class="text-sm font-medium">
                        {{ profitMargin.toFixed(1) }}%
                      </span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-sm text-gray-600">Status:</span>
                      <span :class="statusBadgeClass" class="px-2 py-1 text-xs font-semibold rounded-full">
                        {{ profitStatus }}
                      </span>
                    </div>
                    <div class="flex justify-between border-t pt-2">
                      <span class="text-sm text-gray-600">Avg per Trip:</span>
                      <span :class="avgIncomeClass" class="text-sm font-medium">
                        {{ formatCurrency(incomePerTrip) }}
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Quick Stats -->
                <div class="bg-white rounded-lg p-4 shadow-sm">
                  <h4 class="text-sm font-medium text-gray-600 mb-2">Quick Stats</h4>
                  <div class="space-y-2">
                    <div class="flex justify-between">
                      <span class="text-sm text-gray-600">Total Trips:</span>
                      <span class="text-sm font-medium text-blue-600">{{ form.total_trips }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-sm text-gray-600">Driver Share:</span>
                      <span class="text-sm font-medium text-orange-600">{{ driverSharePercentage.toFixed(1) }}%</span>
                    </div>
                    <div class="flex justify-between border-t pt-2">
                      <span class="text-sm text-gray-600">Your Share:</span>
                      <span class="text-sm font-medium text-green-600">{{ ownerSharePercentage.toFixed(1) }}%</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="mt-8 flex justify-end space-x-4">
            <Link :href="route('trips.index')" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
              <i class="fas fa-arrow-left mr-2"></i>
              Cancel
            </Link>
            <button
              type="submit"
              :disabled="form.processing || !canSubmit"
              class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
            >
              <i v-if="form.processing" class="fas fa-spinner fa-spin mr-2"></i>
              <i v-else class="fas fa-plus mr-2"></i>
              {{ form.processing ? 'Creating Trips...' : `Create ${form.total_trips || 0} Trip${(form.total_trips || 0) !== 1 ? 's' : ''}` }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { computed, watch, ref, onMounted } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  tippers: Array,
  drivers: Array,
  plants: Array
})

const driverBalance = ref(0)

const form = useForm({
  tipper_number: '',
  driver_id: '',
  plant_id: '',
  delivery_date: new Date().toISOString().split('T')[0],
  delivery_time: '',
  trip_amount_per_trip: '',
  driver_salary_per_trip: '',
  total_trips: '',
  advance_amount: '',
  deduction_amount: '',
  payment_notes: ''
})

// Calculations
const incomePerTrip = computed(() => {
  return parseFloat(form.trip_amount_per_trip || 0) - parseFloat(form.driver_salary_per_trip || 0)
})

const totalTripAmount = computed(() => {
  return parseFloat(form.trip_amount_per_trip || 0) * parseInt(form.total_trips || 0)
})

const totalDriverSalary = computed(() => {
  return parseFloat(form.driver_salary_per_trip || 0) * parseInt(form.total_trips || 0)
})

const totalIncome = computed(() => {
  return totalTripAmount.value - totalDriverSalary.value
})

const profitMargin = computed(() => {
  if (totalTripAmount.value === 0) return 0
  return (totalIncome.value / totalTripAmount.value) * 100
})

const driverSharePercentage = computed(() => {
  if (totalTripAmount.value === 0) return 0
  return (totalDriverSalary.value / totalTripAmount.value) * 100
})

const ownerSharePercentage = computed(() => {
  if (totalTripAmount.value === 0) return 0
  return (totalIncome.value / totalTripAmount.value) * 100
})

const profitStatus = computed(() => {
  if (totalIncome.value < 0) return 'Loss'
  if (profitMargin.value < 10) return 'Low Profit'
  if (profitMargin.value < 25) return 'Good Profit'
  return 'High Profit'
})

// Advanced payment calculations
const actuallyPaidAmount = computed(() => {
  const salary = totalDriverSalary.value
  const advance = parseFloat(form.advance_amount || 0)
  const deduction = parseFloat(form.deduction_amount || 0)
  return salary + advance - deduction
})

const newDriverBalance = computed(() => {
  const current = driverBalance.value
  const advance = parseFloat(form.advance_amount || 0)
  const deduction = parseFloat(form.deduction_amount || 0)
  return current - advance + deduction
})

const newBalanceText = computed(() => {
  return newDriverBalance.value < 0 ? 'Driver owes' : 'Driver balance'
})

const newBalanceClass = computed(() => {
  return newDriverBalance.value < 0 ? 'text-red-600' : 'text-green-600'
})

const newBalanceTextClass = computed(() => {
  return newDriverBalance.value < 0 ? 'text-red-500' : 'text-green-500'
})

const balanceAlertClass = computed(() => {
  return driverBalance.value < 0 
    ? 'bg-red-50 border border-red-200' 
    : 'bg-green-50 border border-green-200'
})

const balanceIconClass = computed(() => {
  return driverBalance.value < 0 
    ? 'fas fa-exclamation-triangle text-red-500' 
    : 'fas fa-check-circle text-green-500'
})

const balanceMessage = computed(() => {
  return driverBalance.value < 0 
    ? 'Driver has outstanding advance. Consider deducting from this trip.'
    : 'Driver has positive balance.'
})

// Driver helpers
const preferredDriver = computed(() => {
  if (!form.tipper_number) return null
  return props.drivers.find(driver => driver.tipper_number === form.tipper_number)
})

const selectedDriver = computed(() => {
  if (!form.driver_id) return null
  return props.drivers.find(driver => driver.id === form.driver_id)
})

const isPreferredDriver = (driver) => {
  return form.tipper_number && driver.tipper_number === form.tipper_number
}

// Styling classes
const incomePerTripClass = computed(() => incomePerTrip.value >= 0 ? 'text-green-600' : 'text-red-600')
const totalIncomeClass = computed(() => totalIncome.value >= 0 ? 'text-green-600' : 'text-red-600')
const profitMarginClass = computed(() => {
  if (profitMargin.value < 0) return 'text-red-600'
  if (profitMargin.value < 10) return 'text-yellow-600'
  return 'text-green-600'
})
const statusBadgeClass = computed(() => {
  if (totalIncome.value < 0) return 'bg-red-100 text-red-800'
  if (profitMargin.value < 10) return 'bg-yellow-100 text-yellow-800'
  if (profitMargin.value < 25) return 'bg-green-100 text-green-800'
  return 'bg-blue-100 text-blue-800'
})
const avgIncomeClass = computed(() => incomePerTrip.value >= 0 ? 'text-green-600' : 'text-red-600')

const canSubmit = computed(() => {
  return form.tipper_number &&
         form.driver_id &&
         form.plant_id &&
         form.delivery_date &&
         form.trip_amount_per_trip &&
         form.driver_salary_per_trip &&
         form.total_trips
})

// Methods
const formatCurrency = (value) => {
  return 'Rs ' + parseFloat(value).toFixed(2)
}

const onTipperChange = () => {
  if (preferredDriver.value) {
    form.driver_id = preferredDriver.value.id
    loadDriverBalance()
  } else {
    form.driver_id = ''
  }
}

const onDriverChange = () => {
  loadDriverBalance()
}

const loadDriverBalance = async () => {
  if (!form.driver_id) {
    driverBalance.value = 0
    return
  }
  
  try {
    const response = await fetch(`/api/drivers/${form.driver_id}/balance`)
    const data = await response.json()
    driverBalance.value = data.balance || 0
  } catch (error) {
    console.error('Failed to load driver balance:', error)
    driverBalance.value = 0
  }
}

const setDeductionFull = () => {
  const maxDeduction = Math.min(totalDriverSalary.value, Math.abs(driverBalance.value))
  form.deduction_amount = maxDeduction.toFixed(2)
}

const setDeductionHalf = () => {
  const maxDeduction = Math.min(totalDriverSalary.value / 2, Math.abs(driverBalance.value))
  form.deduction_amount = maxDeduction.toFixed(2)
}

const submitForm = () => {
  if (!canSubmit.value) return

  form.post(route('trips.store'), {
    onSuccess: () => {
      console.log('Trips created successfully')
    },
    onError: (errors) => {
      console.error('Form submission errors:', errors)
    }
  })
}

onMounted(() => {
  const firstField = document.querySelector('select')
  if (firstField) firstField.focus()
})
</script>

<style scoped>
/* Custom styles for better visual appeal */
.container {
  max-width: 1200px;
}

/* Animation for the calculation summary */
.bg-gradient-to-r {
  background: linear-gradient(135deg, #f0f9ff 0%, #e0e7ff 100%);
}

/* Hover effects for interactive elements */
select:hover, input:hover {
  border-color: #93c5fd;
  transition: border-color 0.2s ease;
}

/* Focus states */
select:focus, input:focus {
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
  border-color: #3b82f6;
}

/* Button hover effects */
button:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  transition: all 0.2s ease;
}

/* Card hover effects */
.bg-white:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transition: box-shadow 0.2s ease;
}

/* Loading spinner animation */
@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.fa-spin {
  animation: spin 1s linear infinite;
}

/* Form progress bar */
.bg-blue-600 {
  transition: width 0.3s ease;
}

</style>


