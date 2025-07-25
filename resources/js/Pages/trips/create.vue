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
              :disabled="!availableDrivers.length"
            >
              <option value="">Select Driver</option>
              <option v-for="driver in availableDrivers" :key="driver.id" :value="driver.id">
                {{ driver.name }} - {{ driver.phone }}
              </option>
            </select>
            <p v-if="form.errors.driver_id" class="mt-1 text-sm text-red-600">{{ form.errors.driver_id }}</p>
            <p v-if="form.tipper_number && !availableDrivers.length" class="mt-1 text-sm text-yellow-600">
              No drivers available for this tipper
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

          <!-- Paid Amount -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Paid Amount *</label>
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

        <!-- Balance Information -->
        <div v-if="form.trip_amount && form.paid_amount" class="mt-6 p-4 bg-gray-50 rounded-md">
          <div class="flex justify-between items-center">
            <span class="text-sm font-medium text-gray-700">Balance Amount:</span>
            <span :class="balanceClass" class="text-lg font-bold">
              ${{ (form.trip_amount - form.paid_amount).toFixed(2) }}
            </span>
          </div>
          <div class="mt-2">
            <span :class="statusClass" class="px-2 py-1 text-xs font-semibold rounded-full">
              {{ balanceStatus }}
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
import { computed } from 'vue'
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

const availableDrivers = computed(() => {
  if (!form.tipper_number) return []
  return props.drivers.filter(driver => driver.tipper_number === form.tipper_number)
})

const balanceClass = computed(() => {
  const balance = form.trip_amount - form.paid_amount
  return balance > 0 ? 'text-red-600' : 'text-green-600'
})

const balanceStatus = computed(() => {
  const balance = form.trip_amount - form.paid_amount
  return balance > 0 ? 'Pending Payment' : 'Fully Paid'
})

const statusClass = computed(() => {
  const balance = form.trip_amount - form.paid_amount
  return balance > 0
    ? 'bg-red-100 text-red-800'
    : 'bg-green-100 text-green-800'
})

const onTipperChange = () => {
  form.driver_id = ''
}

const submitForm = () => {
  form.post(route('trips.store'))
}
</script>
