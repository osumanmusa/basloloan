<template>
    <div class="bg-white p-6 rounded-lg shadow">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Recent Loans</h3>
            <Link 
                :href="route('loans.index')" 
                class="text-sm text-blue-600 hover:text-blue-900 font-medium"
            >
                View All
            </Link>
        </div>
        
        <div class="space-y-3">
            <div 
                v-for="loan in loans" 
                :key="loan.id"
                class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
            >
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <span class="text-blue-600 font-semibold text-sm">
                            #{{ loan.id }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">
                            {{ loan.customer.full_name }}
                        </p>
                        <p class="text-sm text-gray-500">
                            ${{ loan.amount.toLocaleString() }}
                        </p>
                    </div>
                </div>
                <span :class="statusBadgeClass(loan.status)" class="text-xs font-medium px-2 py-1 rounded-full">
                    {{ loan.status }}
                </span>
            </div>
            
            <div v-if="loans.length === 0" class="text-center py-8 text-gray-500">
                <p>No recent loans</p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    loans: {
        type: Array,
        default: () => []
    }
});

const statusBadgeClass = (status) => {
    const classes = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'approved': 'bg-green-100 text-green-800',
        'rejected': 'bg-red-100 text-red-800',
        'active': 'bg-blue-100 text-blue-800',
        'completed': 'bg-gray-100 text-gray-800',
        'defaulted': 'bg-red-100 text-red-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};
</script>