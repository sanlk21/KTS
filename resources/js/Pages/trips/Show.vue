<template>
  <div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Trip Details #{{ trip.id }}</h1>
      <div class="flex space-x-4">
        <Link :href="route('trips.edit', trip.id)" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
          Edit Trip
        </Link>
        <button @click="deleteTrip" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
          Delete Trip
        </button>
        <Link :href="route('trips.index')" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
          Back to Trips
        </Link>
      </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Trip Information -->
      <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">Trip Information</h2>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-500">Trip ID</label>
                <p class="text-lg font-semibold text-gray-900">{{ trip.id }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-500">Tipper Number</label>
                <p class="text-lg text-gray-900">{{ trip.tipper_number }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-500">Driver Name</label>
                <p class="text-lg text-gray-900">{{ trip.driver_name }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-500">Plant Name</label>
                <p class="text-lg text-gray-900">{{ trip.plant_name }}</p>
              </div>
            </div>

            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-500">Delivery Date</label>
                <p class="text-lg text-gray-900">{{ formatDate(trip.delivery_date) }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-500">Delivery Time</label>
                <p class="text-lg text-gray-900">{{ formatTime(trip.delivery_time) }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-500">Created At</label>
                <p class="text-lg text-gray-900">{{ formatDateTime(trip.created_at) }}</p>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-500">Last Updated</label>
                <p class="text-lg text-gray-900">{{ formatDateTime(trip.updated_at) }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Related Information -->
        <div class="mt-6 bg-white rounded-lg shadow-md p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">Related Information</h2>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Tipper Details -->
            <div v-if="trip.tipper" class="border rounded-lg p-4">
              <h3 class="font-semibold text-gray-900 mb-2">Tipper Details</h3>
              <div class="space-y-2 text-sm">
                <p><span class="font-medium">Number:</span> {{ trip.tipper.tipper_number }}</p>
                <p><span class="font-medium">Size:</span> {{ trip.tipper.size }} Ton</p>
                <p><span class="font-medium">Capacity:</span> {{ trip.tipper.capacity }} m³</p>
                <p><span class="font-medium">Status:</span>
                  <span :class="trip.tipper.status === 'active' ? 'text-green-600' : 'text-red-600'">
                    {{ trip.tipper.status }}
                  </span>
                </p>
              </div>
            </div>

            <!-- Driver Details -->
            <div v-if="trip.driver" class="border rounded-lg p-4">
              <h3 class="font-semibold text-gray-900 mb-2">Driver Details</h3>
              <div class="space-y-2 text-sm">
                <p><span class="font-medium">Name:</span> {{ trip.driver.name }}</p>
                <p><span class="font-medium">Phone:</span> {{ trip.driver.phone }}</p>
                <p><span class="font-medium">License:</span> {{ trip.driver.license_number }}</p>
                <p><span class="font-medium">Status:</span>
                  <span :class="trip.driver.status === 'active' ? 'text-green-600' : 'text-red-600'">
                    {{ trip.driver.status }}
                  </span>
                </p>
              </div>
            </div>

            <!-- Plant Details -->
            <div v-if="trip.plant" class="border rounded-lg p-4">
              <h3 class="font-semibold text-gray-900 mb-2">Plant Details</h3>
              <div class="space-y-2 text-sm">
                <p><span class="font-medium">Name:</span> {{ trip.plant.name }}</p>
                <p><span class="font-medium">Location:</span> {{ trip.plant.location }}</p>
                <p><span class="font-medium">Contact:</span> {{ trip.plant.contact_person }}</p>
                <p><span class="font-medium">Phone:</span> {{ trip.plant.phone }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Financial Summary -->
      <div class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">Financial Summary</h2>

          <div class="space-y-4">
            <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
              <span class="text-sm font-medium text-gray-600">Trip Amount</span>
              <span class="text-lg font-semibold text-blue-600">${{ trip.trip_amount }}</span>
            </div>

            <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
              <span class="text-sm font-medium text-gray-600">Paid Amount</span>
              <span class="text-lg font-semibold text-green-600">${{ trip.paid_amount }}</span>
            </div>

            <div class="flex justify-between items-center p-3 rounded-lg" :class="balanceClass">
              <span class="text-sm font-medium">Balance Amount</span>
              <span class="text-lg font-semibold">
                ${{ (trip.trip_amount - trip.paid_amount).toFixed(2) }}
              </span>
            </div>

            <div class="pt-3 border-t">
              <div class="flex justify-center">
                <span :class="statusClass" class="px-3 py-1 text-sm font-semibold rounded-full">
                  {{ balanceStatus }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- Status Information -->
        <div class="mt-6 bg-white rounded-lg shadow-md p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">Status Information</h2>

          <div class="space-y-3">
            <div class="flex justify-between items-center">
              <span class="text-sm font-medium text-gray-600">Payment Status</span>
              <span :class="statusClass" class="px-2 py-1 text-xs font-semibold rounded-full">
                {{ balanceStatus }}
              </span>
            </div>

            <div class="flex justify-between items-center">
              <span class="text-sm font-medium text-gray-600">Completion</span>
              <span class="text-sm text-gray-900">{{ completionPercentage }}%</span>
            </div>

            <div class="w-full bg-gray-200 rounded-full h-2">
              <div
                class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                :style="{ width: completionPercentage + '%' }"
              ></div>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-6 bg-white rounded-lg shadow-md p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">Quick Actions</h2>

          <div class="space-y-3">
            <Link :href="route('trips.edit', trip.id)" class="block w-full text-center bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
              Edit Trip
            </Link>

            <button @click="printTrip" class="block w-full text-center bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
              Print Trip
            </button>

            <button @click="duplicateTrip" class="block w-full text-center bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
              Duplicate Trip
            </button>

            <button @click="deleteTrip" class="block w-full text-center bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
              Delete Trip
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Link, Head } from '@inertiajs/vue3'
import { computed } from 'vue'

export default {
  components: {
    Link,
    Head
  },
  props: {
    trip: {
      type: Object,
      required: true
    }
  },
  setup(props) {
    const balance = computed(() => props.trip.trip_amount - props.trip.paid_amount)

    const balanceClass = computed(() => {
      return balance.value > 0 ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600'
    })

    const balanceStatus = computed(() => {
      return balance.value > 0 ? 'Pending Payment' : 'Fully Paid'
    })

    const statusClass = computed(() => {
      return balance.value > 0
        ? 'bg-red-100 text-red-800'
        : 'bg-green-100 text-green-800'
    })

    const completionPercentage = computed(() => {
      if (props.trip.trip_amount === 0) return 0
      return Math.round((props.trip.paid_amount / props.trip.trip_amount) * 100)
    })

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      })
    }

    const formatTime = (time) => {
      if (!time) return 'Not specified'
      return new Date(`1970-01-01T${time}`).toLocaleTimeString([], {
        hour: '2-digit',
        minute: '2-digit'
      })
    }

    const formatDateTime = (dateTime) => {
      return new Date(dateTime).toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    }

    const deleteTrip = () => {
      if (confirm('Are you sure you want to delete this trip? This action cannot be undone.')) {
        window.$inertia.delete(route('trips.destroy', props.trip.id))
      }
    }

    const printTrip = () => {
      window.print()
    }

    const duplicateTrip = () => {
      if (confirm('Do you want to create a new trip with the same details?')) {
        window.$inertia.get(route('trips.create'), {
          duplicate: props.trip.id
        })
      }
    }

    return {
      balance,
      balanceClass,
      balanceStatus,
      statusClass,
      completionPercentage,
      formatDate,
      formatTime,
      formatDateTime,
      deleteTrip,
      printTrip,
      duplicateTrip
    }
  }
}
</script>

<style scoped>
@media print {
  .no-print {
    display: none !important;
  }
}
</style>
