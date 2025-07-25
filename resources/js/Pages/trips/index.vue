<template>
  <div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Trips Management</h1>
      <div class="flex space-x-4">
        <Link :href="route('trips.create')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
          Add New Trip
        </Link>
        <Link :href="route('trips.reports')" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
          View Reports
        </Link>
      </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
          <input
            v-model="searchTerm"
            type="text"
            placeholder="Search by tipper, driver, or plant..."
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
          <input
            v-model="dateFrom"
            type="date"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
          <input
            v-model="dateTo"
            type="date"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
          <select
            v-model="statusFilter"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="completed">Completed</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Trips Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipper</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Driver</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plant</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delivery Date</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trip Amount</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Paid Amount</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="trip in filteredTrips" :key="trip.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ trip.id }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ trip.tipper_number }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ getDriverName(trip) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ getPlantName(trip) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ formatDate(trip.delivery_date) }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ trip.trip_amount }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ trip.paid_amount }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${{ getBalance(trip) }}</td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="getStatusClass(trip)" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                {{ getStatus(trip) }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
              <div class="flex space-x-2">
                <Link :href="route('trips.show', { id: trip.id })" class="text-blue-600 hover:text-blue-900">View</Link>
                <Link :href="route('trips.edit', { id: trip.id })" class="text-indigo-600 hover:text-indigo-900">Edit</Link>
                <button @click="deleteTrip(trip.id)" class="text-red-600 hover:text-red-900">Delete</button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>

      <!-- No data message -->
      <div v-if="filteredTrips.length === 0" class="text-center py-12">
        <p class="text-gray-500 text-lg">No trips found.</p>
      </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6 flex justify-between items-center" v-if="trips.data">
      <div class="text-sm text-gray-700">
        Showing {{ trips.from || 0 }} to {{ trips.to || 0 }} of {{ trips.total || 0 }} trips
      </div>

      <!-- Pagination links if using Laravel pagination -->
      <div v-if="trips.links" class="flex space-x-2">
        <template v-for="link in trips.links" :key="link.label">
          <Link
            v-if="link.url"
            :href="link.url"
            v-html="link.label"
            :class="[
              'px-3 py-2 text-sm border rounded',
              link.active ? 'bg-blue-500 text-white border-blue-500' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50'
            ]"
          />
          <span
            v-else
            v-html="link.label"
            :class="[
              'px-3 py-2 text-sm border rounded cursor-not-allowed',
              link.active ? 'bg-blue-500 text-white border-blue-500' : 'bg-gray-100 text-gray-400 border-gray-200'
            ]"
          />
        </template>
      </div>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

export default {
  components: {
    Link,
    Head
  },
  props: {
    trips: {
      type: [Object, Array],
      default: () => ({ data: [] })
    }
  },
  setup(props) {
    const searchTerm = ref('')
    const dateFrom = ref('')
    const dateTo = ref('')
    const statusFilter = ref('')

    // Handle both paginated object and plain array
    const tripsData = computed(() => {
      return props.trips.data || props.trips || []
    })

    const filteredTrips = computed(() => {
      let filtered = tripsData.value

      if (searchTerm.value) {
        filtered = filtered.filter(trip =>
          trip.tipper_number?.toLowerCase().includes(searchTerm.value.toLowerCase()) ||
          getDriverName(trip).toLowerCase().includes(searchTerm.value.toLowerCase()) ||
          getPlantName(trip).toLowerCase().includes(searchTerm.value.toLowerCase())
        )
      }

      if (dateFrom.value) {
        filtered = filtered.filter(trip => trip.delivery_date >= dateFrom.value)
      }

      if (dateTo.value) {
        filtered = filtered.filter(trip => trip.delivery_date <= dateTo.value)
      }

      if (statusFilter.value) {
        filtered = filtered.filter(trip => {
          const balance = parseFloat(trip.trip_amount) - parseFloat(trip.paid_amount)
          if (statusFilter.value === 'pending') return balance > 0
          if (statusFilter.value === 'completed') return balance === 0
          return true
        })
      }

      return filtered
    })

    const getDriverName = (trip) => {
      return trip.driver?.name || 'N/A'
    }

    const getPlantName = (trip) => {
      return trip.plant?.name || 'N/A'
    }

    const getBalance = (trip) => {
      const balance = parseFloat(trip.trip_amount) - parseFloat(trip.paid_amount)
      return balance.toFixed(2)
    }

    const formatDate = (date) => {
      if (!date) return 'N/A'
      return new Date(date).toLocaleDateString()
    }

    const getStatus = (trip) => {
      const balance = parseFloat(trip.trip_amount) - parseFloat(trip.paid_amount)
      return balance > 0 ? 'Pending' : 'Completed'
    }

    const getStatusClass = (trip) => {
      const balance = parseFloat(trip.trip_amount) - parseFloat(trip.paid_amount)
      return balance > 0
        ? 'bg-yellow-100 text-yellow-800'
        : 'bg-green-100 text-green-800'
    }

    const deleteTrip = (id) => {
      if (confirm('Are you sure you want to delete this trip?')) {
        window.$inertia.delete(route('trips.destroy', { id: id }))
      }
    }

    return {
      searchTerm,
      dateFrom,
      dateTo,
      statusFilter,
      filteredTrips,
      getDriverName,
      getPlantName,
      getBalance,
      formatDate,
      getStatus,
      getStatusClass,
      deleteTrip
    }
  }
}
</script>
