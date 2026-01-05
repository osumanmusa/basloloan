<template>
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Payment Management
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Manage loan payments and collections</p>
                </div>
                <div class="flex space-x-2">
                    <PrimaryButton 
                        @click="$inertia.visit(route('payments.create'))"
                        class="flex items-center space-x-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span>Record Payment</span>
                    </PrimaryButton>
                    <SecondaryButton 
                        @click="$inertia.visit(route('payments.bulk'))"
                        class="flex items-center space-x-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Bulk Payments</span>
                    </SecondaryButton>
                </div>
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

                        <!-- Header with Stats -->
                        <div class="flex justify-between items-center mb-6">
                            <div class="flex items-center space-x-4">
                                <div class="text-sm text-gray-600">
                                    <span class="font-semibold">{{ payments.total }}</span> total payments
                                </div>
                                <div class="text-sm text-green-600" v-if="completedCount > 0">
                                    <span class="font-semibold">{{ completedCount }}</span> completed
                                </div>
                                <div class="text-sm text-yellow-600" v-if="pendingCount > 0">
                                    <span class="font-semibold">{{ pendingCount }}</span> pending
                                </div>
                                <div class="text-sm text-red-600" v-if="lateCount > 0">
                                    <span class="font-semibold">{{ lateCount }}</span> late
                                </div>
                            </div>
                            
                            <!-- Quick Actions for Mobile -->
                            <div class="md:hidden flex space-x-2">
                                <PrimaryButton 
                                    @click="$inertia.visit(route('payments.create'))"
                                    class="flex items-center space-x-1"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    <span>New</span>
                                </PrimaryButton>
                            </div>
                        </div>

                        <!-- Filters -->
                        <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                            <div class="flex flex-wrap gap-4 items-center">
                                <div class="flex items-center space-x-2">
                                    <label class="text-sm font-medium text-gray-700">Filter by:</label>
                                    <select 
                                        v-model="filters.status"
                                        class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                        @change="filterPayments"
                                    >
                                        <option value="">All Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="completed">Completed</option>
                                        <option value="late">Late</option>
                                        <option value="partial">Partial</option>
                                    </select>
                                    
                                    <select 
                                        v-model="filters.payment_method"
                                        class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                        @change="filterPayments"
                                    >
                                        <option value="">All Methods</option>
                                        <option value="cash">Cash</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="check">Check</option>
                                        <option value="mobile_money">Mobile Money</option>
                                        <option value="online">Online</option>
                                    </select>
                                </div>
                                
                                <div class="flex-1 max-w-md">
                                    <input 
                                        type="text"
                                        v-model="filters.search"
                                        placeholder="Search by customer name, loan ID, or reference..."
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                        @input="filterPayments"
                                    >
                                </div>

                                <div class="flex items-center space-x-2">
                                    <label class="text-sm font-medium text-gray-700">Date:</label>
                                    <input 
                                        type="date"
                                        v-model="filters.date_from"
                                        class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                        @change="filterPayments"
                                    >
                                    <span class="text-gray-500">to</span>
                                    <input 
                                        type="date"
                                        v-model="filters.date_to"
                                        class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                        @change="filterPayments"
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

                        <!-- Payments Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Payment Details
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Loan & Customer
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Amount Details
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Dates
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
                                    <tr v-for="payment in payments.data" :key="payment.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                #{{ payment.id }}
                                            </div>
                                            <div class="text-sm text-gray-500 capitalize">
                                                {{ payment.payment_method?.replace('_', ' ') }}
                                            </div>
                                            <div class="text-xs text-gray-400" v-if="payment.reference_number">
                                                Ref: {{ payment.reference_number }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ payment.loan.customer.first_name }} {{ payment.loan.customer.last_name }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                Loan #{{ payment.loan.id }}
                                            </div>
                                            <div class="text-xs text-gray-400">
                                                {{ payment.loan.customer.phone }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                ${{ payment.amount.toLocaleString() }}
                                            </div>
                                            <div class="text-sm text-gray-500" v-if="payment.principal_amount && payment.interest_amount">
                                                Principal: ${{ payment.principal_amount.toLocaleString() }}
                                            </div>
                                            <div class="text-xs text-gray-400" v-if="payment.principal_amount && payment.interest_amount">
                                                Interest: ${{ payment.interest_amount.toLocaleString() }}
                                            </div>
                                        </td>
                                     <!-- In the table body of Index.vue -->
<td class="px-6 py-4">
    <div class="text-sm text-gray-900">
        Due: {{ payment.schedule ? formatDate(payment.schedule.due_date) : 'N/A' }}
    </div>
    <div class="text-sm text-gray-500" v-if="payment.payment_date">
        Paid: {{ formatDate(payment.payment_date) }}
    </div>
    <div class="text-xs text-red-500 font-medium" v-if="isOverdue(payment)">
        Overdue
    </div>
    <div class="text-xs text-yellow-500" v-else-if="isDueSoon(payment)">
        Due Soon
    </div>
    <div class="text-xs text-gray-400" v-if="payment.schedule">
        Installment #{{ payment.schedule.installment_number }}
    </div>
</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="statusBadgeClass(payment.status)" class="px-2 py-1 text-xs font-medium rounded-full">
                                                {{ formatStatus(payment.status) }}
                                            </span>
                                            <div class="text-xs text-gray-500 mt-1 capitalize">
                                                {{ payment.payment_method?.replace('_', ' ') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex flex-wrap gap-1">
                                                <!-- View Button -->
                                                <button 
                                                    @click="$inertia.visit(route('payments.show', payment.id))"
                                                    class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-blue-500 transition-colors duration-200"
                                                    title="View Payment Details"
                                                >
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </button>

                                                <!-- Edit Button -->
                                                <button 
                                                    v-if="payment.status === 'pending'"
                                                    @click="$inertia.visit(route('payments.edit', payment.id))"
                                                    class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-green-500 transition-colors duration-200"
                                                    title="Edit Payment"
                                                >
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                </button>

                                                <!-- Mark as Paid Button -->
                                                <button 
                                                    v-if="payment.status === 'pending'"
                                                    @click="markAsPaid(payment)"
                                                    class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-green-500 transition-colors duration-200"
                                                    title="Mark as Paid"
                                                >
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </button>

                                                <!-- Delete Button -->
                                                <button 
                                                    v-if="payment.status === 'pending'"
                                                    @click="deletePayment(payment)"
                                                    class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-red-500 transition-colors duration-200"
                                                    title="Delete Payment"
                                                >
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="payments.data.length === 0">
                                        <td colspan="6" class="px-6 py-8 text-center">
                                            <div class="text-gray-500">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0 6v1m0-1v1m6-10h2m-10 0h2m-1 14h1"/>
                                                </svg>
                                                <h3 class="mt-2 text-sm font-medium text-gray-900">No payments found</h3>
                                                <p class="mt-1 text-sm text-gray-500">
                                                    {{ hasActiveFilters ? 'Try changing your filters' : 'Get started by recording your first payment' }}
                                                </p>
                                                <div class="mt-6" v-if="!hasActiveFilters">
                                                    <PrimaryButton 
                                                        @click="$inertia.visit(route('payments.create'))"
                                                        class="flex items-center space-x-2 mx-auto"
                                                    >
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                        </svg>
                                                        <span>Record Payment</span>
                                                    </PrimaryButton>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <Pagination :links="payments.links" class="mt-6" />
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
import Pagination from '@/Components/Pagination.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    payments: Object,
    filters: Object
});

const filters = ref({
    status: props.filters?.status || '',
    payment_method: props.filters?.payment_method || '',
    search: props.filters?.search || '',
    date_from: props.filters?.date_from || '',
    date_to: props.filters?.date_to || ''
});

// Computed properties
const pendingCount = computed(() => {
    return props.payments.data.filter(payment => payment.status === 'pending').length;
});

const completedCount = computed(() => {
    return props.payments.data.filter(payment => payment.status === 'completed').length;
});

const lateCount = computed(() => {
    return props.payments.data.filter(payment => payment.status === 'late').length;
});

const hasActiveFilters = computed(() => {
    return filters.value.status || filters.value.payment_method || filters.value.search || filters.value.date_from || filters.value.date_to;
});

const filterPayments = () => {
    router.get(route('payments.index'), filters.value, {
        preserveState: true,
        replace: true,
    });
};

const clearFilters = () => {
    filters.value = { 
        status: '', 
        payment_method: '', 
        search: '', 
        date_from: '', 
        date_to: '' 
    };
    filterPayments();
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
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

// In the script section of Index.vue
const isOverdue = (payment) => {
    if (payment.status === 'completed' || payment.status === 'cancelled') return false;
    
    // For scheduled payments, check the schedule due date
    if (payment.schedule && payment.schedule.due_date) {
        const dueDate = new Date(payment.schedule.due_date);
        const today = new Date();
        return dueDate < today;
    }
    
    return false;
};

const isDueSoon = (payment) => {
    if (payment.status !== 'pending') return false;
    
    // For scheduled payments, check the schedule due date
    if (payment.schedule && payment.schedule.due_date) {
        const dueDate = new Date(payment.schedule.due_date);
        const today = new Date();
        const daysUntilDue = Math.ceil((dueDate - today) / (1000 * 60 * 60 * 24));
        return daysUntilDue <= 7 && daysUntilDue > 0;
    }
    
    return false;
};

const markAsPaid = (payment) => {
    if (confirm('Mark this payment as completed?')) {
        router.put(route('payments.complete', payment.id));
    }
};

const deletePayment = (payment) => {
    if (confirm('Are you sure you want to delete this payment? This action cannot be undone.')) {
        router.delete(route('payments.destroy', payment.id));
    }
};
</script>