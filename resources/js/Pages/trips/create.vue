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
                  <span v-if="isPreferredDriver(driver)" class="text-xs">(Assigned to this tipper)</span>
                </option>
              </select>
              <p v-if="form.errors.driver_id" class="mt-1 text-sm text-red-600">{{ form.errors.driver_id }}</p>
              <p v-if="form.tipper_number && preferredDriver" class="mt-1 text-sm text-blue-600">
                <i class="fas fa-info-circle"></i> {{ preferredDriver.name }} is assigned to this tipper
              </p>
              <p v-if="form.driver_id && selectedDriver && !isPreferredDriver(selectedDriver)" class="mt-1 text-sm text-amber-600">
                <i class="fas fa-exclamation-triangle"></i> Selected driver is not assigned to this tipper
              </p>
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
                <option v-for="n in 7" :key="n" :value="n">
                  {{ n }} Trip{{ n > 1 ? 's' : '' }}
                </option>
              </select>
              <p v-if="form.errors.total_trips" class="mt-1 text-sm text-red-600">{{ form.errors.total_trips }}</p>
              <p v-if="form.total_trips" class="mt-1 text-sm text-green-600">
                <i class="fas fa-info-circle"></i> This will create {{ form.total_trips }} individual trip records
              </p>
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
                :class="{ 'border-red-500': form.errors.delivery_time }"
              />
              <p v-if="form.errors.delivery_time" class="mt-1 text-sm text-red-600">{{ form.errors.delivery_time }}</p>
            </div>

            <!-- Trip Amount Per Trip -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Trip Amount (Per Trip) *</label>
              <div class="relative">
                <span class="absolute left-3 top-2 text-gray-500">$</span>
                <input
                  v-model="form.trip_amount_per_trip"
                  type="number"
                  step="0.01"
                  min="0"
                  class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
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
                <span class="absolute left-3 top-2 text-gray-500">$</span>
                <input
                  v-model="form.driver_salary_per_trip"
                  type="number"
                  step="0.01"
                  min="0"
                  class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  :class="{ 'border-red-500': form.errors.driver_salary_per_trip }"
                  placeholder="0.00"
                />
              </div>
              <p v-if="form.errors.driver_salary_per_trip" class="mt-1 text-sm text-red-600">{{ form.errors.driver_salary_per_trip }}</p>
            </div>
          </div>

          <!-- Calculations Summary -->
          <div v-if="form.trip_amount_per_trip && form.driver_salary_per_trip && form.total_trips" class="mt-8">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-200">
              <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <i class="fas fa-calculator text-blue-600 mr-2"></i>
                Trip Calculations Summary
              </h3>

              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Per Trip Calculations -->
                <div class="bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                  <h4 class="text-sm font-medium text-gray-600 mb-2">Per Trip</h4>
                  <div class="space-y-2">
                    <div class="flex justify-between">
                      <span class="text-sm text-gray-600">Amount:</span>
                      <span class="text-sm font-medium text-green-600">${{ parseFloat(form.trip_amount_per_trip || 0).toFixed(2) }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-sm text-gray-600">Driver Salary:</span>
                      <span class="text-sm font-medium text-red-600">${{ parseFloat(form.driver_salary_per_trip || 0).toFixed(2) }}</span>
                    </div>
                    <div class="flex justify-between border-t pt-2">
                      <span class="text-sm font-medium text-gray-900">Your Income:</span>
                      <span :class="incomePerTripClass" class="text-sm font-bold">
                        ${{ incomePerTrip.toFixed(2) }}
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Total Calculations -->
                <div class="bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
                  <h4 class="text-sm font-medium text-gray-600 mb-2">Total ({{ form.total_trips }} trips)</h4>
                  <div class="space-y-2">
                    <div class="flex justify-between">
                      <span class="text-sm text-gray-600">Total Amount:</span>
                      <span class="text-sm font-medium text-green-600">${{ totalTripAmount.toFixed(2) }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-sm text-gray-600">Total Salary:</span>
                      <span class="text-sm font-medium text-red-600">${{ totalDriverSalary.toFixed(2) }}</span>
                    </div>
                    <div class="flex justify-between border-t pt-2">
                      <span class="text-sm font-medium text-gray-900">Total Income:</span>
                      <span :class="totalIncomeClass" class="text-sm font-bold">
                        ${{ totalIncome.toFixed(2) }}
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Profit Analysis -->
                <div class="bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
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
                        ${{ incomePerTrip.toFixed(2) }}
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Quick Stats -->
                <div class="bg-white rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow">
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

              <!-- Warning/Success Messages -->
              <div class="mt-4">
                <div v-if="totalIncome < 0" class="flex items-center p-3 bg-red-100 border border-red-300 rounded-md">
                  <i class="fas fa-exclamation-triangle text-red-600 mr-2"></i>
                  <span class="text-sm text-red-800">
                    <strong>Warning:</strong> You will lose ${{ Math.abs(totalIncome).toFixed(2) }} on this batch of trips.
                  </span>
                </div>
                <div v-else-if="profitMargin < 10" class="flex items-center p-3 bg-yellow-100 border border-yellow-300 rounded-md">
                  <i class="fas fa-exclamation-circle text-yellow-600 mr-2"></i>
                  <span class="text-sm text-yellow-800">
                    <strong>Low Profit:</strong> Profit margin is only {{ profitMargin.toFixed(1) }}%. Consider reviewing pricing.
                  </span>
                </div>
                <div v-else-if="profitMargin < 25" class="flex items-center p-3 bg-blue-100 border border-blue-300 rounded-md">
                  <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                  <span class="text-sm text-blue-800">
                    <strong>Moderate Profit:</strong> You'll earn ${{ totalIncome.toFixed(2) }} with {{ profitMargin.toFixed(1) }}% profit margin.
                  </span>
                </div>
                <div v-else class="flex items-center p-3 bg-green-100 border border-green-300 rounded-md">
                  <i class="fas fa-check-circle text-green-600 mr-2"></i>
                  <span class="text-sm text-green-800">
                    <strong>Excellent Profit:</strong> You'll earn ${{ totalIncome.toFixed(2) }} with {{ profitMargin.toFixed(1) }}% profit margin.
                  </span>
                </div>
              </div>

              <!-- Trip Breakdown Preview -->
              <div class="mt-6 pt-4 border-t border-blue-200">
                <h4 class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                  <i class="fas fa-list text-blue-600 mr-2"></i>
                  Trip Breakdown Preview
                </h4>
                <div class="bg-white rounded-md p-3 text-xs text-gray-600">
                  <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                    <div>
                      <div class="font-medium text-gray-800">{{ form.total_trips }} Trips</div>
                      <div class="text-gray-500">Will be created</div>
                    </div>
                    <div>
                      <div class="font-medium text-green-600">${{ parseFloat(form.trip_amount_per_trip || 0).toFixed(2) }} each</div>
                      <div class="text-gray-500">Trip amount</div>
                    </div>
                    <div>
                      <div class="font-medium text-red-600">${{ parseFloat(form.driver_salary_per_trip || 0).toFixed(2) }} each</div>
                      <div class="text-gray-500">Driver salary</div>
                    </div>
                    <div>
                      <div class="font-medium" :class="incomePerTripClass">${{ incomePerTrip.toFixed(2) }} each</div>
                      <div class="text-gray-500">Your income</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="mt-8 flex justify-end space-x-4">
            <Link :href="route('trips.index')" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors duration-200 flex items-center">
              <i class="fas fa-arrow-left mr-2"></i>
              Cancel
            </Link>
            <button
              type="submit"
              :disabled="form.processing || !canSubmit"
              class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 flex items-center shadow-sm hover:shadow-md"
              :class="{ 'transform hover:scale-105': !form.processing && canSubmit }"
            >
              <i v-if="form.processing" class="fas fa-spinner fa-spin mr-2"></i>
              <i v-else class="fas fa-plus mr-2"></i>
              {{ form.processing ? 'Creating Trips...' : `Create ${form.total_trips || 0} Trip${(form.total_trips || 0) !== 1 ? 's' : ''}` }}
            </button>
          </div>

          <!-- Form Progress Indicator -->
          <div v-if="showProgressIndicator" class="mt-4">
            <div class="bg-gray-200 rounded-full h-2">
              <div
                class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                :style="{ width: formCompletionPercentage + '%' }"
              ></div>
            </div>
            <p class="text-sm text-gray-600 mt-1 text-center">
              Form {{ formCompletionPercentage.toFixed(0) }}% complete
            </p>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { computed, watch, ref, onMounted } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  tippers: {
    type: Array,
    default: () => []
  },
  drivers: {
    type: Array,
    default: () => []
  },
  plants: {
    type: Array,
    default: () => []
  }
})

const showProgressIndicator = ref(false)

const form = useForm({
  tipper_number: '',
  driver_id: '',
  plant_id: '',
  delivery_date: new Date().toISOString().split('T')[0],
  delivery_time: '',
  trip_amount_per_trip: '',
  driver_salary_per_trip: '',
  total_trips: ''
})

// Computed properties for calculations
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

// Find the preferred driver for the selected tipper
const preferredDriver = computed(() => {
  if (!form.tipper_number) return null
  return props.drivers.find(driver => driver.tipper_number === form.tipper_number)
})

// Get the currently selected driver
const selectedDriver = computed(() => {
  if (!form.driver_id) return null
  return props.drivers.find(driver => driver.id === form.driver_id)
})

// Check if a driver is the preferred one for the selected tipper
const isPreferredDriver = (driver) => {
  return form.tipper_number && driver.tipper_number === form.tipper_number
}

// Styling classes based on profit/loss
const incomePerTripClass = computed(() => {
  return incomePerTrip.value >= 0 ? 'text-green-600' : 'text-red-600'
})

const totalIncomeClass = computed(() => {
  return totalIncome.value >= 0 ? 'text-green-600' : 'text-red-600'
})

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

const avgIncomeClass = computed(() => {
  return incomePerTrip.value >= 0 ? 'text-green-600' : 'text-red-600'
})

// Form validation and progress
const canSubmit = computed(() => {
  return form.tipper_number &&
         form.driver_id &&
         form.plant_id &&
         form.delivery_date &&
         form.trip_amount_per_trip &&
         form.driver_salary_per_trip &&
         form.total_trips &&
         parseFloat(form.trip_amount_per_trip) >= 0 &&
         parseFloat(form.driver_salary_per_trip) >= 0 &&
         parseInt(form.total_trips) > 0
})

const formCompletionPercentage = computed(() => {
  const fields = [
    form.tipper_number,
    form.driver_id,
    form.plant_id,
    form.delivery_date,
    form.trip_amount_per_trip,
    form.driver_salary_per_trip,
    form.total_trips
  ]

  const completedFields = fields.filter(field => field && field !== '').length
  return (completedFields / fields.length) * 100
})

// Methods
const onTipperChange = () => {
  // Auto-assign the preferred driver if available
  if (preferredDriver.value) {
    form.driver_id = preferredDriver.value.id
  } else {
    form.driver_id = ''
  }
}

const submitForm = () => {
  if (!canSubmit.value) return

  // Show confirmation for large batches
  if (parseInt(form.total_trips) > 10) {
    if (!confirm(`Are you sure you want to create ${form.total_trips} trips? This will generate ${form.total_trips} individual trip records.`)) {
      return
    }
  }

  form.post(route('trips.store'), {
    onSuccess: () => {
      // Form will be redirected by the controller
      console.log('Trips created successfully')
    },
    onError: (errors) => {
      console.error('Form submission errors:', errors)

      // Show error message
      if (errors.error) {
        alert('Error: ' + errors.error)
      }
    },
    onStart: () => {
      showProgressIndicator.value = true
    },
    onFinish: () => {
      showProgressIndicator.value = false
    }
  })
}

// Watchers for user feedback
watch([() => form.trip_amount_per_trip, () => form.driver_salary_per_trip], () => {
  // Could add real-time validation or suggestions here
}, { immediate: false })

// Auto-save draft functionality (optional)
const saveDraft = () => {
  const draft = {
    tipper_number: form.tipper_number,
    driver_id: form.driver_id,
    plant_id: form.plant_id,
    delivery_date: form.delivery_date,
    delivery_time: form.delivery_time,
    trip_amount_per_trip: form.trip_amount_per_trip,
    driver_salary_per_trip: form.driver_salary_per_trip,
    total_trips: form.total_trips,
    timestamp: new Date().toISOString()
  }

  // In a real app, you might save to localStorage or send to server
  // localStorage.setItem('trip_draft', JSON.stringify(draft))
}

const loadDraft = () => {
  // In a real app, you might load from localStorage
  // const draft = localStorage.getItem('trip_draft')
  // if (draft) {
  //   const parsedDraft = JSON.parse(draft)
  //   Object.keys(parsedDraft).forEach(key => {
  //     if (key !== 'timestamp' && form.hasOwnProperty(key)) {
  //       form[key] = parsedDraft[key]
  //     }
  //   })
  // }
}

// Lifecycle hooks
onMounted(() => {
  // Load any saved draft
  loadDraft()

  // Set focus to first field
  const firstField = document.querySelector('select')
  if (firstField) {
    firstField.focus()
  }
})

// Auto-save draft when form changes (debounced)
let saveTimeout = null
watch(form, () => {
  if (saveTimeout) clearTimeout(saveTimeout)
  saveTimeout = setTimeout(saveDraft, 1000) // Save after 1 second of inactivity
}, { deep: true })
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


