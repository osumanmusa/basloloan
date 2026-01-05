<template>
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Payment Schedules
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Loans with pending payment schedules</p>
                </div>
                <SecondaryButton 
                    @click="$inertia.visit(route('loans.index'))"
                    class="flex items-center space-x-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z"/>
                    </svg>
                    <span>View Loans</span>
                </SecondaryButton>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Stats Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                <div class="flex items-center">
                                    <div class="p-2 bg-blue-100 rounded-lg">
                                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0 6v1m0-1v1m6-10h2m-10 0h2m-1 14h1"/>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-600">Loans with Pending</p>
                                        <p class="text-2xl font-bold text-gray-900">{{ stats.total_loans_with_pending || 0 }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                <div class="flex items-center">
                                    <div class="p-2 bg-yellow-100 rounded-lg">
                                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-600">Pending Schedules</p>
                                        <p class="text-2xl font-bold text-yellow-600">{{ stats.total_pending_schedules || 0 }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                <div class="flex items-center">
                                    <div class="p-2 bg-red-100 rounded-lg">
                                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-600">Overdue</p>
                                        <p class="text-2xl font-bold text-red-600">{{ stats.overdue_schedules || 0 }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                <div class="flex items-center">
                                    <div class="p-2 bg-green-100 rounded-lg">
                                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0 6v1m0-1v1m6-10h2m-10 0h2m-1 14h1"/>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-600">Total Due</p>
                                        <p class="text-2xl font-bold text-green-600">${{ formatCurrency(stats.total_due_amount) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Filters -->
                        <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                            <div class="flex flex-wrap gap-4 items-center">
                                <div class="flex-1 max-w-md">
                                    <input 
                                        type="text"
                                        v-model="filters.search"
                                        placeholder="Search by customer name..."
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
                                            Loan & Customer
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Loan Details
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Pending Schedules
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Next Due Date
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
                                                {{ loan.customer.first_name }} {{ loan.customer.last_name }}
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
                                                Loan #{{ loan.id }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                ${{ formatCurrency(loan.amount) }} • {{ loan.term_months }} months
                                            </div>
                                            <div class="text-xs text-gray-400 capitalize">
                                                {{ loan.type }} • {{ loan.purpose }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ loan.pending_schedules_count }} schedules
                                            </div>
                                            <div class="text-sm text-red-600 font-semibold" v-if="loan.overdue_schedules_count > 0">
                                                {{ loan.overdue_schedules_count }} overdue
                                            </div>
                                            <div class="text-sm text-green-600">
                                                ${{ formatCurrency(loan.total_due_amount) }} total due
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900" v-if="loan.next_due_date">
                                                {{ formatDate(loan.next_due_date) }}
                                            </div>
                                            <div class="text-sm text-gray-500" v-else>
                                                No pending schedules
                                            </div>
                                            <div class="text-xs text-red-500 font-medium" 
                                                 v-if="loan.next_due_date && isOverdue(loan.next_due_date)">
                                                Overdue
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <PrimaryButton 
                                                @click="$inertia.visit(route('payment-schedules.loan', loan.id))"
                                                class="flex items-center space-x-2"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                </svg>
                                                <span>View Schedules</span>
                                            </PrimaryButton>
                                        </td>
                                    </tr>
                                    <tr v-if="loans.data.length === 0">
                                        <td colspan="5" class="px-6 py-8 text-center">
                                            <div class="text-gray-500">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
                                                </svg>
                                                <h3 class="mt-2 text-sm font-medium text-gray-900">No loans with pending schedules</h3>
                                                <p class="mt-1 text-sm text-gray-500">
                                                    {{ hasActiveFilters ? 'Try changing your filters' : 'All payment schedules are up to date' }}
                                                </p>
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
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Pagination from '@/Components/Pagination.vue';
import { router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    loans: Object,
    stats: Object,
    filters: Object
});

const filters = ref({
    search: props.filters?.search || '',
});

const hasActiveFilters = computed(() => {
    return filters.value.search !== '';
});

const filterLoans = () => {
    router.get(route('payment-schedules.index'), filters.value, {
        preserveState: true,
        replace: true,
    });
};

const clearFilters = () => {
    filters.value = { search: '' };
    filterLoans();
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

const isOverdue = (dueDate) => {
    return new Date(dueDate) < new Date();
};
</script>