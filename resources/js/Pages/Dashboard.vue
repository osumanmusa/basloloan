<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import StatCard from '@/Components/StatCard.vue';
import PieChart from '@/Components/Charts/PieChart.vue';
import BarChart from '@/Components/Charts/BarChart.vue';
import SidebarLayout from '@/Layouts/SidebarLayout.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
// import StatCard from '@/Components/StatCard.vue';
// import PieChart from '@/Components/Charts/PieChart.vue';
// import BarChart from '@/Components/Charts/BarChart.vue';
import QuickActions from '@/Components/QuickActions.vue';
import RecentLoans from '@/Components/RecentLoans.vue';
import UpcomingPayments from '@/Components/UpcomingPayments.vue';
import PerformanceMetrics from '@/Components/PerformanceMetrics.vue';
// import QuickActions from '@/Components/QuickActions.vue';
// import RecentLoans from '@/Components/RecentLoans.vue';
// import UpcomingPayments from '@/Components/UpcomingPayments.vue';
// import PerformanceMetrics from '@/Components/PerformanceMetrics.vue';
// defineProps({
//     stats: Object,
//     recentLoans: Array,
//     charts: Object,
//     upcomingPayments: Array,
//     performanceMetrics: Object,
// });

const statusBadgeClass = (status) => {
    const classes = {
        'pending': 'bg-yellow-100 text-yellow-800',
        'approved': 'bg-green-100 text-green-800',
        'rejected': 'bg-red-100 text-red-800',
        'active': 'bg-blue-100 text-blue-800',
        'completed': 'bg-gray-100 text-gray-800',
        'defaulted': 'bg-red-100 text-red-800',
    };
    return `px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${classes[status] || 'bg-gray-100 text-gray-800'}`;
};
const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US').format(value);
};
const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            total_portfolio: 0,
            active_loans: 0,
            monthly_revenue: 0,
            default_rate: 0
        })
    },
    charts: {
        type: Object,
        default: () => ({
            loanStatus: [],
            monthlyDisbursements: []
        })
    },
    recentLoans: {
        type: Array,
        default: () => []
    },
    upcomingPayments: {
        type: Array,
        default: () => []
    },
    performanceMetrics: {
        type: Object,
        default: () => ({
            collection_rate: 0,
            average_loan_size: 0,
            customer_satisfaction: 0
        })
    }
});

// const formatCurrency = (value) => {
//     return new Intl.NumberFormat('en-US').format(value || 0);
// };
</script>

<template>
    <Head title="Dashboard" />
   <AppLayout>
        <template #header>
            <h1 class="text-2xl font-semibold text-gray-900">
                Dashboard
            </h1>
            <p class="mt-1 text-sm text-gray-600">
                Overview of your loan management system
            </p>
        </template>


        <div class="py-12">
            <div class="max-w-full mx-auto sm:px-6 lg:px-8">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <StatCard 
                        title="Total Loan Portfolio"
                        :value="`GH₵${formatCurrency(stats.total_portfolio)}`"
                        icon="portfolio"
                        :trend="12.5"
                    />
                    <StatCard 
                        title="Active Loans"
                        :value="stats.active_loans"
                        icon="loans"
                        :trend="8.2"
                    />
                    <StatCard 
                        title="Monthly Revenue"
                        :value="`GH₵${formatCurrency(stats.monthly_revenue)}`"
                        icon="revenue"
                        :trend="15.3"
                    />
                    <StatCard 
                        title="Default Rate"
                        :value="`${stats.default_rate}%`"
                        icon="risk"
                        :trend="-2.1"
                        :trend-up="false"
                    />
                </div>

                <!-- Charts and Analytics -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <!-- Loan Status Distribution -->
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-semibold mb-4">Loan Status Distribution</h3>
                        <div class="h-64">
                            <PieChart 
                                :data="charts.loanStatus" 
                                title="Loan Status Distribution"
                            />
                        </div>
                    </div>

                    <!-- Monthly Disbursements --> 
                    <div class="bg-white p-6 rounded-lg shadow " style="height:450px;">
                        <h3 class="text-lg font-semibold mb-4">Monthly Disbursements</h3>
                        <div class="h-64">
                            <BarChart 
                                :data="charts.monthlyDisbursements"
                                title="Monthly Disbursements"
                            />
                        </div>
                    </div>
                </div>

                <!-- Quick Actions & Recent Activity -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Quick Actions -->
                    <div class="space-y-6">
                        <!-- <QuickActions /> -->
                        <UpcomingPayments :payments="upcomingPayments" />
                    </div>

                    <!-- Recent Activity & Performance -->
                    <div class="lg:col-span-2 space-y-6">
                        <RecentLoans :loans="recentLoans" />
                        <!-- <PerformanceMetrics :metrics="performanceMetrics" /> -->
                    </div>
                </div>
            </div>
        </div>
       
     </AppLayout>
</template>


