<template>
    <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Performance Metrics</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <p class="text-2xl font-bold text-green-900">
                    {{ metrics.collection_rate }}%
                </p>
                <p class="text-sm text-green-600">Collection Rate</p>
            </div>
            
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <p class="text-2xl font-bold text-blue-900">
                    ${{ Math.round(metrics.average_loan_size).toLocaleString() }}
                </p>
                <p class="text-sm text-blue-600">Avg Loan Size</p>
            </div>
            
            <div class="text-center p-4 bg-purple-50 rounded-lg">
                <p class="text-2xl font-bold text-purple-900">
                    {{ metrics.customer_satisfaction }}%
                </p>
                <p class="text-sm text-purple-600">Satisfaction</p>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    metrics: {
        type: Object,
        default: () => ({
            collection_rate: 0,
            average_loan_size: 0,
            customer_satisfaction: 0
        })
    }
});
</script>