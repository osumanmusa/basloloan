<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Bulk Payment Processing
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-center">
                        <h3 class="text-lg font-semibold mb-4">Bulk Payment Processing</h3>
                        <p class="text-gray-600 mb-4">
                            This feature will be implemented soon. You'll be able to upload Excel/CSV files 
                            with multiple payments and process them in bulk.
                        </p>
                        <PrimaryButton @click="$inertia.visit(route('payments.index'))">
                            Back to Payments
                        </PrimaryButton>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
</script>