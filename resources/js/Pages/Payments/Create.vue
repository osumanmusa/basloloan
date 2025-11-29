<template>
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Record New Payment
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Record a loan payment</p>
                </div>
                <SecondaryButton @click="$inertia.visit(route('payments.index'))">
                    Back to Payments
                </SecondaryButton>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Success/Error Messages -->
                        <div v-if="$page.props.flash.success" class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            {{ $page.props.flash.success }}
                        </div>

                        <div v-if="$page.props.flash.error" class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            {{ $page.props.flash.error }}
                        </div>

                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Loan Selection -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Select Loan *
                                    </label>
                                    <select 
                                        v-model="form.loan_id"
                                        required
                                        @change="onLoanChange"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-300': form.errors.loan_id }"
                                    >
                                        <option value="">Select a loan</option>
                                        <option 
                                            v-for="loan in loans" 
                                            :key="loan.id" 
                                            :value="loan.id"
                                        >
                                            Loan #{{ loan.id }} - {{ loan.customer.first_name }} {{ loan.customer.last_name }} 
                                            - ${{ loan.remaining_balance.toLocaleString() }} remaining
                                            ({{ loan.status }})
                                            {{ loan.scheduled_payments.length > 0 ? `- ${loan.scheduled_payments.length} scheduled payments` : '' }}
                                        </option>
                                    </select>
                                    <p v-if="form.errors.loan_id" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.loan_id }}
                                    </p>
                                </div>

                                <!-- Selected Loan Info -->
                                <div class="md:col-span-2 bg-blue-50 p-4 rounded-lg" v-if="selectedLoan">
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Loan Information</h3>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                        <div>
                                            <p class="text-gray-600">Customer</p>
                                            <p class="font-semibold text-gray-900">
                                                {{ selectedLoan.customer.first_name }} {{ selectedLoan.customer.last_name }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600">Remaining Balance</p>
                                            <p class="font-semibold text-gray-900">
                                                ${{ selectedLoan.remaining_balance.toLocaleString() }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600">Monthly Payment</p>
                                            <p class="font-semibold text-gray-900">
                                                ${{ selectedLoan.monthly_payment.toLocaleString() }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600">Status</p>
                                            <span :class="statusBadgeClass(selectedLoan.status)" class="px-2 py-1 text-xs font-medium rounded-full">
                                                {{ formatStatus(selectedLoan.status) }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <!-- Scheduled Payments Info -->
                                    <div class="mt-4" v-if="selectedLoan.scheduled_payments.length > 0">
                                        <h4 class="text-md font-medium text-gray-900 mb-2">Scheduled Payments</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                                            <div v-for="payment in selectedLoan.scheduled_payments" :key="payment.id" 
                                                 class="bg-white p-2 rounded border">
                                                <div class="flex justify-between items-center">
                                                    <span class="font-medium">Due: {{ formatDate(payment.due_date) }}</span>
                                                    <span class="text-green-600 font-semibold">${{ payment.amount.toLocaleString() }}</span>
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    Principal: ${{ payment.principal_amount.toLocaleString() }} | 
                                                    Interest: ${{ payment.interest_amount.toLocaleString() }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-4 text-yellow-600 text-sm" v-else>
                                        <p>No scheduled payments found for this loan.</p>
                                    </div>
                                </div>

                                <!-- Payment Type Selection (Only show if scheduled payments exist) -->
                                <div class="md:col-span-2" v-if="selectedLoan && selectedLoan.scheduled_payments.length > 0">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Payment Type
                                    </label>
                                    <div class="grid grid-cols-2 gap-4">
                                        <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none">
                                            <input type="radio" name="payment_type" v-model="paymentType" value="regular" class="sr-only">
                                            <span class="flex flex-1">
                                                <span class="flex flex-col">
                                                    <span class="block text-sm font-medium text-gray-900">Regular Payment</span>
                                                    <span class="mt-1 flex items-center text-sm text-gray-500">Record a standard payment</span>
                                                </span>
                                            </span>
                                            <svg class="h-5 w-5 text-blue-600" :class="{'invisible': paymentType !== 'regular'}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        </label>
                                        <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none">
                                            <input type="radio" name="payment_type" v-model="paymentType" value="scheduled" class="sr-only">
                                            <span class="flex flex-1">
                                                <span class="flex flex-col">
                                                    <span class="block text-sm font-medium text-gray-900">Pay Scheduled</span>
                                                    <span class="mt-1 flex items-center text-sm text-gray-500">Pay against scheduled payment</span>
                                                </span>
                                            </span>
                                            <svg class="h-5 w-5 text-blue-600" :class="{'invisible': paymentType !== 'scheduled'}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        </label>
                                    </div>
                                </div>

                                <!-- Scheduled Payment Selection -->
                                <div class="md:col-span-2" v-if="paymentType === 'scheduled' && selectedLoan && selectedLoan.scheduled_payments.length > 0">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Select Scheduled Payment to Pay
                                    </label>
                                    <select 
                                        v-model="selectedScheduledPaymentId"
                                        @change="onScheduledPaymentChange"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        required
                                    >
                                        <option value="">Select a scheduled payment</option>
                                        <option 
                                            v-for="payment in selectedLoan.scheduled_payments" 
                                            :key="payment.id" 
                                            :value="payment.id"
                                        >
                                            Due: {{ formatDate(payment.due_date) }} - Amount: ${{ payment.amount.toLocaleString() }}
                                            (Principal: ${{ payment.principal_amount.toLocaleString() }}, Interest: ${{ payment.interest_amount.toLocaleString() }})
                                        </option>
                                    </select>
                                </div>

                                <!-- Payment Amount -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Payment Amount ($) *
                                    </label>
                                    <input 
                                        type="number"
                                        v-model="form.amount"
                                        required
                                        min="0.01"
                                        step="0.01"
                                        @input="calculateAutoSplit"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-300': form.errors.amount }"
                                        placeholder="0.00"
                                        :max="getMaxAmount()"
                                    >
                                    <p v-if="form.errors.amount" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.amount }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1" v-if="selectedScheduledPayment">
                                        Scheduled amount: ${{ selectedScheduledPayment.amount.toLocaleString() }}
                                    </p>
                                </div>

                                <!-- Payment Status -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Payment Status *
                                    </label>
                                    <select 
                                        v-model="form.status"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-300': form.errors.status }"
                                    >
                                        <option value="completed">Completed</option>
                                        <option value="pending">Pending</option>
                                        <option value="partial">Partial</option>
                                        <option value="late">Late</option>
                                    </select>
                                    <p v-if="form.errors.status" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.status }}
                                    </p>
                                </div>

                                <!-- Rest of the form remains the same -->
                                <!-- ... (Principal Amount, Interest Amount, Payment Date, Due Date, Payment Method, Reference Number, Notes) ... -->

                                <!-- Principal Amount -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Principal Amount ($)
                                    </label>
                                    <input 
                                        type="number"
                                        v-model="form.principal_amount"
                                        min="0"
                                        step="0.01"
                                        :max="selectedLoan ? selectedLoan.remaining_balance : 0"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-300': form.errors.principal_amount }"
                                        placeholder="0.00"
                                    >
                                    <p v-if="form.errors.principal_amount" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.principal_amount }}
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        Auto-calculated if left blank
                                    </p>
                                </div>

                                <!-- Interest Amount -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Interest Amount ($)
                                    </label>
                                    <input 
                                        type="number"
                                        v-model="form.interest_amount"
                                        min="0"
                                        step="0.01"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-300': form.errors.interest_amount }"
                                        placeholder="0.00"
                                    >
                                    <p v-if="form.errors.interest_amount" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.interest_amount }}
                                    </p>
                                </div>

                                <!-- Payment Date -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Payment Date *
                                    </label>
                                    <input 
                                        type="date"
                                        v-model="form.payment_date"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-300': form.errors.payment_date }"
                                    >
                                    <p v-if="form.errors.payment_date" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.payment_date }}
                                    </p>
                                </div>

                                <!-- Due Date -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Due Date
                                    </label>
                                    <input 
                                        type="date"
                                        v-model="form.due_date"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-300': form.errors.due_date }"
                                    >
                                    <p v-if="form.errors.due_date" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.due_date }}
                                    </p>
                                </div>

                                <!-- Payment Method -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Payment Method *
                                    </label>
                                    <select 
                                        v-model="form.payment_method"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-300': form.errors.payment_method }"
                                    >
                                        <option value="">Select payment method</option>
                                        <option value="cash">Cash</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="check">Check</option>
                                        <option value="mobile_money">Mobile Money</option>
                                        <option value="online">Online</option>
                                    </select>
                                    <p v-if="form.errors.payment_method" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.payment_method }}
                                    </p>
                                </div>

                                <!-- Reference Number -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Reference Number
                                    </label>
                                    <input 
                                        type="text"
                                        v-model="form.reference_number"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-300': form.errors.reference_number }"
                                        placeholder="TRX123456"
                                    >
                                    <p v-if="form.errors.reference_number" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.reference_number }}
                                    </p>
                                </div>

                                <!-- Notes -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Notes
                                    </label>
                                    <textarea 
                                        v-model="form.notes"
                                        rows="3"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-300': form.errors.notes }"
                                        placeholder="Additional payment notes..."
                                    ></textarea>
                                    <p v-if="form.errors.notes" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.notes }}
                                    </p>
                                </div>

                                <!-- Amount Summary -->
                                <div class="md:col-span-2 bg-gray-50 p-4 rounded-lg" v-if="form.amount">
                                    <h3 class="text-lg font-medium text-gray-900 mb-3">Payment Summary</h3>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                        <div>
                                            <p class="text-gray-600">Total Amount</p>
                                            <p class="font-semibold text-gray-900">${{ parseFloat(form.amount || 0).toFixed(2) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600">Principal</p>
                                            <p class="font-semibold text-gray-900">${{ parseFloat(form.principal_amount || 0).toFixed(2) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600">Interest</p>
                                            <p class="font-semibold text-gray-900">${{ parseFloat(form.interest_amount || 0).toFixed(2) }}</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-600">Remaining After</p>
                                            <p class="font-semibold text-gray-900" v-if="selectedLoan">
                                                ${{ (selectedLoan.remaining_balance - parseFloat(form.principal_amount || 0)).toFixed(2) }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-3 text-xs text-gray-500" v-if="paymentType === 'scheduled' && selectedScheduledPayment">
                                        <p>Scheduled Payment: ${{ selectedScheduledPayment.amount.toLocaleString() }}</p>
                                        <p v-if="form.amount < selectedScheduledPayment.amount" class="text-yellow-600">
                                            This is a partial payment. Remaining: ${{ (selectedScheduledPayment.amount - parseFloat(form.amount || 0)).toFixed(2) }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                                <SecondaryButton 
                                    type="button"
                                    @click="$inertia.visit(route('payments.index'))"
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
                                    <span>{{ form.processing ? 'Processing...' : 'Record Payment' }}</span>
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
import { useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    loans: Array,
    errors: Object
});

const selectedLoan = ref(null);
const selectedScheduledPaymentId = ref(null);
const paymentType = ref('regular');

const form = useForm({
    loan_id: '',
    amount: '',
    principal_amount: '',
    interest_amount: '',
    payment_date: new Date().toISOString().split('T')[0],
    due_date: '',
    status: 'completed',
    payment_method: '',
    reference_number: '',
    notes: '',
    scheduled_payment_id: null
});

// Computed property to get the selected scheduled payment
const selectedScheduledPayment = computed(() => {
    if (!selectedLoan.value || !selectedScheduledPaymentId.value) return null;
    return selectedLoan.value.scheduled_payments.find(p => p.id == selectedScheduledPaymentId.value);
});

const onLoanChange = () => {
    selectedLoan.value = props.loans.find(loan => loan.id == form.loan_id);
    selectedScheduledPaymentId.value = null;
    
    if (selectedLoan.value) {
        // Auto-select the first scheduled payment if available
        if (selectedLoan.value.scheduled_payments.length > 0) {
            paymentType.value = 'scheduled';
            selectedScheduledPaymentId.value = selectedLoan.value.scheduled_payments[0].id;
            onScheduledPaymentChange();
        } else {
            paymentType.value = 'regular';
        }
        
        if (!form.due_date) {
            const dueDate = new Date();
            dueDate.setDate(dueDate.getDate() + 30);
            form.due_date = dueDate.toISOString().split('T')[0];
        }
    }
};

const onScheduledPaymentChange = () => {
    if (selectedScheduledPayment.value) {
        form.amount = selectedScheduledPayment.value.amount;
        form.due_date = selectedScheduledPayment.value.due_date;
        form.principal_amount = selectedScheduledPayment.value.principal_amount;
        form.interest_amount = selectedScheduledPayment.value.interest_amount;
        form.scheduled_payment_id = selectedScheduledPayment.value.id;
    }
};

const getMaxAmount = () => {
    if (paymentType.value === 'scheduled' && selectedScheduledPayment.value) {
        return selectedScheduledPayment.value.amount;
    }
    return null;
};

const calculateAutoSplit = () => {
    if (!selectedLoan.value || !form.amount) return;
    
    const amount = parseFloat(form.amount);
    const remainingBalance = parseFloat(selectedLoan.value.remaining_balance);
    
    if (amount <= remainingBalance) {
        form.principal_amount = amount.toFixed(2);
        form.interest_amount = '0.00';
    } else {
        form.principal_amount = remainingBalance.toFixed(2);
        form.interest_amount = (amount - remainingBalance).toFixed(2);
    }
};

const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const formatStatus = (status) => {
    return status.replace(/_/g, ' ');
};

const statusBadgeClass = (status) => {
    const classes = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'completed': 'bg-green-100 text-green-800',
        'late': 'bg-red-100 text-red-800',
        'partial': 'bg-blue-100 text-blue-800',
        'active': 'bg-blue-100 text-blue-800',
        'disbursed': 'bg-purple-100 text-purple-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const submit = () => {
    form.post(route('payments.store'));
};

// Watch payment type changes
watch(paymentType, (newType) => {
    if (newType === 'regular') {
        selectedScheduledPaymentId.value = null;
        form.scheduled_payment_id = null;
        form.amount = '';
        form.principal_amount = '';
        form.interest_amount = '';
    }
});
</script>