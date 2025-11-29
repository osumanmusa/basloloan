<template>
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Loan #{{ loan.id }}
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Loan Details</p>
                </div>
                <div class="flex space-x-2">
                    <SecondaryButton @click="$inertia.visit(route('loans.index'))">
                        Back to Loans
                    </SecondaryButton>
                    <PrimaryButton 
                        v-if="$page.props.auth.can['edit loans'] && loan.status === 'pending'"
                        @click="$inertia.visit(route('loans.edit', loan.id))"
                    >
                        Edit Loan
                    </PrimaryButton>
                    <button 
                        v-if="$page.props.auth.can['delete loans'] && loan.status === 'pending'"
                        @click="deleteLoan"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete Loan
                    </button>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Success Message -->
                <div v-if="$page.props.flash.success" class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ $page.props.flash.success }}
                </div>

                <!-- Error Message -->
                <div v-if="$page.props.flash.error" class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ $page.props.flash.error }}
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Loan Details -->
                    <div class="lg:col-span-2">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Loan Information</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Basic Loan Info -->
                                    <div class="space-y-4">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Loan Amount</label>
                                            <p class="text-lg font-semibold text-gray-900">${{ loan.amount.toLocaleString() }}</p>
                                        </div>
                                        
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Interest Rate</label>
                                            <p class="text-lg font-semibold text-gray-900">{{ loan.interest_rate }}%</p>
                                        </div>
                                        
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Loan Term</label>
                                            <p class="text-lg font-semibold text-gray-900">{{ loan.term_months }} months</p>
                                        </div>
                                        
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Monthly Payment</label>
                                            <p class="text-lg font-semibold text-gray-900" v-if="loan.monthly_payment">
                                                ${{ loan.monthly_payment.toLocaleString() }}
                                            </p>
                                            <p class="text-sm text-gray-500" v-else>Not calculated</p>
                                        </div>
                                    </div>

                                    <!-- Status & Dates -->
                                    <div class="space-y-4">
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Status</label>
                                            <span :class="statusBadgeClass(loan.status)" class="px-3 py-1 text-sm font-medium rounded-full">
                                                {{ formatStatus(loan.status) }}
                                            </span>
                                        </div>
                                        
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Loan Type</label>
                                            <p class="text-sm font-semibold text-gray-900 capitalize">{{ loan.type }}</p>
                                        </div>
                                        
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Purpose</label>
                                            <p class="text-sm text-gray-900 capitalize">{{ loan.purpose?.replace('_', ' ') }}</p>
                                        </div>
                                        
                                        <div>
                                            <label class="text-sm font-medium text-gray-700">Created Date</label>
                                            <p class="text-sm text-gray-900">{{ formatDate(loan.created_at) }}</p>
                                        </div>
                                        
                                        <div v-if="loan.approved_at">
                                            <label class="text-sm font-medium text-gray-700">Approved Date</label>
                                            <p class="text-sm text-gray-900">{{ formatDate(loan.approved_at) }}</p>
                                        </div>
                                        
                                        <div v-if="loan.disbursement_date">
                                            <label class="text-sm font-medium text-gray-700">Disbursement Date</label>
                                            <p class="text-sm text-gray-900">{{ formatDate(loan.disbursement_date) }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Purpose Description -->
                                <div class="mt-6" v-if="loan.purpose_description">
                                    <label class="text-sm font-medium text-gray-700">Purpose Description</label>
                                    <p class="text-sm text-gray-900 mt-1">{{ loan.purpose_description }}</p>
                                </div>

                                <!-- Admin Notes -->
                                <div class="mt-6" v-if="loan.admin_notes">
                                    <label class="text-sm font-medium text-gray-700">Admin Notes</label>
                                    <p class="text-sm text-gray-900 mt-1">{{ loan.admin_notes }}</p>
                                </div>

                                <!-- Rejection Reason -->
                                <div class="mt-6" v-if="loan.rejection_reason">
                                    <label class="text-sm font-medium text-gray-700 text-red-600">Rejection Reason</label>
                                    <p class="text-sm text-red-600 mt-1">{{ loan.rejection_reason }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Loan Actions</h3>
                               <!-- In the action buttons section -->
<div class="flex flex-wrap gap-3">
    <!-- Edit Button - Only for pending loans -->
    <PrimaryButton 
        v-if="loan.status === 'pending'"
        @click="$inertia.visit(route('loans.edit', loan.id))"
    >
        Edit Loan
    </PrimaryButton>

    <!-- Approve Button - Only for pending loans -->
    <button 
        v-if="loan.status === 'pending'"
        @click="approveLoan"
        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200"
    >
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        Approve Loan
    </button>

    <!-- Reject Button - Only for pending loans -->
    <button 
        v-if="loan.status === 'pending'"
        @click="rejectLoan"
        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
    >
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        Reject Loan
    </button>

    <!-- Disburse Button - Only for approved loans -->
    <button 
        v-if="loan.status === 'approved'"
        @click="disburseLoan"
        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200"
    >
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0 6v1m0-1v1m6-10h2m-10 0h2m-1 14h1"/>
        </svg>
        Disburse Loan
    </button>

    <!-- Delete Button - Only for pending loans -->
    <button 
        v-if="loan.status === 'pending'"
        @click="deleteLoan"
        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200"
    >
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
        </svg>
        Delete Loan
    </button>
</div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Information & Summary -->
                    <div class="space-y-6">
                        <!-- Customer Information -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Customer Information</h3>
                                
                                <div class="flex items-center mb-4">
                                    <div class="flex-shrink-0 h-12 w-12 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 font-semibold text-sm">
                                            {{ getInitials(loan.customer.first_name + ' ' + loan.customer.last_name) }}
                                        </span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ loan.customer.first_name }} {{ loan.customer.last_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ loan.customer.email }}</div>
                                    </div>
                                </div>
                                
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Phone:</span>
                                        <span class="text-gray-900">{{ loan.customer.phone }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">ID Number:</span>
                                        <span class="text-gray-900">{{ loan.customer.id_number }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Employment:</span>
                                        <span class="text-gray-900 capitalize">{{ loan.customer.employment_status?.replace('_', ' ') }}</span>
                                    </div>
                                    <div class="flex justify-between" v-if="loan.customer.monthly_income">
                                        <span class="text-gray-600">Monthly Income:</span>
                                        <span class="text-gray-900">${{ loan.customer.monthly_income.toLocaleString() }}</span>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <Link 
                                        :href="route('customers.show', loan.customer.id)"
                                        class="text-sm text-blue-600 hover:text-blue-900"
                                    >
                                        View Customer Profile →
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- Loan Summary -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Loan Summary</h3>
                                
                                <div class="space-y-3 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Principal Amount:</span>
                                        <span class="text-gray-900">${{ loan.amount.toLocaleString() }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Total Interest:</span>
                                        <span class="text-gray-900" v-if="loan.total_amount">
                                            ${{ (loan.total_amount - loan.amount).toLocaleString() }}
                                        </span>
                                        <span class="text-gray-500 text-xs" v-else>Not calculated</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Total Payment:</span>
                                        <span class="text-gray-900" v-if="loan.total_amount">
                                            ${{ loan.total_amount.toLocaleString() }}
                                        </span>
                                        <span class="text-gray-500 text-xs" v-else>Not calculated</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Remaining Balance:</span>
                                        <span class="text-gray-900" v-if="loan.remaining_balance">
                                            ${{ loan.remaining_balance.toLocaleString() }}
                                        </span>
                                        <span class="text-gray-500 text-xs" v-else>Not calculated</span>
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
    loan: Object
});

const formatDate = (date) => {
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
        'under_review': 'bg-blue-100 text-blue-800',
        'approved': 'bg-green-100 text-green-800',
        'rejected': 'bg-red-100 text-red-800',
        'disbursed': 'bg-purple-100 text-purple-800',
        'active': 'bg-blue-100 text-blue-800',
        'completed': 'bg-gray-100 text-gray-800',
        'defaulted': 'bg-red-100 text-red-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const getInitials = (fullName) => {
    if (!fullName) return '';
    return fullName
        .split(' ')
        .map(name => name[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
};

const deleteLoan = () => {
    if (confirm('Are you sure you want to delete this loan? This action cannot be undone.')) {
        router.delete(route('loans.destroy', props.loan.id));
    }
};

const approveLoan = () => {
    if (confirm('Are you sure you want to approve this loan?')) {
        router.put(route('loans.approve', props.loan.id));
    }
};

const rejectLoan = () => {
    if (confirm('Are you sure you want to reject this loan?')) {
        router.put(route('loans.reject', props.loan.id));
    }
};

const disburseLoan = () => {
    if (confirm('Are you sure you want to disburse this loan?')) {
        router.put(route('loans.disburse', props.loan.id));
    }
};
</script>