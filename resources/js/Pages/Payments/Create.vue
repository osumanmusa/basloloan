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

                        <!-- No Loans Available Message -->
                        <div v-if="loans.length === 0" class="mb-6 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                                <div>
                                    <p class="font-semibold">No loans available for payment</p>
                                    <p class="text-sm">There are no active loans with pending scheduled payments.</p>
                                    <p class="text-sm mt-1">All scheduled payments may have been completed or there are no disbursed loans.</p>
                                </div>
                            </div>
                            <div class="mt-4">
                                <PrimaryButton @click="$inertia.visit(route('payments.index'))">
                                    View All Payments
                                </PrimaryButton>
                            </div>
                        </div>

                        <form @submit.prevent="submit" v-if="loans.length > 0">
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
                                            - {{ loan.payment_schedules.length }} pending payment(s)
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
                                    <div class="mt-4" v-if="selectedLoan.payment_schedules && selectedLoan.payment_schedules.length > 0">
                                        <h4 class="text-md font-medium text-gray-900 mb-2">Pending Scheduled Payments</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                                            <div v-for="schedule in selectedLoan.payment_schedules" :key="schedule.id" 
                                                 class="bg-white p-2 rounded border"
                                                 :class="{'border-red-300 bg-red-50': schedule.status === 'overdue'}">
                                                <div class="flex justify-between items-center">
                                                    <span class="font-medium">Due: {{ formatDate(schedule.due_date) }}</span>
                                                    <span class="text-green-600 font-semibold">${{ schedule.due_amount.toLocaleString() }}</span>
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    Installment #{{ schedule.installment_number }} | 
                                                    Principal: ${{ schedule.principal_amount.toLocaleString() }} | 
                                                    Interest: ${{ schedule.interest_amount.toLocaleString() }}
                                                </div>
                                                <div class="text-xs mt-1" :class="schedule.status === 'overdue' ? 'text-red-500 font-semibold' : 'text-yellow-500'">
                                                    Status: {{ formatStatus(schedule.status) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Type Selection (Only show if scheduled payments exist) -->
                                <div class="md:col-span-2" v-if="selectedLoan && selectedLoan.payment_schedules && selectedLoan.payment_schedules.length > 0">
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
                                <div class="md:col-span-2" v-if="paymentType === 'scheduled' && selectedLoan && selectedLoan.payment_schedules && selectedLoan.payment_schedules.length > 0">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Select Scheduled Payment to Pay *
                                    </label>
                                    <select 
                                        v-model="selectedScheduleId"
                                        @change="onScheduleChange"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-300': form.errors.loan_payment_schedule_id }"
                                        required
                                    >
                                        <option value="">Select a scheduled payment</option>
                                        <option 
                                            v-for="schedule in selectedLoan.payment_schedules" 
                                            :key="schedule.id" 
                                            :value="schedule.id"
                                        >
                                            Installment #{{ schedule.installment_number }} - 
                                            Due: {{ formatDate(schedule.due_date) }} - 
                                            Amount: ${{ schedule.due_amount.toLocaleString() }}
                                            (Status: {{ formatStatus(schedule.status) }})
                                        </option>
                                    </select>
                                    <p v-if="form.errors.loan_payment_schedule_id" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.loan_payment_schedule_id }}
                                    </p>
                                </div>

                                <!-- Rest of the form fields remain the same -->
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
                                    <p class="text-xs text-gray-500 mt-1" v-if="selectedSchedule">
                                        Scheduled amount: ${{ selectedSchedule.due_amount.toLocaleString() }}
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

                                <!-- Principal Amount, Interest Amount, Payment Date, Payment Method, Reference Number, Notes -->
                                <!-- ... (keep the same as before) ... -->

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
                                    <div class="mt-3 text-xs text-gray-500" v-if="paymentType === 'scheduled' && selectedSchedule">
                                        <p>Scheduled Payment: ${{ selectedSchedule.due_amount.toLocaleString() }}</p>
                                        <p v-if="form.amount < selectedSchedule.due_amount" class="text-yellow-600">
                                            This is a partial payment. Remaining: ${{ (selectedSchedule.due_amount - parseFloat(form.amount || 0)).toFixed(2) }}
                                        </p>
                                        <p v-else-if="form.amount >= selectedSchedule.due_amount" class="text-green-600">
                                            This payment will mark the scheduled installment as completed.
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
import { useForm, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    loans: Array,
    errors: Object
});

const selectedLoan = ref(null);
const selectedScheduleId = ref(null);
const paymentType = ref('regular');

const form = useForm({
    loan_id: '',
    loan_payment_schedule_id: null,
    amount: '',
    principal_amount: '',
    interest_amount: '',
    payment_date: new Date().toISOString().split('T')[0],
    payment_method: '',
    payment_type: 'adhoc',
    reference_number: '',
    notes: '',
    status: 'completed'
});

// Computed property to get the selected schedule
const selectedSchedule = computed(() => {
    if (!selectedLoan.value || !selectedScheduleId.value || !selectedLoan.value.payment_schedules) return null;
    return selectedLoan.value.payment_schedules.find(s => s.id == selectedScheduleId.value);
});

const onLoanChange = () => {
    selectedLoan.value = props.loans.find(loan => loan.id == form.loan_id);
    selectedScheduleId.value = null;
    
    if (selectedLoan.value) {
        // Reset form values first
        form.amount = '';
        form.principal_amount = '';
        form.interest_amount = '';
        form.loan_payment_schedule_id = null;
        
        // Auto-select the first scheduled payment if available
        if (selectedLoan.value.payment_schedules && selectedLoan.value.payment_schedules.length > 0) {
            paymentType.value = 'scheduled';
            selectedScheduleId.value = selectedLoan.value.payment_schedules[0].id;
            onScheduleChange();
        } else {
            paymentType.value = 'regular';
            form.payment_type = 'adhoc';
        }
    }
};

const onScheduleChange = () => {
    if (selectedSchedule.value) {
        // Auto-fill the payment amount
        form.amount = selectedSchedule.value.due_amount;
        
        // Auto-fill principal and interest amounts
        form.principal_amount = selectedSchedule.value.principal_amount;
        form.interest_amount = selectedSchedule.value.interest_amount;
        
        // Set the scheduled payment ID for reference
        form.loan_payment_schedule_id = selectedSchedule.value.id;
        
        // Set payment type to scheduled
        form.payment_type = 'scheduled';
        
        // Auto-set status to completed for scheduled payments
        form.status = 'completed';
    }
};

const getMaxAmount = () => {
    if (paymentType.value === 'scheduled' && selectedSchedule.value) {
        return selectedSchedule.value.due_amount;
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
        'overdue': 'bg-red-100 text-red-800',
        'paid': 'bg-green-100 text-green-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const submit = () => {
    form.post(route('payments.store'), {
        onSuccess: () => {
            // This will automatically redirect to payments.index as configured in the controller
        },
        onError: (errors) => {
            // Errors will be displayed automatically via form.errors
        }
    });
};

// Watch payment type changes to reset appropriately
watch(paymentType, (newType) => {
    if (newType === 'regular') {
        selectedScheduleId.value = null;
        form.loan_payment_schedule_id = null;
        form.payment_type = 'adhoc';
        form.amount = '';
        form.principal_amount = '';
        form.interest_amount = '';
    } else if (newType === 'scheduled' && selectedSchedule.value) {
        // If switching to scheduled and we have a selected payment, auto-fill
        onScheduleChange();
    }
});
</script>