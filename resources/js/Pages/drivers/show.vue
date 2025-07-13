<template>
  <div>
    <Head title="Driver Details" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Driver Details</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">ID</label>
                <p class="mt-1 text-sm text-gray-900">{{ driver.id }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <p class="mt-1 text-sm text-gray-900">{{ driver.name }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">NIC</label>
                <p class="mt-1 text-sm text-gray-900">{{ driver.nic }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                <p class="mt-1 text-sm text-gray-900">{{ driver.phone_number }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Tipper Number</label>
                <p class="mt-1 text-sm text-gray-900">{{ driver.tipper_number }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Address</label>
                <p class="mt-1 text-sm text-gray-900">{{ driver.address }}</p>
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700">Photo</label>
                <div class="mt-1">
                  <img
                    v-if="driver.photo"
                    :src="driver.photo"
                    alt="Driver photo"
                    class="h-20 w-20 rounded-full object-cover"
                  />
                  <p v-else class="text-sm text-gray-500">No photo available</p>
                </div>
              </div>
            </div>

            <div class="mt-6 flex justify-end">
              <Link
                :href="route('drivers.index')"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2"
              >
                Back
              </Link>
              <Link
                :href="route('drivers.edit', driver.id)"
                class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded"
              >
                Edit
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Head, Link } from '@inertiajs/vue3'

export default {
  components: {
    Head,
    Link
  },
  props: {
    driver: {
      type: Object,
      required: true
    }
  }
}
</script>
