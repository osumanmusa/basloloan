<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Loan Calculator
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Calculator Form -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-semibold mb-6">Loan Parameters</h3>
                        
                        <form @submit.prevent="calculate">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Loan Amount</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">$</span>
                                        </div>
                                        <input
                                            v-model="form.amount"
                                            type="number"
                                            class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md"
                                            placeholder="0.00"
                                        />
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Interest Rate (%)</label>
                                    <input
                                        v-model="form.interest_rate"
                                        type="number"
                                        step="0.01"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Loan Term (Months)</label>
                                    <input
                                        v-model="form.term_months"
                                        type="number"
                                        class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Loan Type</label>
                                    <select
                                        v-model="form.loan_type"
                                        class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    >
                                        <option value="personal">Personal Loan</option>
                                        <option value="business">Business Loan</option>
                                        <option value="mortgage">Mortgage</option>
                                        <option value="auto">Auto Loan</option>
                                        <option value="education">Education Loan</option>
                                    </select>
                                </div>

                                <PrimaryButton type="submit" class="w-full">
                                    Calculate Loan
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>

                    <!-- Results -->
                    <div class="bg-white p-6 rounded-lg shadow" v-if="results">
                        <h3 class="text-lg font-semibold mb-6">Loan Calculation Results</h3>
                        
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="text-center p-4 bg-blue-50 rounded-lg">
                                    <p class="text-sm text-blue-600">Monthly Payment</p>
                                    <p class="text-2xl font-bold text-blue-900">${{ results.monthly_payment }}</p>
                                </div>
                                <div class="text-center p-4 bg-green-50 rounded-lg">
                                    <p class="text-sm text-green-600">Total Payment</p>
                                    <p class="text-2xl font-bold text-green-900">${{ results.total_payment }}</p>
                                </div>
                            </div>

                            <div class="border-t pt-4">
                                <h4 class="font-semibold mb-3">Payment Breakdown</h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span>Principal Amount:</span>
                                        <span>${{ form.amount }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Total Interest:</span>
                                        <span>${{ results.total_interest }}</span>
                                    </div>
                                    <div class="flex justify-between font-semibold border-t pt-2">
                                        <span>Total Amount:</span>
                                        <span>${{ results.total_payment }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Amortization Schedule Preview -->
                            <div class="border-t pt-4">
                                <h4 class="font-semibold mb-3">Amortization Schedule (First 12 Months)</h4>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-sm">
                                        <thead>
                                            <tr class="bg-gray-50">
                                                <th class="px-3 py-2 text-left">Month</th>
                                                <th class="px-3 py-2 text-left">Payment</th>
                                                <th class="px-3 py-2 text-left">Principal</th>
                                                <th class="px-3 py-2 text-left">Interest</th>
                                                <th class="px-3 py-2 text-left">Balance</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="payment in results.amortization_schedule.slice(0, 12)" :key="payment.month">
                                                <td class="px-3 py-2">{{ payment.month }}</td>
                                                <td class="px-3 py-2">${{ payment.payment }}</td>
                                                <td class="px-3 py-2">${{ payment.principal }}</td>
                                                <td class="px-3 py-2">${{ payment.interest }}</td>
                                                <td class="px-3 py-2">${{ payment.balance }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const form = ref({
    amount: 10000,
    interest_rate: 8.5,
    term_months: 36,
    loan_type: 'personal'
});

const results = ref(null);

const calculate = async () => {
    try {
        const response = await router.post(route('calculator.calculate'), form.value);
        results.value = response.props.results;
    } catch (error) {
        console.error('Calculation error:', error);
    }
};
</script>