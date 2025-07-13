<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
  tippers: Array,
  flash: Object
});

const form = ref({
  name: '',
  tipper_number: '',
  nic: '',
  phone_number: '',
  address: '',
  photo: null
});

const photoPreview = ref(null);

const handlePhotoChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    form.value.photo = file;
    const reader = new FileReader();
    reader.onload = (e) => {
      photoPreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const removePhoto = () => {
  form.value.photo = null;
  photoPreview.value = null;
  document.getElementById('photo').value = '';
};

const submit = () => {
  const formData = new FormData();

  Object.keys(form.value).forEach(key => {
    if (form.value[key] !== null) {
      formData.append(key, form.value[key]);
    }
  });

  router.post(route('drivers.store'), formData, {
    forceFormData: true,
  });
};
</script>

<template>
  <Head title="Create Driver" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Create Driver</h2>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <h3 class="text-lg font-medium mb-6">Add New Driver</h3>

            <!-- Flash Messages -->
            <div v-if="flash?.success" class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
              {{ flash.success }}
            </div>
            <div v-if="flash?.error" class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
              {{ flash.error }}
            </div>

            <!-- Error Messages -->
            <div v-if="$page.props.errors && Object.keys($page.props.errors).length > 0" class="mb-4">
              <div v-for="(error, key) in $page.props.errors" :key="key" class="mb-2 p-3 bg-red-100 border border-red-400 text-red-700 rounded">
                <strong>{{ key.charAt(0).toUpperCase() + key.slice(1) }}:</strong> {{ error }}
              </div>
            </div>

            <form @submit.prevent="submit" enctype="multipart/form-data">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <!-- Photo Upload -->
                <div class="md:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-2">Driver Photo</label>
                  <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                      <div v-if="photoPreview" class="relative">
                        <img :src="photoPreview" alt="Preview" class="h-20 w-20 rounded-full object-cover border-2 border-gray-300">
                        <button
                          type="button"
                          @click="removePhoto"
                          class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                          </svg>
                        </button>
                      </div>
                      <div v-else class="h-20 w-20 rounded-full bg-gray-300 flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                      </div>
                    </div>
                    <div>
                      <input
                        id="photo"
                        type="file"
                        @change="handlePhotoChange"
                        accept="image/*"
                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                      />
                      <p class="mt-1 text-sm text-gray-500">JPG, PNG up to 5MB</p>
                    </div>
                  </div>
                </div>

                <!-- Name -->
                <div>
                  <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                  <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    required
                  />
                </div>

                <!-- NIC -->
                <div>
                  <label for="nic" class="block text-sm font-medium text-gray-700">NIC Number</label>
                  <input
                    id="nic"
                    v-model="form.nic"
                    type="text"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    required
                  />
                </div>

                <!-- Phone Number -->
                <div>
                  <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
                  <input
                    id="phone_number"
                    v-model="form.phone_number"
                    type="tel"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    required
                  />
                </div>

                <!-- Tipper Number -->
                <div>
                  <label for="tipper_number" class="block text-sm font-medium text-gray-700">Tipper Number</label>
                  <select
                    id="tipper_number"
                    v-model="form.tipper_number"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    required
                  >
                    <option value="" disabled>Select a tipper</option>
                    <option v-for="tipper in tippers" :key="tipper.tipper_number" :value="tipper.tipper_number">
                      {{ tipper.tipper_number }}
                    </option>
                  </select>
                </div>

                <!-- Address -->
                <div class="md:col-span-2">
                  <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                  <textarea
                    id="address"
                    v-model="form.address"
                    rows="3"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    required
                  ></textarea>
                </div>
              </div>

              <div class="flex justify-end mt-6 space-x-3">
                <Link
                  :href="route('drivers.index')"
                  class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150"
                >
                  Cancel
                </Link>
                <button
                  type="submit"
                  class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150"
                >
                  Create Driver
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
