<template>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Upcoming Payments</h3>
        
        <div class="space-y-3">
            <div 
                v-for="payment in payments" 
                :key="payment.id"
                class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
            >
                <div>
                    <p class="text-sm font-medium text-gray-900">
                        {{ payment.loan.customer.full_name }}
                    </p>
                    <p class="text-sm text-gray-500">
                        Due: {{ payment.due_date }}
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold text-gray-900">
                        ${{ payment.amount.toLocaleString() }}
                    </p>
                    <p class="text-xs text-gray-500">
                        Loan #{{ payment.loan.id }}
                    </p>
                </div>
            </div>
            
            <div v-if="payments.length === 0" class="text-center py-4 text-gray-500">
                <p>No upcoming payments</p>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    payments: {
        type: Array,
        default: () => []
    }
});
</script>