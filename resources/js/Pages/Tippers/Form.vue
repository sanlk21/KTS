<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    tipper: {
        type: Object,
        default: () => ({}),
    },
});

const form = useForm({
    tipper_number: props.tipper.tipper_number || '',
    size: props.tipper.size || '2',
    license_expiry: props.tipper.license_expiry || '',
});

const submit = () => {
    props.tipper.tipper_number
        ? form.put(route('tippers.update', props.tipper.tipper_number))
        : form.post(route('tippers.store'));
};
</script>

<template>
    <Head :title="tipper.tipper_number ? 'Edit Tipper' : 'Create Tipper'" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ tipper.tipper_number ? 'Edit Tipper' : 'Create New Tipper' }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 gap-6">
                                <div v-if="!tipper.tipper_number">
                                    <InputLabel for="tipper_number" value="Tipper Number" />
                                    <TextInput
                                        id="tipper_number"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.tipper_number"
                                        required
                                        autofocus
                                    />
                                    <InputError class="mt-2" :message="form.errors.tipper_number" />
                                </div>

                                <div>
                                    <InputLabel for="size" value="Size (ton)" />
                                    <select
                                        id="size"
                                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        v-model="form.size"
                                        required
                                    >
                                        <option value="1">1 Cube</option>
                                        <option value="2">2 Cube</option>
                                        <option value="3">3 Cube</option>
                                        <option value="4">4 Cube</option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.size" />
                                </div>

                                <div>
                                    <InputLabel for="license_expiry" value="License Expiry Date" />
                                    <TextInput
                                        id="license_expiry"
                                        type="date"
                                        class="mt-1 block w-full"
                                        v-model="form.license_expiry"
                                    />
                                    <InputError class="mt-2" :message="form.errors.license_expiry" />
                                </div>

                                <div class="flex items-center justify-end mt-4">
                                    <Link
                                        :href="route('tippers.index')"
                                        class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                    >
                                        Cancel
                                    </Link>

                                    <PrimaryButton
                                        class="ml-4"
                                        :class="{ 'opacity-25': form.processing }"
                                        :disabled="form.processing"
                                    >
                                        {{ tipper.tipper_number ? 'Update' : 'Create' }}
                                    </PrimaryButton>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
