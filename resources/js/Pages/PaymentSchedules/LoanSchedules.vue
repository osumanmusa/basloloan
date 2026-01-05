<template>
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Payment Schedules for Loan #{{ loan.id }}
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ loan.customer.first_name }} {{ loan.customer.last_name }} - 
                        ${{ formatCurrency(loan.amount) }} • {{ loan.term_months }} months
                    </p>
                </div>
                <div class="flex space-x-2">
                    <SecondaryButton @click="$inertia.visit(route('payment-schedules.index'))">
                        Back to Loans
                    </SecondaryButton>
                    <PrimaryButton 
                        @click="$inertia.visit(route('loans.show', loan.id))"
                        class="flex items-center space-x-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <span>View Loan</span>
                    </PrimaryButton>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Loan Stats -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                <div class="text-center">
                                    <p class="text-sm font-medium text-gray-600">Total Schedules</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ loanStats.total_schedules || 0 }}</p>
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                <div class="text-center">
                                    <p class="text-sm font-medium text-gray-600">Pending</p>
                                    <p class="text-2xl font-bold text-yellow-600">{{ loanStats.pending_schedules || 0 }}</p>
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                <div class="text-center">
                                    <p class="text-sm font-medium text-gray-600">Paid</p>
                                    <p class="text-2xl font-bold text-green-600">{{ loanStats.paid_schedules || 0 }}</p>
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                <div class="text-center">
                                    <p class="text-sm font-medium text-gray-600">Total Due</p>
                                    <p class="text-2xl font-bold text-blue-600">${{ formatCurrency(loanStats.total_due_amount) }}</p>
                                </div>
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
                                        @change="filterSchedules"
                                    >
                                        <option value="">All Status</option>
                                        <option value="pending">Pending</option>
                                        <option value="overdue">Overdue</option>
                                        <option value="paid">Paid</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    <label class="text-sm font-medium text-gray-700">Due Date:</label>
                                    <input 
                                        type="date"
                                        v-model="filters.date_from"
                                        class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                        @change="filterSchedules"
                                    >
                                    <span class="text-gray-500">to</span>
                                    <input 
                                        type="date"
                                        v-model="filters.date_to"
                                        class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                        @change="filterSchedules"
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

                        <!-- Schedules Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Installment
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Amount Details
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Due Date
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
                                    <tr v-for="schedule in schedules.data" :key="schedule.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                #{{ schedule.installment_number }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                Schedule #{{ schedule.id }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                ${{ formatCurrency(schedule.due_amount) }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                Principal: ${{ formatCurrency(schedule.principal_amount) }}
                                            </div>
                                            <div class="text-xs text-gray-400">
                                                Interest: ${{ formatCurrency(schedule.interest_amount) }}
                                            </div>
                                            <div class="text-xs text-green-600 font-medium" v-if="schedule.paid_amount > 0">
                                                Paid: ${{ formatCurrency(schedule.paid_amount) }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900">
                                                {{ formatDate(schedule.due_date) }}
                                            </div>
                                            <div class="text-xs text-red-500 font-medium" v-if="isOverdue(schedule)">
                                                Overdue by {{ getDaysOverdue(schedule) }} days
                                            </div>
                                            <div class="text-xs text-yellow-500" v-else-if="isDueSoon(schedule)">
                                                Due in {{ getDaysUntilDue(schedule) }} days
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="statusBadgeClass(schedule.status)" class="px-2 py-1 text-xs font-medium rounded-full">
                                                {{ formatStatus(schedule.status) }}
                                            </span>
                                            <div class="text-xs text-gray-500 mt-1" v-if="schedule.payments_count > 0">
                                                {{ schedule.payments_count }} payment(s)
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex flex-wrap gap-1">
                                                <!-- View Button -->
                                                <button 
                                                    @click="$inertia.visit(route('payment-schedules.show', schedule.id))"
                                                    class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-blue-500 transition-colors duration-200"
                                                    title="View Schedule Details"
                                                >
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                </button>

                                                <!-- Record Payment Button -->
                                                <button 
                                                    v-if="schedule.status === 'pending' || schedule.status === 'overdue'"
                                                    @click="recordPayment(schedule)"
                                                    class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-1 focus:ring-offset-1 focus:ring-green-500 transition-colors duration-200"
                                                    title="Record Payment"
                                                >
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="schedules.data.length === 0">
                                        <td colspan="5" class="px-6 py-8 text-center">
                                            <div class="text-gray-500">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
                                                </svg>
                                                <h3 class="mt-2 text-sm font-medium text-gray-900">No payment schedules found</h3>
                                                <p class="mt-1 text-sm text-gray-500">
                                                    {{ hasActiveFilters ? 'Try changing your filters' : 'No payment schedules have been created for this loan' }}
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <Pagination :links="schedules.links" class="mt-6" />
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
import { router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    loan: Object,
    schedules: Object,
    loanStats: Object,
    filters: Object
});

const filters = ref({
    status: props.filters?.status || '',
    date_from: props.filters?.date_from || '',
    date_to: props.filters?.date_to || ''
});

const hasActiveFilters = computed(() => {
    return Object.values(filters.value).some(value => value !== '');
});

const filterSchedules = () => {
    router.get(route('payment-schedules.loan', props.loan.id), filters.value, {
        preserveState: true,
        replace: true,
    });
};

const clearFilters = () => {
    filters.value = { status: '', date_from: '', date_to: '' };
    filterSchedules();
};

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

const formatStatus = (status) => {
    const statusMap = {
        'pending': 'Pending',
        'paid': 'Paid',
        'overdue': 'Overdue',
        'cancelled': 'Cancelled'
    };
    return statusMap[status] || status;
};

const statusBadgeClass = (status) => {
    const classes = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'paid': 'bg-green-100 text-green-800',
        'overdue': 'bg-red-100 text-red-800',
        'cancelled': 'bg-gray-100 text-gray-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const isOverdue = (schedule) => {
    return schedule.status === 'overdue' || (schedule.status === 'pending' && new Date(schedule.due_date) < new Date());
};

const isDueSoon = (schedule) => {
    if (schedule.status !== 'pending') return false;
    const dueDate = new Date(schedule.due_date);
    const today = new Date();
    const diffTime = dueDate - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays <= 7 && diffDays >= 0;
};

const getDaysOverdue = (schedule) => {
    const dueDate = new Date(schedule.due_date);
    const today = new Date();
    const diffTime = today - dueDate;
    return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
};

const getDaysUntilDue = (schedule) => {
    const dueDate = new Date(schedule.due_date);
    const today = new Date();
    const diffTime = dueDate - today;
    return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
};

const recordPayment = (schedule) => {
    router.visit(route('payments.create'), {
        data: { 
            loan_id: schedule.loan_id,
            loan_payment_schedule_id: schedule.id
        }
    });
};
</script>