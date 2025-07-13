<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    tipper_number: '',
    size: '',
    license_expiry: '',
});

const submit = () => {
    form.post(route('tippers.store'));
};
</script>

<template>
    <Head title="Add New Tipper" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Add New Tipper</h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white p-6 rounded shadow-sm">
                    <form @submit.prevent="submit">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Tipper Number</label>
                            <input v-model="form.tipper_number" type="text" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                            <div v-if="form.errors.tipper_number" class="text-red-600 text-sm">{{ form.errors.tipper_number }}</div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Size (tons)</label>
                            <select v-model="form.size" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="">Select Size</option>
                                <option value="1">1 Cube</option>
                                <option value="2">2 Cube</option>
                                <option value="3">3 Cube</option>
                                <option value="4">4 Cube</option>
                            </select>
                            <div v-if="form.errors.size" class="text-red-600 text-sm">{{ form.errors.size }}</div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">License Expiry</label>
                            <input v-model="form.license_expiry" type="date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                            <div v-if="form.errors.license_expiry" class="text-red-600 text-sm">{{ form.errors.license_expiry }}</div>
                        </div>

                        <div>
                            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
