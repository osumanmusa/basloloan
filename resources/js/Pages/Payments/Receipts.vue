<template>
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Payment Receipt #{{ payment.id }}
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Payment confirmation receipt</p>
                </div>
                <div class="flex space-x-2">
                    <SecondaryButton @click="$inertia.visit(route('payments.index'))">
                        Back to Payments
                    </SecondaryButton>
                    <PrimaryButton @click="exportPDF" class="flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Export PDF</span>
                    </PrimaryButton>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <!-- Printable Receipt -->
                <div class="bg-white shadow-sm sm:rounded-lg p-8" id="receipt">
                    <!-- Header -->
                    <div class="text-center mb-8 border-b pb-6">
                        <h1 class="text-2xl font-bold text-gray-900">Payment Receipt</h1>
                        <p class="text-gray-600">Loan Management System</p>
                    </div>

                    <!-- Receipt Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <!-- Payment Information -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Details</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Receipt Number:</span>
                                    <span class="font-semibold">#{{ payment.id }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Payment Date:</span>
                                    <span class="font-semibold">{{ formatDate(payment.payment_date) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Due Date:</span>
                                    <span class="font-semibold">{{ formatDate(payment.due_date) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Status:</span>
                                    <span :class="statusBadgeClass(payment.status)" class="px-2 py-1 text-xs font-medium rounded-full">
                                        {{ formatStatus(payment.status) }}
                                    </span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Payment Method:</span>
                                    <span class="font-semibold capitalize">{{ payment.payment_method?.replace('_', ' ') }}</span>
                                </div>
                                <div class="flex justify-between" v-if="payment.reference_number">
                                    <span class="text-gray-600">Reference:</span>
                                    <span class="font-semibold">{{ payment.reference_number }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Amount Information -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Amount Details</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between text-lg">
                                    <span class="text-gray-700">Total Amount:</span>
                                    <span class="font-bold text-green-600">${{ payment.amount.toLocaleString() }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Principal:</span>
                                    <span class="font-semibold">${{ payment.principal_amount.toLocaleString() }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Interest:</span>
                                    <span class="font-semibold">${{ payment.interest_amount.toLocaleString() }}</span>
                                </div>
                                <div class="border-t pt-2 mt-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-700">Received by:</span>
                                        <span class="font-semibold">{{ payment.receiver?.name || 'System' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer & Loan Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Customer Information</h3>
                            <div class="space-y-2">
                                <p class="font-semibold">{{ payment.loan.customer.first_name }} {{ payment.loan.customer.last_name }}</p>
                                <p class="text-gray-600">{{ payment.loan.customer.email }}</p>
                                <p class="text-gray-600">{{ payment.loan.customer.phone }}</p>
                                <p class="text-gray-600">{{ payment.loan.customer.address }}</p>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Loan Information</h3>
                            <div class="space-y-2">
                                <p class="font-semibold">Loan #{{ payment.loan.id }}</p>
                                <p class="text-gray-600">Original Amount: ${{ payment.loan.amount.toLocaleString() }}</p>
                                <p class="text-gray-600">Remaining Balance: ${{ payment.loan.remaining_balance.toLocaleString() }}</p>
                                <p class="text-gray-600">Interest Rate: {{ payment.loan.interest_rate }}%</p>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="border-t pt-6" v-if="payment.notes">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Notes</h3>
                        <p class="text-gray-600">{{ payment.notes }}</p>
                    </div>

                    <!-- Footer -->
                    <div class="border-t pt-6 mt-8 text-center text-gray-500 text-sm">
                        <p>Generated on {{ currentDate }}</p>
                        <p>Thank you for your payment!</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 flex justify-center space-x-4">
                    <SecondaryButton @click="printReceipt">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                        Print Receipt
                    </SecondaryButton>
                    <PrimaryButton @click="exportPDF">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Download PDF
                    </PrimaryButton>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    payment: Object
});

const currentDate = new Date().toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
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
        'completed': 'bg-green-100 text-green-800',
        'late': 'bg-red-100 text-red-800',
        'partial': 'bg-blue-100 text-blue-800',
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const printReceipt = () => {
    const receiptElement = document.getElementById('receipt');
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>Payment Receipt #${props.payment.id}</title>
                <script src="https://cdn.tailwindcss.com"><\/script>
            </head>
            <body>
                ${receiptElement.innerHTML}
                <script>
                    window.onload = function() {
                        window.print();
                        setTimeout(() => window.close(), 500);
                    }
                <\/script>
            </body>
        </html>
    `);
    printWindow.document.close();
};

const exportPDF = () => {
    router.visit(route('payments.export.pdf', props.payment.id));
};
</script>