<template>
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Loan Management
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Manage loan applications and approvals</p>
                </div>
                <PrimaryButton 
                    @click="$inertia.visit(route('loans.create'))"
                    class="flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>Create New Loan</span>
                </PrimaryButton>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Success/Error Messages -->
                        <div v-if="$page.props.flash.success" class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            {{ $page.props.flash.success }}
                        </div>

                        <div v-if="$page.props.flash.error" class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            {{ $page.props.flash.error }}
                        </div>

                        <!-- Header with Stats and Create Button -->
                        <div class="flex justify-between items-center mb-6">
                            <div class="flex items-center space-x-4">
                                <div class="text-sm text-gray-600">
                                    <span class="font-semibold">{{ loans.total }}</span> total loans
                                </div>
                                <div class="text-sm text-yellow-600" v-if="pendingCount > 0">
                                    <span class="font-semibold">{{ pendingCount }}</span> pending approval
                                </div>
                                <div class="text-sm text-green-600" v-if="activeCount > 0">
                                    <span class="font-semibold">{{ activeCount }}</span> active loans
                                </div>
                            </div>
                            
                            <!-- Create Button for smaller screens -->
                            <PrimaryButton 
                                @click="$inertia.visit(route('loans.create'))"
                                class="md:hidden flex items-center space-x-2"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                <span>New Loan</span>
                            </PrimaryButton>
                        </div>

                        <!-- Filters -->
                        <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                            <div class="flex flex-wrap gap-4 items-center">
                                <div class="flex items-center space-x-2">
                                    <label class="text-sm font-medium text-gray-700">Filter by:</label>
                                    <select 
                                        v-model="filters.status"
                                        class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                        @change="filterLoans"
                                    >
                                        <option value="">All Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="under_review">Under Review</option>
                                        <option value="approved">Approved</option>
                                        <option value="rejected">Rejected</option>
                                        <option value="disbursed">Disbursed</option>
                                        <option value="active">Active</option>
                                        <option value="completed">Completed</option>
                                        <option value="defaulted">Defaulted</option>
                                    </select>
                                    
                                    <select 
                                        v-model="filters.type"
                                        class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                        @change="filterLoans"
                                    >
                                        <option value="">All Types</option>
                                        <option value="personal">Personal</option>
                                        <option value="business">Business</option>
                                        <option value="mortgage">Mortgage</option>
                                        <option value="auto">Auto</option>
                                        <option value="education">Education</option>
                                    </select>
                                </div>
                                
                                <div class="flex-1 max-w-md">
                                    <input 
                                        type="text"
                                        v-model="filters.search"
                                        placeholder="Search by customer name, email, or loan ID..."
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                        @input="filterLoans"
                                    >
                                </div>

                                <button 
                                    @click="clearFilters"
                                    class="text-sm text-gray-600 hover:text-gray-800 px-3 py-1 border border-gray-300 rounded-md hover:bg-gray-50"
                                    v-if="hasActiveFilters"
                                >
                                    Clear Filters
                                </button>
                            </div>
                        </div>

                        <!-- Loans Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Loan Details
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Customer
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Amount & Terms
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="loan in loans.data" :key="loan.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                #{{ loan.id }}
                                            </div>
                                            <div class="text-sm text-gray-500 capitalize">
                                                {{ loan.type }} • {{ loan.purpose }}
                                            </div>
                                            <div class="text-xs text-gray-400">
                                                Created {{ formatDate(loan.created_at) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ getCustomerFullName(loan.customer) }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ loan.customer.email }}
                                            </div>
                                            <div class="text-xs text-gray-400">
                                                {{ loan.customer.phone }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                ${{ loan.amount.toLocaleString() }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ loan.interest_rate }}% for {{ loan.term_months }} months
                                            </div>
                                            <div class="text-xs text-gray-400" v-if="loan.monthly_payment">
                                                ${{ loan.monthly_payment.toLocaleString() }}/month
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="statusBadgeClass(loan.status)" class="px-2 py-1 text-xs font-medium rounded-full">
                                                {{ formatStatus(loan.status) }}
                                            </span>
                                            <div class="text-xs text-gray-500 mt-1" v-if="loan.approved_at">
                                                Approved {{ formatDate(loan.approved_at) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                           <!-- In the actions column -->
<div class="flex flex-wrap gap-1">
    <!-- View Button - Always visible -->
    <button 
        @click="$inertia.visit(route('loans.show', loan.id))"
        class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-blue-500 transition-colors duration-200"
        title="View Loan Details"
    >
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
        </svg> <span class="px-1">view</span>
    </button>

    <!-- Edit Button - Only for pending loans -->
    <button 
        v-if="loan.status === 'pending'"
        @click="$inertia.visit(route('loans.edit', loan.id))"
        class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-green-500 transition-colors duration-200"
        title="Edit Loan"
    >
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
        </svg><span class="px-1">edit</span>
    </button>

    <!-- Approve Button - Only for pending loans -->
    <button 
        v-if="loan.status === 'pending'"
        @click="approveLoan(loan)"
        class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-green-500 transition-colors duration-200"
        title="Approve Loan"
    >
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg><span class="px-1">approve</span>
    </button>

    <!-- Reject Button - Only for pending loans -->
    <button 
        v-if="loan.status === 'pending'"
        @click="rejectLoan(loan)"
        class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-red-500 transition-colors duration-200"
        title="Reject Loan"
    >
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg><span class="px-1">reject</span>
    </button>

    <!-- Disburse Button - Only for approved loans -->
    <button 
        v-if="loan.status === 'approved'"
        @click="disburseLoan(loan)"
        class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-purple-500 transition-colors duration-200"
        title="Disburse Loan"
    >
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0 6v1m0-1v1m6-10h2m-10 0h2m-1 14h1"/>
        </svg><span class="px-1">disburse</span>
    </button>

    <!-- Delete Button - Only for pending loans -->
    <button 
        v-if="loan.status === 'pending'"
        @click="deleteLoan(loan)"
        class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-red-500 transition-colors duration-200"
        title="Delete Loan"
    >
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
        </svg><span class="px-1">delete</span>
    </button>
</div>
                                        </td>
                                    </tr>
                                    <tr v-if="loans.data.length === 0">
                                        <td colspan="5" class="px-6 py-8 text-center">
                                            <div class="text-gray-500">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0 6v1m0-1v1m6-10h2m-10 0h2m-1 14h1"/>
                                                </svg>
                                                <h3 class="mt-2 text-sm font-medium text-gray-900">No loans found</h3>
                                                <p class="mt-1 text-sm text-gray-500">
                                                    {{ hasActiveFilters ? 'Try changing your filters' : 'Get started by creating a new loan' }}
                                                </p>
                                                <div class="mt-6" v-if="!hasActiveFilters">
                                                    <PrimaryButton 
                                                        @click="$inertia.visit(route('loans.create'))"
                                                        class="flex items-center space-x-2 mx-auto"
                                                    >
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                        </svg>
                                                        <span>Create New Loan</span>
                                                    </PrimaryButton>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <Pagination :links="loans.links" class="mt-6" />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Link, router } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    loans: Object,
    filters: Object
});

const filters = ref({
    status: props.filters?.status || '',
    type: props.filters?.type || '',
    search: props.filters?.search || ''
});

// Computed properties
const pendingCount = computed(() => {
    return props.loans.data.filter(loan => loan.status === 'pending').length;
});

const activeCount = computed(() => {
    return props.loans.data.filter(loan => ['active', 'disbursed'].includes(loan.status)).length;
});

const hasActiveFilters = computed(() => {
    return filters.value.status || filters.value.type || filters.value.search;
});

const filterLoans = () => {
    router.get(route('loans.index'), filters.value, {
        preserveState: true,
        replace: true,
    });
};

const clearFilters = () => {
    filters.value = { status: '', type: '', search: '' };
    filterLoans();
};

const getCustomerFullName = (customer) => {
    if (customer.full_name) {
        return customer.full_name;
    }
    return `${customer.first_name} ${customer.last_name}`;
};

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

const approveLoan = (loan) => {
    if (confirm('Are you sure you want to approve this loan?')) {
        router.put(route('loans.approve', loan.id));
    }
};

const rejectLoan = (loan) => {
    if (confirm('Are you sure you want to reject this loan?')) {
        router.put(route('loans.reject', loan.id));
    }
};

const disburseLoan = (loan) => {
    if (confirm('Are you sure you want to disburse this loan?')) {
        router.put(route('loans.disburse', loan.id));
    }
};

const deleteLoan = (loan) => {
    if (confirm('Are you sure you want to delete this loan? This action cannot be undone.')) {
        router.delete(route('loans.destroy', loan.id));
    }
};
</script>