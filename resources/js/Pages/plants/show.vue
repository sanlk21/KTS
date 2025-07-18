<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
  plant: Object,
  flash: Object,
});
</script>

<template>
  <Head :title="`Plant Details - ${plant.name}`" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Plant Details</h2>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <h3 class="text-lg font-medium mb-6">Plant Details: {{ plant.name }}</h3>

            <!-- Flash Messages -->
            <div v-if="flash?.success" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
              {{ flash.success }}
            </div>
            <div v-if="flash?.error" class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
              {{ flash.error }}
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">ID</label>
                  <p class="mt-1 text-sm text-gray-900">{{ plant.id }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Name</label>
                  <p class="mt-1 text-sm text-gray-900">{{ plant.name }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                  <p class="mt-1 text-sm text-gray-900">{{ plant.phone_number || 'N/A' }}</p>
                </div>
              </div>
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700">Address</label>
                  <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ plant.address || 'N/A' }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Created At</label>
                  <p class="mt-1 text-sm text-gray-500">{{ new Date(plant.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }) }}</p>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Last Updated</label>
                  <p class="mt-1 text-sm text-gray-500">{{ new Date(plant.updated_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }) }}</p>
                </div>
              </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
              <Link
                :href="route('plants.index')"
                class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
              >
                Back to List
              </Link>
              <Link
                :href="route('plants.edit', plant.id)"
                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150"
              >
                Edit Plant
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
