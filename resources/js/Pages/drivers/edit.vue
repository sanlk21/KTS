```vue
<template>
  <div>
    <Head title="Edit Driver" />

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Edit Driver</h2>

            <!-- Error Messages -->
            <div v-if="form.errors" class="mb-4">
              <div v-for="(error, key) in form.errors" :key="key" class="p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                {{ error }}
              </div>
            </div>

            <!-- Edit Form -->
            <form @submit.prevent="submit">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                  <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                  <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    required
                  />
                </div>
                <div class="mb-4">
                  <label for="nic" class="block text-sm font-medium text-gray-700">NIC</label>
                  <input
                    id="nic"
                    v-model="form.nic"
                    type="text"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    required
                  />
                </div>
                <div class="mb-4">
                  <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
                  <input
                    id="phone_number"
                    v-model="form.phone_number"
                    type="tel"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    required
                  />
                </div>
                <div class="mb-4">
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
                <div class="mb-4 md:col-span-2">
                  <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                  <textarea
                    id="address"
                    v-model="form.address"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    rows="4"
                    required
                  ></textarea>
                </div>
                <div class="mb-4">
                  <label for="photo" class="block text-sm font-medium text-gray-700">Photo</label>
                  <div class="mt-1 flex items-center">
                    <img
                      v-if="form.photo || driver.photo_url"
                      :src="form.photo || driver.photo_url"
                      alt="Driver photo"
                      class="h-20 w-20 rounded-full object-cover mr-4"
                    />
                    <input
                      id="photo"
                      type="file"
                      accept="image/*"
                      @change="handlePhotoUpload"
                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                    />
                  </div>
                </div>
              </div>
              <div class="flex justify-end mt-6">
                <Link
                  :href="route('drivers.index')"
                  class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mr-2"
                >
                  Cancel
                </Link>
                <button
                  type="submit"
                  :disabled="form.processing"
                  class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50"
                >
                  <span v-if="form.processing">Updating...</span>
                  <span v-else>Update</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { Head, Link, useForm } from '@inertiajs/vue3'

export default {
  components: {
    Head,
    Link
  },
  props: {
    driver: {
      type: Object,
      required: true
    },
    tippers: {
      type: Array,
      default: () => []
    }
  },
  setup(props) {
    const form = useForm({
      name: props.driver.name,
      tipper_number: props.driver.tipper_number,
      nic: props.driver.nic,
      phone_number: props.driver.phone_number,
      address: props.driver.address,
      photo: null // Initialize as null for file uploads
    })

    return { form }
  },
  methods: {
    handlePhotoUpload(event) {
      this.form.photo = event.target.files[0]
    },
    submit() {
      this.form.put(route('drivers.update', this.driver.id), {
        preserveScroll: true,
        onSuccess: () => {
          this.form.reset('photo') // Reset photo field after successful submission
        }
      })
    }
  }
}
</script>
