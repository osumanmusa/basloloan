<template>
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Payment Details #{{ payment.id }}
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Payment information and details</p>
                </div>
                <div class="flex space-x-2">
                    <SecondaryButton @click="$inertia.visit(route('payments.index'))">
                        Back to Payments
                    </SecondaryButton>
                    <PrimaryButton 
                        v-if="payment.status === 'pending'"
                        @click="$inertia.visit(route('payments.edit', payment.id))"
                        class="flex items-center space-x-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        <span>Edit Payment</span>
                    </PrimaryButton>
                    <button 
                        v-if="payment.status === 'pending'"
                        @click="deletePayment"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete Payment
                    </button>
                </div>
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
                    <!-- Payment Details -->
                    <div class="lg:col-span-2">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Information</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Basic Payment Info -->
                                    <div class="space-y-4">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Payment ID</label>
                                            <p class="text-lg font-semibold text-gray-900">#{{ payment.id }}</p>
                                        </div>
                                        
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Payment Amount</label>
                                            <p class="text-2xl font-bold text-green-600">${{ payment.amount.toLocaleString() }}</p>
                                        </div>
                                        
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Payment Date</label>
                                            <p class="text-lg font-semibold text-gray-900">{{ formatDate(payment.payment_date) }}</p>
                                        </div>
                                        
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Payment Method</label>
                                            <p class="text-sm font-semibold text-gray-900 capitalize">{{ payment.payment_method?.replace('_', ' ') }}</p>
                                        </div>
                                    </div>

                                    <!-- Status & Additional Info -->
                                    <div class="space-y-4">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Status</label>
                                            <span :class="statusBadgeClass(payment.status)" class="px-3 py-1 text-sm font-medium rounded-full">
                                                {{ formatStatus(payment.status) }}
                                            </span>
                                        </div>
                                        
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Payment Type</label>
                                            <p class="text-sm font-semibold text-gray-900 capitalize">{{ payment.payment_type?.replace('_', ' ') }}</p>
                                        </div>
                                        
                                        <div v-if="payment.reference_number">
                                            <label class="text-sm font-medium text-gray-700">Reference Number</label>
                                            <p class="text-sm font-semibold text-gray-900">{{ payment.reference_number }}</p>
                                        </div>
                                        
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Received By</label>
                                            <p class="text-sm text-gray-900">{{ payment.receiver?.name || 'System' }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Amount Breakdown -->
                                <div class="mt-6 border-t pt-6">
                                    <h4 class="text-md font-medium text-gray-900 mb-3">Amount Breakdown</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                                            <p class="text-sm text-gray-600">Principal Amount</p>
                                            <p class="text-lg font-semibold text-gray-900">${{ payment.principal_amount.toLocaleString() }}</p>
                                        </div>
                                        <div class="text-center p-4 bg-green-50 rounded-lg">
                                            <p class="text-sm text-gray-600">Interest Amount</p>
                                            <p class="text-lg font-semibold text-gray-900">${{ payment.interest_amount.toLocaleString() }}</p>
                                        </div>
                                        <div class="text-center p-4 bg-purple-50 rounded-lg">
                                            <p class="text-sm text-gray-600">Total Amount</p>
                                            <p class="text-lg font-semibold text-green-600">${{ payment.amount.toLocaleString() }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Notes -->
                                <div class="mt-6" v-if="payment.notes">
                                    <label class="text-sm font-medium text-gray-700">Notes</label>
                                    <p class="text-sm text-gray-900 mt-1 bg-gray-50 p-3 rounded">{{ payment.notes }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Actions</h3>
                                <div class="flex flex-wrap gap-3">
                                    <PrimaryButton 
                                        @click="$inertia.visit(route('payments.receipt', payment.id))"
                                        class="flex items-center space-x-2"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span>View Receipt</span>
                                    </PrimaryButton>
                                    
                                    <button 
                                        v-if="payment.status === 'pending'"
                                        @click="markAsCompleted"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Mark as Completed
                                    </button>

                                    <button 
                                        v-if="payment.status === 'completed'"
                                        @click="exportPDF"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        Export PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Loan & Schedule Information -->
                    <div class="space-y-6">
                        <!-- Loan Information -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Loan Information</h3>
                                
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Loan ID:</span>
                                        <span class="font-semibold">#{{ payment.loan.id }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Customer:</span>
                                        <span class="font-semibold">{{ payment.loan.customer.first_name }} {{ payment.loan.customer.last_name }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Original Amount:</span>
                                        <span class="font-semibold">${{ payment.loan.amount.toLocaleString() }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Remaining Balance:</span>
                                        <span class="font-semibold">${{ payment.loan.remaining_balance.toLocaleString() }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Status:</span>
                                        <span :class="statusBadgeClass(payment.loan.status)" class="px-2 py-1 text-xs font-medium rounded-full">
                                            {{ formatStatus(payment.loan.status) }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <Link 
                                        :href="route('loans.show', payment.loan.id)"
                                        class="text-sm text-blue-600 hover:text-blue-900"
                                    >
                                        View Loan Details →
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- Schedule Information -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" v-if="payment.schedule">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Schedule Information</h3>
                                
                                <div class="space-y-3">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Installment:</span>
                                        <span class="font-semibold">#{{ payment.schedule.installment_number }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Due Date:</span>
                                        <span class="font-semibold">{{ formatDate(payment.schedule.due_date) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Scheduled Amount:</span>
                                        <span class="font-semibold">${{ payment.schedule.due_amount.toLocaleString() }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Status:</span>
                                        <span :class="statusBadgeClass(payment.schedule.status)" class="px-2 py-1 text-xs font-medium rounded-full">
                                            {{ formatStatus(payment.schedule.status) }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <Link 
                                        :href="route('payment-schedules.show', payment.schedule.id)"
                                        class="text-sm text-blue-600 hover:text-blue-900"
                                    >
                                        View Schedule Details →
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Timeline -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Timeline</h3>
                                
                                <div class="space-y-4">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Payment Recorded</p>
                                            <p class="text-sm text-gray-500">{{ formatDateTime(payment.created_at) }}</p>
                                            <p class="text-xs text-gray-400">by {{ payment.receiver?.name || 'System' }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-start space-x-3" v-if="payment.updated_at !== payment.created_at">
                                        <div class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Payment Updated</p>
                                            <p class="text-sm text-gray-500">{{ formatDateTime(payment.updated_at) }}</p>
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
    payment: Object
});

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
        'cancelled': 'bg-gray-100 text-gray-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const deletePayment = () => {
    if (confirm('Are you sure you want to delete this payment? This action cannot be undone.')) {
        router.delete(route('payments.destroy', props.payment.id));
    }
};

const markAsCompleted = () => {
    if (confirm('Mark this payment as completed?')) {
        router.put(route('payments.complete', props.payment.id));
    }
};

const exportPDF = () => {
    router.visit(route('payments.export.pdf', props.payment.id));
};
</script>