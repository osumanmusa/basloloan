<template>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
        <div class="space-y-3">
            <PrimaryButton 
                class="w-full justify-center" 
                @click="$inertia.visit(route('loans.create'))"
            >
                Create New Loan
            </PrimaryButton>
            <SecondaryButton 
                class="w-full justify-center" 
                @click="$inertia.visit(route('customers.create'))"
            >
                Add New Customer
            </SecondaryButton>
            <SecondaryButton 
                class="w-full justify-center" 
                @click="$inertia.visit(route('payments.create'))"
            >
                Record Payment
            </SecondaryButton>
            <SecondaryButton 
                class="w-full justify-center" 
                @click="$inertia.visit(route('payments.bulk'))"
            >
                Bulk Payments
            </SecondaryButton>
        </div>
    </div>
</template>

<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
</script>