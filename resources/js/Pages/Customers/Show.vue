<template>
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ customer.first_name }} {{ customer.last_name }}
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Customer Profile</p>
                </div>
                <div class="flex space-x-2">
                    <SecondaryButton @click="$inertia.visit(route('customers.index'))">
                        Back to Customers
                    </SecondaryButton>
                    <PrimaryButton 
                        @click="$inertia.visit(route('customers.edit', customer.id))"
                    >
                        Edit Customer
                    </PrimaryButton>
                    <PrimaryButton 
                        @click="$inertia.visit(route('loans.create'), { data: { customer_id: customer.id } })"
                        class="flex items-center space-x-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        <span>New Loan</span>
                    </PrimaryButton>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Customer Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-white p-4 rounded-lg shadow">
                        <div class="text-sm text-gray-600">Total Loans</div>
                        <div class="text-2xl font-bold text-gray-900">{{ stats.total_loans }}</div>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow">
                        <div class="text-sm text-gray-600">Active Loans</div>
                        <div class="text-2xl font-bold text-green-600">{{ stats.active_loans }}</div>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow">
                        <div class="text-sm text-gray-600">Total Borrowed</div>
                        <div class="text-2xl font-bold text-blue-600">${{ stats.total_borrowed?.toLocaleString() }}</div>
                    </div>
                    <div class="bg-white p-4 rounded-lg shadow">
                        <div class="text-sm text-gray-600">Pending Loans</div>
                        <div class="text-2xl font-bold text-yellow-600">{{ stats.pending_loans }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Customer Details -->
                    <div class="lg:col-span-2">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Customer Information</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-700">Full Name</label>
                                        <p class="text-sm text-gray-900">{{ customer.first_name }} {{ customer.last_name }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-700">Email</label>
                                        <p class="text-sm text-gray-900">{{ customer.email }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-700">Phone</label>
                                        <p class="text-sm text-gray-900">{{ customer.phone }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-700">ID Number</label>
                                        <p class="text-sm text-gray-900">{{ customer.id_number }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-700">Date of Birth</label>
                                        <p class="text-sm text-gray-900">{{ formatDate(customer.date_of_birth) }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-700">Age</label>
                                        <p class="text-sm text-gray-900">{{ calculateAge(customer.date_of_birth) }} years</p>
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="text-sm font-medium text-gray-700">Address</label>
                                        <p class="text-sm text-gray-900">{{ customer.address }}</p>
                                        <p class="text-sm text-gray-900">{{ customer.city }}, {{ customer.state }} {{ customer.zip_code }}</p>
                                    </div>
                                </div>

                                <!-- Financial Information -->
                                <h3 class="text-lg font-medium text-gray-900 mt-6 mb-4">Financial Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-700">Monthly Income</label>
                                        <p class="text-sm text-gray-900" v-if="customer.monthly_income">
                                            ${{ customer.monthly_income.toLocaleString() }}
                                        </p>
                                        <p class="text-sm text-gray-500" v-else>Not provided</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-700">Employment Status</label>
                                        <p class="text-sm text-gray-900 capitalize">
                                            {{ customer.employment_status?.replace('_', ' ') }}
                                        </p>
                                    </div>
                                    <div class="md:col-span-2" v-if="customer.employment_details">
                                        <label class="text-sm font-medium text-gray-700">Employment Details</label>
                                        <p class="text-sm text-gray-900">{{ customer.employment_details }}</p>
                                    </div>
                                    <div>
                                        <label class="text-sm font-medium text-gray-700">Credit Score</label>
                                        <p class="text-sm text-gray-900" v-if="customer.credit_score">
                                            {{ customer.credit_score }}
                                        </p>
                                        <p class="text-sm text-gray-500" v-else>Not provided</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Loans -->
                    <div>
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Loans</h3>
                                
                                <div class="space-y-3">
                                    <div 
                                        v-for="loan in recentLoans" 
                                        :key="loan.id"
                                        class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50"
                                    >
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">
                                                    ${{ loan.amount.toLocaleString() }}
                                                </p>
                                                <p class="text-xs text-gray-500 capitalize">
                                                    {{ loan.type }} • {{ loan.purpose }}
                                                </p>
                                            </div>
                                            <span :class="statusBadgeClass(loan.status)" class="text-xs font-medium px-2 py-1 rounded-full">
                                                {{ loan.status.replace('_', ' ') }}
                                            </span>
                                        </div>
                                        <div class="text-xs text-gray-400 mt-2">
                                            Created {{ formatDate(loan.created_at) }}
                                        </div>
                                    </div>
                                    
                                    <div v-if="recentLoans.length === 0" class="text-center py-4 text-gray-500">
                                        <p class="text-sm">No loans found</p>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <Link 
                                        :href="route('loans.index', { customer: customer.id })"
                                        class="text-sm text-blue-600 hover:text-blue-900"
                                    >
                                        View all loans →
                                    </Link>
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
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    customer: Object,
    stats: Object,
    recentLoans: Array
});

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const calculateAge = (dateOfBirth) => {
    const today = new Date();
    const birthDate = new Date(dateOfBirth);
    let age = today.getFullYear() - birthDate.getFullYear();
    const monthDiff = today.getMonth() - birthDate.getMonth();
    
    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
        age--;
    }
    
    return age;
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
</script>