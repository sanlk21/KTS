```vue
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps({
  drivers: Array,
  flash: Object
});

const deleteDriver = (id) => {
  if (confirm('Are you sure you want to delete this driver?')) {
    router.delete(route('drivers.destroy', id), {
      preserveScroll: true,
      onSuccess: () => {}
    });
  }
};

// Debug: Log drivers to console
console.log('Drivers data at', new Date().toLocaleString('en-US', { timeZone: 'Asia/Colombo' }), ':', props.drivers);

const handleImageError = (event) => {
  console.error('Image failed to load for URL:', event.target.src);
  event.target.style.display = 'none'; // Hide broken image
};
</script>

<template>
  <Head title="Drivers" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Drivers Management</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
              <h3 class="text-lg font-medium">All Drivers</h3>
              <Link
                :href="route('drivers.create')"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150"
              >
                Add New Driver
              </Link>
            </div>

            <!-- Flash Messages -->
            <div v-if="flash?.success" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
              {{ flash.success }}
            </div>
            <div v-if="flash?.error" class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
              {{ flash.error }}
            </div>

            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Photo
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Name
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      NIC
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Phone
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Tipper Number
                    </th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                      Actions
                    </th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="driver in drivers" :key="driver.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      <div class="flex-shrink-0 h-10 w-10">
                        <img
                          v-if="driver.photo_url"
                          :src="driver.photo_url"
                          :alt="driver.name"
                          class="h-10 w-10 rounded-full object-cover"
                          @error="handleImageError"
                        />
                        <div
                          v-else
                          class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center"
                        >
                          <span class="text-sm font-medium text-gray-700">
                            {{ driver.name.charAt(0).toUpperCase() }}
                          </span>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm font-medium text-gray-900">{{ driver.name }}</div>
                      <div class="text-sm text-gray-500">{{ driver.address }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ driver.nic }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ driver.phone_number }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ driver.tipper_number }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <Link
                        :href="route('drivers.show', driver.id)"
                        class="text-indigo-600 hover:text-indigo-900 mr-3"
                      >
                        View
                      </Link>
                      <Link
                        :href="route('drivers.edit', driver.id)"
                        class="text-indigo-600 hover:text-indigo-900 mr-3"
                      >
                        Edit
                      </Link>
                      <button
                        @click="deleteDriver(driver.id)"
                        class="text-red-600 hover:text-red-900"
                      >
                        Delete
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div v-if="!drivers.length" class="text-center py-8">
              <p class="text-gray-500">No drivers found.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

