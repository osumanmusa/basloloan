<template>
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Payment Schedule #{{ schedule.id }}
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Installment #{{ schedule.installment_number }} Details</p>
                </div>
                <SecondaryButton @click="$inertia.visit(route('payment-schedules.index'))">
                    Back to Schedules
                </SecondaryButton>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Success/Error Messages -->
                <div v-if="$page.props.flash.success" class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ $page.props.flash.success }}
                </div>

                <div v-if="$page.props.flash.error" class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ $page.props.flash.error }}
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Schedule Details -->
                    <div class="lg:col-span-2">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Schedule Information</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-4">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Installment Number</label>
                                            <p class="text-lg font-semibold text-gray-900">#{{ schedule.installment_number }}</p>
                                        </div>
                                        
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Due Amount</label>
                                            <p class="text-2xl font-bold text-green-600">${{ formatCurrency(schedule.due_amount) }}</p>
                                        </div>
                                        
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Due Date</label>
                                            <p class="text-lg font-semibold text-gray-900">{{ formatDate(schedule.due_date) }}</p>
                                        </div>
                                    </div>

                                    <div class="space-y-4">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Status</label>
                                            <span :class="statusBadgeClass(schedule.status)" class="px-3 py-1 text-sm font-medium rounded-full">
                                                {{ formatStatus(schedule.status) }}
                                            </span>
                                        </div>
                                        
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Days Status</label>
                                            <p class="text-sm font-semibold" :class="getDaysStatusClass(schedule)">
                                                {{ getDaysStatusText(schedule) }}
                                            </p>
                                        </div>
                                        
                                        <div v-if="schedule.notes">
                                            <label class="text-sm font-medium text-gray-700">Notes</label>
                                            <p class="text-sm text-gray-900">{{ schedule.notes }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Amount Breakdown -->
                                <div class="mt-6 border-t pt-6">
                                    <h4 class="text-md font-medium text-gray-900 mb-3">Amount Breakdown</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                                            <p class="text-sm text-gray-600">Principal Amount</p>
                                            <p class="text-lg font-semibold text-gray-900">${{ formatCurrency(schedule.principal_amount) }}</p>
                                        </div>
                                        <div class="text-center p-4 bg-green-50 rounded-lg">
                                            <p class="text-sm text-gray-600">Interest Amount</p>
                                            <p class="text-lg font-semibold text-gray-900">${{ formatCurrency(schedule.interest_amount) }}</p>
                                        </div>
                                        <div class="text-center p-4 bg-purple-50 rounded-lg">
                                            <p class="text-sm text-gray-600">Total Due</p>
                                            <p class="text-lg font-semibold text-green-600">${{ formatCurrency(schedule.due_amount) }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Progress -->
                                <div class="mt-6 border-t pt-6" v-if="getPaidAmount(schedule) > 0">
                                    <h4 class="text-md font-medium text-gray-900 mb-3">Payment Progress</h4>
                                    <div class="space-y-2">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Paid Amount</span>
                                            <span class="font-semibold text-green-600">${{ formatCurrency(getPaidAmount(schedule)) }}</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div 
                                                class="bg-green-600 h-2 rounded-full" 
                                                :style="{ width: getPaymentProgress(schedule) + '%' }"
                                            ></div>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">Remaining</span>
                                            <span class="font-semibold text-gray-900">${{ formatCurrency(getRemainingAmount(schedule)) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Schedule Actions</h3>
                                <div class="flex flex-wrap gap-3">
                                    <PrimaryButton 
                                        v-if="schedule.status === 'pending' || schedule.status === 'overdue'"
                                        @click="recordPayment"
                                        class="flex items-center space-x-2"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        <span>Record Payment</span>
                                    </PrimaryButton>
                                    
                                    <button 
                                        v-if="schedule.status === 'pending'"
                                        @click="editSchedule"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors duration-200"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit Schedule
                                    </button>

                                    <button 
                                        v-if="schedule.status === 'pending' && getPaymentsCount(schedule) === 0"
                                        @click="deleteSchedule"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Delete Schedule
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Payment History -->
                        <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg" v-if="schedule.payments && schedule.payments.length > 0">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Payment History</h3>
                                <div class="space-y-3">
                                    <div 
                                        v-for="payment in schedule.payments" 
                                        :key="payment.id"
                                        class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50"
                                    >
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    ${{ formatCurrency(payment.amount) }}
                                                </p>
                                                <p class="text-xs text-gray-500 capitalize">
                                                    {{ payment.payment_method }} • {{ payment.status }}
                                                </p>
                                                <p class="text-xs text-gray-400">
                                                    {{ formatDateTime(payment.payment_date) }}
                                                </p>
                                            </div>
                                            <span :class="statusBadgeClass(payment.status)" class="text-xs font-medium px-2 py-1 rounded-full">
                                                {{ payment.status }}
                                            </span>
                                        </div>
                                        <div class="text-xs text-gray-400 mt-2" v-if="payment.notes">
                                            {{ payment.notes }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Loan Information -->
                    <div class="space-y-6" v-if="hasLoanData(schedule)">
                        <!-- Loan Information -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Loan Information</h3>
                                
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Loan ID:</span>
                                        <span class="font-semibold">#{{ getLoanId(schedule) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Customer:</span>
                                        <span class="font-semibold">{{ getCustomerName(schedule) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Original Amount:</span>
                                        <span class="font-semibold">${{ formatCurrency(getLoanAmount(schedule)) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Remaining Balance:</span>
                                        <span class="font-semibold">${{ formatCurrency(getLoanRemainingBalance(schedule)) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Status:</span>
                                        <span :class="statusBadgeClass(getLoanStatus(schedule))" class="px-2 py-1 text-xs font-medium rounded-full">
                                            {{ formatStatus(getLoanStatus(schedule)) }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="mt-4" v-if="getLoanId(schedule)">
                                    <Link 
                                        :href="route('loans.show', getLoanId(schedule))"
                                        class="text-sm text-blue-600 hover:text-blue-900"
                                    >
                                        View Loan Details →
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- Schedule Timeline -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Schedule Timeline</h3>
                                
                                <div class="space-y-4">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Schedule Created</p>
                                            <p class="text-sm text-gray-500">{{ formatDateTime(schedule.created_at) }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start space-x-3" v-if="schedule.updated_at !== schedule.created_at">
                                        <div class="flex-shrink-0 w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Schedule Updated</p>
                                            <p class="text-sm text-gray-500">{{ formatDateTime(schedule.updated_at) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- No Loan Data Message -->
                    <div class="space-y-6" v-else>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">Loan Information Unavailable</h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>The associated loan data for this payment schedule could not be loaded.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Schedule Timeline -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Schedule Timeline</h3>
                                
                                <div class="space-y-4">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Schedule Created</p>
                                            <p class="text-sm text-gray-500">{{ formatDateTime(schedule.created_at) }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start space-x-3" v-if="schedule.updated_at !== schedule.created_at">
                                        <div class="flex-shrink-0 w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Schedule Updated</p>
                                            <p class="text-sm text-gray-500">{{ formatDateTime(schedule.updated_at) }}</p>
                                        </div>
                                    </div>
                                </div>
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
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
    schedule: Object
});

// Safe loan data check
const hasLoanData = (schedule) => {
    return schedule.loan !== null && schedule.loan !== undefined;
};

// Safe loan data getters
const getLoanId = (schedule) => {
    return hasLoanData(schedule) ? schedule.loan.id : null;
};

const getCustomerName = (schedule) => {
    if (!hasLoanData(schedule) || !schedule.loan.customer) return 'Unknown Customer';
    return `${schedule.loan.customer.first_name || ''} ${schedule.loan.customer.last_name || ''}`.trim() || 'Unknown Customer';
};

const getLoanAmount = (schedule) => {
    return hasLoanData(schedule) ? schedule.loan.amount : 0;
};

const getLoanRemainingBalance = (schedule) => {
    return hasLoanData(schedule) ? schedule.loan.remaining_balance : 0;
};

const getLoanStatus = (schedule) => {
    return hasLoanData(schedule) ? schedule.loan.status : 'unknown';
};

// Safe currency formatting function
const formatCurrency = (value) => {
    if (value === null || value === undefined) return '0.00';
    return parseFloat(value).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
};

const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const formatDateTime = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

const formatStatus = (status) => {
    if (!status) return 'Unknown';
    return status.replace(/_/g, ' ');
};

const statusBadgeClass = (status) => {
    const classes = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'paid': 'bg-green-100 text-green-800',
        'overdue': 'bg-red-100 text-red-800',
        'cancelled': 'bg-gray-100 text-gray-800',
        'unknown': 'bg-gray-100 text-gray-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const getDaysStatusText = (schedule) => {
    if (!schedule.status) return 'Unknown';
    if (schedule.status === 'paid') return 'Fully Paid';
    if (schedule.status === 'cancelled') return 'Cancelled';
    
    if (!schedule.due_date) return 'No due date';
    
    const dueDate = new Date(schedule.due_date);
    const today = new Date();
    const diffTime = dueDate - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays < 0) return `${Math.abs(diffDays)} days overdue`;
    if (diffDays === 0) return 'Due today';
    if (diffDays === 1) return 'Due tomorrow';
    return `Due in ${diffDays} days`;
};

const getDaysStatusClass = (schedule) => {
    if (!schedule.status) return 'text-gray-600';
    if (schedule.status === 'paid') return 'text-green-600';
    if (schedule.status === 'cancelled') return 'text-gray-600';
    
    if (!schedule.due_date) return 'text-gray-600';
    
    const dueDate = new Date(schedule.due_date);
    const today = new Date();
    const diffTime = dueDate - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays < 0) return 'text-red-600';
    if (diffDays <= 3) return 'text-yellow-600';
    return 'text-green-600';
};

// Safe getter functions for computed properties
const getPaidAmount = (schedule) => {
    if (!schedule.paid_amount) return 0;
    return parseFloat(schedule.paid_amount);
};

const getRemainingAmount = (schedule) => {
    if (!schedule.remaining_amount) return parseFloat(schedule.due_amount || 0);
    return parseFloat(schedule.remaining_amount);
};

const getPaymentProgress = (schedule) => {
    const paid = getPaidAmount(schedule);
    const due = parseFloat(schedule.due_amount || 0);
    if (due === 0) return 0;
    return Math.min((paid / due) * 100, 100);
};

const getPaymentsCount = (schedule) => {
    if (!schedule.payments_count && (!schedule.payments || schedule.payments.length === 0)) return 0;
    return schedule.payments_count || schedule.payments.length;
};

const recordPayment = () => {
    router.visit(route('payments.create'), {
        data: { 
            loan_id: props.schedule.loan_id,
            loan_payment_schedule_id: props.schedule.id
        }
    });
};

const editSchedule = () => {
    // Implement edit functionality
    console.log('Edit schedule:', props.schedule);
};

const deleteSchedule = () => {
    if (confirm(`Are you sure you want to delete this payment schedule? This action cannot be undone.`)) {
        router.delete(route('payment-schedules.destroy', props.schedule.id));
    }
};
</script>