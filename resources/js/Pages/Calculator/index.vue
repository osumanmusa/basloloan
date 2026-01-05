<template>
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Loan Calculator
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Calculate loan payments, interest, and amortization schedule
                    </p>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Calculator Form -->
                    <div class="lg:col-span-1">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Loan Details</h3>
                                
                                <form @submit.prevent="calculateLoan" class="space-y-4">
                                    <!-- Loan Amount -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Loan Amount ($)
                                        </label>
                                        <input 
                                            type="number"
                                            v-model="form.loan_amount"
                                            required
                                            min="100"
                                            step="100"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            placeholder="10,000"
                                        >
                                    </div>

                                    <!-- Interest Rate -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Annual Interest Rate (%)
                                        </label>
                                        <input 
                                            type="number"
                                            v-model="form.interest_rate"
                                            required
                                            min="0.1"
                                            max="50"
                                            step="0.1"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            placeholder="5.0"
                                        >
                                    </div>

                                    <!-- Loan Term -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Loan Term
                                            </label>
                                            <input 
                                                type="number"
                                                v-model="form.loan_term"
                                                required
                                                min="1"
                                                max="360"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                                placeholder="12"
                                            >
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                &nbsp;
                                            </label>
                                            <select 
                                                v-model="form.term_type"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            >
                                                <option value="months">Months</option>
                                                <option value="years">Years</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Calculate Button -->
                                    <div class="pt-4">
                                        <PrimaryButton 
                                            type="submit"
                                            :disabled="calculating"
                                            class="w-full flex justify-center items-center space-x-2"
                                        >
                                            <svg v-if="calculating" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <span>{{ calculating ? 'Calculating...' : 'Calculate Loan' }}</span>
                                        </PrimaryButton>
                                    </div>
                                </form>

                                <!-- Quick Examples -->
                                <div class="mt-6 pt-6 border-t border-gray-200">
                                    <h4 class="text-sm font-medium text-gray-900 mb-3">Quick Examples</h4>
                                    <div class="space-y-2">
                                        <button 
                                            v-for="example in quickExamples"
                                            :key="example.name"
                                            @click="loadExample(example)"
                                            class="w-full text-left p-2 text-sm text-gray-600 hover:bg-gray-50 rounded-md transition-colors duration-200"
                                        >
                                            <div class="font-medium text-gray-900">{{ example.name }}</div>
                                            <div class="text-xs text-gray-500">
                                                ${{ example.amount.toLocaleString() }} • {{ example.rate }}% • {{ example.term }} {{ example.termType }}
                                            </div>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Results & Amortization -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Results Summary -->
                        <div v-if="calculationResults" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Loan Summary</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                                        <p class="text-sm text-gray-600">Monthly Payment</p>
                                        <p class="text-2xl font-bold text-blue-600">${{ formatCurrency(calculationResults.monthly_payment) }}</p>
                                    </div>
                                    <div class="text-center p-4 bg-green-50 rounded-lg">
                                        <p class="text-sm text-gray-600">Total Payment</p>
                                        <p class="text-2xl font-bold text-green-600">${{ formatCurrency(calculationResults.total_payment) }}</p>
                                    </div>
                                    <div class="text-center p-4 bg-red-50 rounded-lg">
                                        <p class="text-sm text-gray-600">Total Interest</p>
                                        <p class="text-2xl font-bold text-red-600">${{ formatCurrency(calculationResults.total_interest) }}</p>
                                    </div>
                                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                                        <p class="text-sm text-gray-600">Loan Term</p>
                                        <p class="text-2xl font-bold text-purple-600">{{ calculationResults.term_months }} months</p>
                                    </div>
                                </div>

                                <!-- Payment Breakdown -->
                                <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <h4 class="text-md font-medium text-gray-900 mb-3">Payment Breakdown</h4>
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Principal Amount:</span>
                                                <span class="font-semibold">${{ formatCurrency(calculationResults.principal) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Total Interest:</span>
                                                <span class="font-semibold">${{ formatCurrency(calculationResults.total_interest) }}</span>
                                            </div>
                                            <div class="flex justify-between border-t pt-2">
                                                <span class="text-gray-600 font-medium">Total Payment:</span>
                                                <span class="font-bold text-green-600">${{ formatCurrency(calculationResults.total_payment) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="text-md font-medium text-gray-900 mb-3">Interest Analysis</h4>
                                        <div class="space-y-2">
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Interest to Principal Ratio:</span>
                                                <span class="font-semibold">{{ calculateInterestRatio() }}%</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-600">Effective Interest Rate:</span>
                                                <span class="font-semibold">{{ calculateEffectiveRate() }}%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Amortization Schedule -->
                        <div v-if="calculationResults && calculationResults.amortization_schedule.length > 0" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-lg font-medium text-gray-900">Amortization Schedule</h3>
                                    <button 
                                        @click="exportSchedule"
                                        class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-green-600 bg-green-50 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200"
                                    >
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Export
                                    </button>
                                </div>

                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Payment #
                                                </th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Date
                                                </th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Payment
                                                </th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Principal
                                                </th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Interest
                                                </th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                    Balance
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            <tr 
                                                v-for="payment in paginatedSchedule" 
                                                :key="payment.month"
                                                class="hover:bg-gray-50"
                                            >
                                                <td class="px-4 py-3 text-sm text-gray-900">
                                                    {{ payment.month }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-500">
                                                    {{ payment.payment_date }}
                                                </td>
                                                <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                                    ${{ formatCurrency(payment.payment) }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-green-600">
                                                    ${{ formatCurrency(payment.principal) }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-red-600">
                                                    ${{ formatCurrency(payment.interest) }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-500">
                                                    ${{ formatCurrency(payment.balance) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <div class="mt-4 flex items-center justify-between">
                                    <div class="text-sm text-gray-700">
                                        Showing {{ pagination.from }} to {{ pagination.to }} of {{ calculationResults.amortization_schedule.length }} payments
                                    </div>
                                    <div class="flex space-x-2">
                                        <button 
                                            @click="previousPage"
                                            :disabled="pagination.currentPage === 1"
                                            class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                        >
                                            Previous
                                        </button>
                                        <button 
                                            @click="nextPage"
                                            :disabled="pagination.currentPage === pagination.totalPages"
                                            class="px-3 py-1 text-sm border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                        >
                                            Next
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-else class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No calculation yet</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Enter loan details and click "Calculate Loan" to see the results.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { router } from '@inertiajs/vue3';
import { ref, reactive, computed, onMounted } from 'vue';

// Define props from Inertia
const props = defineProps({
    calculationResults: Object,
    formData: Object
});

const form = reactive({
    loan_amount: props.formData?.loan_amount || 10000,
    interest_rate: props.formData?.interest_rate || 5.0,
    loan_term: props.formData?.loan_term || 12,
    term_type: props.formData?.term_type || 'months'
});

const calculating = ref(false);
const currentPage = ref(1);
const itemsPerPage = 12;

const quickExamples = [
    { name: 'Personal Loan', amount: 10000, rate: 7.5, term: 36, termType: 'months' },
    { name: 'Car Loan', amount: 25000, rate: 4.5, term: 60, termType: 'months' },
    { name: 'Mortgage', amount: 300000, rate: 3.5, term: 30, termType: 'years' },
    { name: 'Small Business', amount: 50000, rate: 6.0, term: 24, termType: 'months' }
];

const pagination = computed(() => {
    if (!props.calculationResults) return { currentPage: 1, totalPages: 1, from: 0, to: 0 };
    
    const totalItems = props.calculationResults.amortization_schedule.length;
    const totalPages = Math.ceil(totalItems / itemsPerPage);
    const from = ((currentPage.value - 1) * itemsPerPage) + 1;
    const to = Math.min(currentPage.value * itemsPerPage, totalItems);
    
    return {
        currentPage: currentPage.value,
        totalPages,
        from,
        to
    };
});

const paginatedSchedule = computed(() => {
    if (!props.calculationResults) return [];
    
    const start = (currentPage.value - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    return props.calculationResults.amortization_schedule.slice(start, end);
});

const calculateLoan = () => {
    calculating.value = true;
    
    router.post(route('calculator.calculate'), form, {
        preserveState: true,
        preserveScroll: true,
        onFinish: () => {
            calculating.value = false;
            currentPage.value = 1;
        }
    });
};

const loadExample = (example) => {
    form.loan_amount = example.amount;
    form.interest_rate = example.rate;
    form.loan_term = example.term;
    form.term_type = example.termType;
};

const formatCurrency = (value) => {
    if (value === null || value === undefined) return '0.00';
    return parseFloat(value).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
};

const calculateInterestRatio = () => {
    if (!props.calculationResults) return '0';
    return ((props.calculationResults.total_interest / props.calculationResults.principal) * 100).toFixed(1);
};

const calculateEffectiveRate = () => {
    if (!props.calculationResults) return '0';
    return ((props.calculationResults.total_interest / props.calculationResults.principal) * 100 / (props.calculationResults.term_months / 12)).toFixed(2);
};

const previousPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
    }
};

const nextPage = () => {
    if (currentPage.value < pagination.value.totalPages) {
        currentPage.value++;
    }
};

const exportSchedule = () => {
    if (!props.calculationResults) return;
    
    // Simple CSV export
    const headers = ['Payment #', 'Date', 'Payment', 'Principal', 'Interest', 'Balance'];
    const csvData = props.calculationResults.amortization_schedule.map(payment => [
        payment.month,
        payment.payment_date,
        payment.payment,
        payment.principal,
        payment.interest,
        payment.balance
    ]);
    
    const csvContent = [headers, ...csvData]
        .map(row => row.map(cell => `"${cell}"`).join(','))
        .join('\n');
    
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `loan-amortization-schedule-${new Date().toISOString().split('T')[0]}.csv`;
    a.click();
    window.URL.revokeObjectURL(url);
};

// Auto-calculate on page load if we have form data but no results
onMounted(() => {
    if (props.formData && !props.calculationResults) {
        calculateLoan();
    }
});
</script>