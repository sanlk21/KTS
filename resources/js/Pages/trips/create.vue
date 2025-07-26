<template>
  <AuthenticatedLayout>
    <div class="container mx-auto px-4 py-8">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Create New Trip</h1>
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
              <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Time *</label>
              <input
                v-model="form.delivery_time"
                type="time"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-500': form.errors.delivery_time }"
              />
              <p v-if="form.errors.delivery_time" class="mt-1 text-sm text-red-600">{{ form.errors.delivery_time }}</p>
            </div>

            <!-- Trip Amount -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Trip Amount *</label>
              <input
                v-model="form.trip_amount"
                type="number"
                step="0.01"
                min="0"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-500': form.errors.trip_amount }"
              />
              <p v-if="form.errors.trip_amount" class="mt-1 text-sm text-red-600">{{ form.errors.trip_amount }}</p>
            </div>

            <!-- Driver Salary -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Driver Salary *</label>
              <input
                v-model="form.paid_amount"
                type="number"
                step="0.01"
                min="0"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                :class="{ 'border-red-500': form.errors.paid_amount }"
              />
              <p v-if="form.errors.paid_amount" class="mt-1 text-sm text-red-600">{{ form.errors.paid_amount }}</p>
            </div>
          </div>

          <!-- Income Information -->
          <div v-if="form.trip_amount && form.paid_amount" class="mt-6 p-4 bg-gray-50 rounded-md">
            <div class="flex justify-between items-center">
              <span class="text-sm font-medium text-gray-700">Your Income:</span>
              <span :class="incomeClass" class="text-lg font-bold">
                ${{ (form.trip_amount - form.paid_amount).toFixed(2) }}
              </span>
            </div>
            <div class="mt-2">
              <span :class="statusClass" class="px-2 py-1 text-xs font-semibold rounded-full">
                {{ incomeStatus }}
              </span>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="mt-8 flex justify-end space-x-4">
            <Link :href="route('trips.index')" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
              Cancel
            </Link>
            <button
              type="submit"
              :disabled="form.processing"
              class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 disabled:opacity-50"
            >
              {{ form.processing ? 'Creating...' : 'Create Trip' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { computed, watch } from 'vue'
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

const form = useForm({
  tipper_number: '',
  driver_id: '',
  plant_id: '',
  delivery_date: new Date().toISOString().split('T')[0],
  delivery_time: '',
  trip_amount: '',
  paid_amount: ''
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

const incomeClass = computed(() => {
  const income = form.trip_amount - form.paid_amount
  return income > 0 ? 'text-green-600' : 'text-red-600'
})

const incomeStatus = computed(() => {
  const income = form.trip_amount - form.paid_amount
  return income > 0 ? 'Profitable' : 'Loss'
})

const statusClass = computed(() => {
  const income = form.trip_amount - form.paid_amount
  return income > 0
    ? 'bg-green-100 text-green-800'
    : 'bg-red-100 text-red-800'
})

const onTipperChange = () => {
  // Auto-assign the preferred driver if available
  if (preferredDriver.value) {
    form.driver_id = preferredDriver.value.id
  } else {
    form.driver_id = ''
  }
}

const submitForm = () => {
  form.post(route('trips.store'))
}
</script>
