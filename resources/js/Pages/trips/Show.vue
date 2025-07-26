<template>
  <AuthenticatedLayout>
    <div class="container mx-auto px-4 py-8">
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Trip Details</h1>
        <div class="flex space-x-2">
          <Link :href="route('trips.edit', trip.id)" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            Edit Trip
          </Link>
          <button
            @click="deleteTrip"
            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
            :disabled="deleteForm.processing"
          >
            {{ deleteForm.processing ? 'Deleting...' : 'Delete Trip' }}
          </button>
          <Link :href="route('trips.index')" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
            Back to Trips
          </Link>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Trip Information -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-start mb-6">
              <div>
                <h2 class="text-2xl font-bold text-gray-900">Trip #{{ trip.id }}</h2>
                <p class="text-gray-600 mt-1">{{ formatDate(trip.delivery_date) }}</p>
              </div>
              <div class="text-right">
                <span :class="statusBadgeClass" class="px-3 py-1 text-sm font-semibold rounded-full">
                  {{ tripStatus }}
                </span>
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Tipper Information -->
              <div class="border-l-4 border-blue-500 pl-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Tipper Information</h3>
                <div class="space-y-2">
                  <div class="flex justify-between">
                    <span class="text-gray-600">Tipper Number:</span>
                    <span class="font-medium text-gray-900">{{ trip.tipper_number }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Size:</span>
                    <span class="font-medium text-gray-900">{{ trip.tipper?.size }} Ton</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Type:</span>
                    <span class="font-medium text-gray-900">{{ trip.tipper?.type || 'N/A' }}</span>
                  </div>
                </div>
              </div>

              <!-- Driver Information -->
              <div class="border-l-4 border-green-500 pl-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Driver Information</h3>
                <div class="space-y-2">
                  <div class="flex justify-between">
                    <span class="text-gray-600">Name:</span>
                    <span class="font-medium text-gray-900">{{ trip.driver?.name || trip.driver_name }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Phone:</span>
                    <span class="font-medium text-gray-900">{{ trip.driver?.phone || 'N/A' }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">License:</span>
                    <span class="font-medium text-gray-900">{{ trip.driver?.license_number || 'N/A' }}</span>
                  </div>
                  <div v-if="!isDriverAssignedToTipper" class="mt-2">
                    <span class="px-2 py-1 bg-amber-100 text-amber-800 text-xs rounded-full">
                      Not assigned to this tipper
                    </span>
                  </div>
                </div>
              </div>

              <!-- Plant Information -->
              <div class="border-l-4 border-purple-500 pl-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Plant Information</h3>
                <div class="space-y-2">
                  <div class="flex justify-between">
                    <span class="text-gray-600">Name:</span>
                    <span class="font-medium text-gray-900">{{ trip.plant?.name || trip.plant_name }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Location:</span>
                    <span class="font-medium text-gray-900">{{ trip.plant?.location || 'N/A' }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Contact:</span>
                    <span class="font-medium text-gray-900">{{ trip.plant?.contact_number || 'N/A' }}</span>
                  </div>
                </div>
              </div>

              <!-- Delivery Information -->
              <div class="border-l-4 border-orange-500 pl-4">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Delivery Information</h3>
                <div class="space-y-2">
                  <div class="flex justify-between">
                    <span class="text-gray-600">Date:</span>
                    <span class="font-medium text-gray-900">{{ formatDate(trip.delivery_date) }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Time:</span>
                    <span class="font-medium text-gray-900">{{ trip.delivery_time || 'Not specified' }}</span>
                  </div>
                  <div class="flex justify-between">
                    <span class="text-gray-600">Status:</span>
                    <span class="font-medium text-gray-900">{{ deliveryStatus }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Financial Summary & Income Analysis -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Financial Summary</h3>
            <div class="space-y-4">
              <div class="flex justify-between items-center p-3 bg-blue-50 rounded">
                <span class="text-gray-600">Trip Amount (from Plant):</span>
                <span class="text-lg font-bold text-blue-600">${{ parseFloat(trip.trip_amount).toFixed(2) }}</span>
              </div>
              <div class="flex justify-between items-center p-3 bg-orange-50 rounded">
                <span class="text-gray-600">Driver Salary:</span>
                <span class="text-lg font-bold text-orange-600">${{ parseFloat(trip.paid_amount).toFixed(2) }}</span>
              </div>
              <div class="flex justify-between items-center p-3 bg-gray-100 rounded border-t-2">
                <span class="text-gray-700 font-medium">Your Income:</span>
                <span :class="incomeClass" class="text-xl font-bold">
                  ${{ incomeAmount.toFixed(2) }}
                </span>
              </div>
              <div class="mt-4">
                <span :class="incomeBadgeClass" class="px-3 py-1 text-sm font-semibold rounded-full block text-center">
                  {{ incomeStatus }}
                </span>
              </div>
            </div>
          </div>

          <!-- Trip Timeline -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Trip Timeline</h3>
            <div class="space-y-4">
              <div class="flex items-start space-x-3">
                <div class="w-3 h-3 bg-green-500 rounded-full mt-1"></div>
                <div>
                  <p class="text-sm font-medium text-gray-900">Trip Created</p>
                  <p class="text-xs text-gray-500">{{ formatDateTime(trip.created_at) }}</p>
                </div>
              </div>
              <div v-if="trip.updated_at !== trip.created_at" class="flex items-start space-x-3">
                <div class="w-3 h-3 bg-blue-500 rounded-full mt-1"></div>
                <div>
                  <p class="text-sm font-medium text-gray-900">Last Updated</p>
                  <p class="text-xs text-gray-500">{{ formatDateTime(trip.updated_at) }}</p>
                </div>
              </div>
              <div class="flex items-start space-x-3">
                <div :class="deliveryStatusDotClass" class="w-3 h-3 rounded-full mt-1"></div>
                <div>
                  <p class="text-sm font-medium text-gray-900">Delivery Date</p>
                  <p class="text-xs text-gray-500">{{ formatDate(trip.delivery_date) }} {{ trip.delivery_time || '' }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Delete Confirmation Modal -->
      <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
          <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
              <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
              </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-2">Delete Trip</h3>
            <div class="mt-2 px-7 py-3">
              <p class="text-sm text-gray-500">
                Are you sure you want to delete this trip? This action cannot be undone.
              </p>
            </div>
            <div class="items-center px-4 py-3">
              <button
                @click="confirmDelete"
                :disabled="deleteForm.processing"
                class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-red-600 disabled:opacity-50"
              >
                {{ deleteForm.processing ? 'Deleting...' : 'Delete' }}
              </button>
              <button
                @click="showDeleteModal = false"
                class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-24 hover:bg-gray-600"
              >
                Cancel
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
  trip: {
    type: Object,
    required: true
  }
})

const showDeleteModal = ref(false)

const deleteForm = useForm({})

const incomeAmount = computed(() => {
  return parseFloat(props.trip.trip_amount) - parseFloat(props.trip.paid_amount)
})

const incomeClass = computed(() => {
  return incomeAmount.value > 0 ? 'text-green-600' : 'text-red-600'
})

const incomeStatus = computed(() => {
  return incomeAmount.value > 0 ? 'Profitable Trip' : 'Loss-Making Trip'
})

const incomeBadgeClass = computed(() => {
  return incomeAmount.value > 0
    ? 'bg-green-100 text-green-800'
    : 'bg-red-100 text-red-800'
})

const deliveryStatus = computed(() => {
  const deliveryDate = new Date(props.trip.delivery_date)
  const today = new Date()
  today.setHours(0, 0, 0, 0)
  deliveryDate.setHours(0, 0, 0, 0)

  if (deliveryDate < today) {
    return 'Completed'
  } else if (deliveryDate.getTime() === today.getTime()) {
    return 'Today'
  } else {
    return 'Scheduled'
  }
})

const deliveryStatusDotClass = computed(() => {
  const status = deliveryStatus.value
  switch (status) {
    case 'Completed':
      return 'bg-green-500'
    case 'Today':
      return 'bg-orange-500'
    case 'Scheduled':
      return 'bg-blue-500'
    default:
      return 'bg-gray-500'
  }
})

const isDriverAssignedToTipper = computed(() => {
  return props.trip.driver?.tipper_number === props.trip.tipper_number
})

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const formatDateTime = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const deleteTrip = () => {
  showDeleteModal.value = true
}

const confirmDelete = () => {
  deleteForm.delete(route('trips.destroy', props.trip.id), {
    onSuccess: () => {
      showDeleteModal.value = false
    }
  })
}
</script>
