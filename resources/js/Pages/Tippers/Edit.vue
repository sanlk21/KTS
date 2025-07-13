<script setup>
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { ref } from 'vue';

const props = defineProps({ tipper: Object });

const form = ref({
    tipper_number: props.tipper.tipper_number, // Include tipper_number
    size: props.tipper.size,
    license_expiry: props.tipper.license_expiry ?? '',
});

const submit = () => {
    router.put(route('tippers.update', props.tipper.tipper_number), form.value);
};
</script>

<template>
    <Head title="Edit Tipper" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Tipper</h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <form @submit.prevent="submit">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tipper Number</label>
                            <input v-model="form.tipper_number" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700">Size</label>
                            <select v-model="form.size" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Select Size</option>
                                <option value="2">2 Ton</option>
                                <option value="4">4 Ton</option>
                            </select>
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700">License Expiry</label>
                            <input v-model="form.license_expiry" type="date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                        </div>
                        <div class="mt-6 flex space-x-2">
                            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Update</button>
                            <button type="button" @click="router.get(route('tippers.index'))" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
