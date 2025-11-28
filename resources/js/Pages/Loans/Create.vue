<template>
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Create New Loan
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Create a new loan application</p>
                </div>
                <SecondaryButton @click="$inertia.visit(route('loans.index'))">
                    Back to Loans
                </SecondaryButton>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Customer Selection -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Customer *
                                    </label>
                                    <select 
                                        v-model="form.customer_id"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-300': errors.customer_id }"
                                    >
                                        <option value="">Select a customer</option>
                                        <option 
                                            v-for="customer in customers" 
                                            :key="customer.id" 
                                            :value="customer.id"
                                        >
                                            {{ customer.full_name }} - {{ customer.email }}
                                        </option>
                                    </select>
                                    <p v-if="errors.customer_id" class="mt-1 text-sm text-red-600">
                                        {{ errors.customer_id }}
                                    </p>
                                </div>

                                <!-- Loan Amount -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Loan Amount ($) *
                                    </label>
                                    <input 
                                        type="number"
                                        v-model="form.amount"
                                        required
                                        min="100"
                                        step="0.01"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-300': errors.amount }"
                                        placeholder="0.00"
                                    >
                                    <p v-if="errors.amount" class="mt-1 text-sm text-red-600">
                                        {{ errors.amount }}
                                    </p>
                                </div>

                                <!-- Interest Rate -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Interest Rate (%) *
                                    </label>
                                    <input 
                                        type="number"
                                        v-model="form.interest_rate"
                                        required
                                        min="0.1"
                                        max="50"
                                        step="0.01"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-300': errors.interest_rate }"
                                        placeholder="0.00"
                                    >
                                    <p v-if="errors.interest_rate" class="mt-1 text-sm text-red-600">
                                        {{ errors.interest_rate }}
                                    </p>
                                </div>

                                <!-- Term Months -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Loan Term (Months) *
                                    </label>
                                    <input 
                                        type="number"
                                        v-model="form.term_months"
                                        required
                                        min="1"
                                        max="360"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-300': errors.term_months }"
                                        placeholder="12"
                                    >
                                    <p v-if="errors.term_months" class="mt-1 text-sm text-red-600">
                                        {{ errors.term_months }}
                                    </p>
                                </div>

                                <!-- Loan Type -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Loan Type *
                                    </label>
                                    <select 
                                        v-model="form.type"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-300': errors.type }"
                                    >
                                        <option value="">Select loan type</option>
                                        <option value="personal">Personal Loan</option>
                                        <option value="business">Business Loan</option>
                                        <option value="mortgage">Mortgage</option>
                                        <option value="auto">Auto Loan</option>
                                        <option value="education">Education Loan</option>
                                    </select>
                                    <p v-if="errors.type" class="mt-1 text-sm text-red-600">
                                        {{ errors.type }}
                                    </p>
                                </div>

                                <!-- Purpose -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Purpose *
                                    </label>
                                    <select 
                                        v-model="form.purpose"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-300': errors.purpose }"
                                    >
                                        <option value="">Select purpose</option>
                                        <option value="debt_consolidation">Debt Consolidation</option>
                                        <option value="home_improvement">Home Improvement</option>
                                        <option value="business_expansion">Business Expansion</option>
                                        <option value="education">Education</option>
                                        <option value="medical">Medical Expenses</option>
                                        <option value="vehicle">Vehicle Purchase</option>
                                        <option value="wedding">Wedding</option>
                                        <option value="vacation">Vacation</option>
                                        <option value="other">Other</option>
                                    </select>
                                    <p v-if="errors.purpose" class="mt-1 text-sm text-red-600">
                                        {{ errors.purpose }}
                                    </p>
                                </div>

                                <!-- Purpose Description -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Purpose Description
                                    </label>
                                    <textarea 
                                        v-model="form.purpose_description"
                                        rows="3"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-300': errors.purpose_description }"
                                        placeholder="Describe the purpose of this loan in detail..."
                                    ></textarea>
                                    <p v-if="errors.purpose_description" class="mt-1 text-sm text-red-600">
                                        {{ errors.purpose_description }}
                                    </p>
                                </div>

                                <!-- Calculation Preview -->
                                <div class="md:col-span-2 bg-gray-50 p-4 rounded-lg" v-if="calculationPreview.monthly_payment">
                                    <h3 class="text-lg font-medium text-gray-900 mb-3">Loan Calculation Preview</h3>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                        <div>
                                            <p class="text-gray-600">Monthly Payment</p>
                                            <p class="font-semibold text-gray-900">${{ calculationPreview.monthly_payment }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600">Total Payment</p>
                                            <p class="font-semibold text-gray-900">${{ calculationPreview.total_payment }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600">Total Interest</p>
                                            <p class="font-semibold text-gray-900">${{ calculationPreview.total_interest }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600">Term</p>
                                            <p class="font-semibold text-gray-900">{{ form.term_months }} months</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                                <SecondaryButton 
                                    type="button"
                                    @click="$inertia.visit(route('loans.index'))"
                                >
                                    Cancel
                                </SecondaryButton>
                                <PrimaryButton 
                                    type="submit"
                                    :disabled="form.processing"
                                    class="flex items-center space-x-2"
                                >
                                    <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>{{ form.processing ? 'Creating...' : 'Create Loan' }}</span>
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { useForm, router } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

const props = defineProps({
    customers: Array,
    errors: Object
});

const form = useForm({
    customer_id: '',
    amount: '',
    interest_rate: '',
    term_months: '',
    purpose: '',
    purpose_description: '',
    type: ''
});

// Calculate loan preview
const calculationPreview = computed(() => {
    if (!form.amount || !form.interest_rate || !form.term_months) {
        return {};
    }

    const principal = parseFloat(form.amount);
    const annualRate = parseFloat(form.interest_rate);
    const termMonths = parseInt(form.term_months);

    if (principal <= 0 || annualRate <= 0 || termMonths <= 0) {
        return {};
    }

    const monthlyRate = annualRate / 100 / 12;
    let monthlyPayment, totalPayment, totalInterest;

    if (monthlyRate > 0) {
        monthlyPayment = principal * (monthlyRate * Math.pow(1 + monthlyRate, termMonths)) / (Math.pow(1 + monthlyRate, termMonths) - 1);
    } else {
        monthlyPayment = principal / termMonths;
    }

    totalPayment = monthlyPayment * termMonths;
    totalInterest = totalPayment - principal;

    return {
        monthly_payment: monthlyPayment.toFixed(2),
        total_payment: totalPayment.toFixed(2),
        total_interest: totalInterest.toFixed(2)
    };
});

// Auto-calculate when form values change
watch(
    () => [form.amount, form.interest_rate, form.term_months],
    () => {
        // Calculation happens automatically in the computed property
    }
);

const submit = () => {
    form.post(route('loans.store'));
};
</script>