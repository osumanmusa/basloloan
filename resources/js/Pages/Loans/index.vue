<template>
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="font-bold text-2xl text-gray-900 leading-tight">
                        Loan Management
                    </h1>
                    <div class="flex items-center space-x-3 mt-1">
                        <p class="text-sm text-gray-600">Manage loan applications and approvals</p>
                        <span class="text-gray-300">•</span>
                        <div class="flex items-center space-x-4 text-xs">
                            <div class="flex items-center">
                                <div class="w-2 h-2 rounded-full bg-blue-500 mr-1"></div>
                                <span class="text-gray-600">{{ loans.total }} total</span>
                            </div>
                            <div class="flex items-center" v-if="pendingCount > 0">
                                <div class="w-2 h-2 rounded-full bg-yellow-500 mr-1"></div>
                                <span class="text-yellow-600">{{ pendingCount }} pending</span>
                            </div>
                            <div class="flex items-center" v-if="activeCount > 0">
                                <div class="w-2 h-2 rounded-full bg-green-500 mr-1"></div>
                                <span class="text-green-600">{{ activeCount }} active</span>
                            </div>
                        </div>
                    </div>
                </div>
                <PrimaryButton 
                    @click="$inertia.visit(route('loans.create'))"
                    class="flex items-center space-x-2 px-6 py-3 font-medium shadow-sm"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>New Loan</span>
                </PrimaryButton>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Success/Error Messages -->
                <div v-if="$page.props.flash.success" class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-800 px-6 py-4 rounded-xl shadow-sm">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-medium">{{ $page.props.flash.success }}</span>
                    </div>
                </div>

                <div v-if="$page.props.flash.error" class="mb-6 bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 text-red-800 px-6 py-4 rounded-xl shadow-sm">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-medium">{{ $page.props.flash.error }}</span>
                    </div>
                </div>

                <div class="bg-white overflow-hidden rounded-2xl shadow-lg border border-gray-100">
                    <div class="p-8">
                        <!-- Advanced Filters Section -->
                        <div class="mb-8">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-bold text-gray-900">Filter Loans</h3>
                                <button 
                                    @click="toggleAdvancedFilters"
                                    class="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center"
                                >
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="showAdvancedFilters ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7'" />
                                    </svg>
                                    {{ showAdvancedFilters ? 'Simple Filters' : 'Advanced Filters' }}
                                </button>
                            </div>

                            <!-- Simple Filters -->
                            <div v-if="!showAdvancedFilters" class="space-y-4">
                                <div class="flex flex-wrap gap-4">
                                    <!-- Status Filter -->
                                    <div class="w-full md:w-auto">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                                        <select 
                                            v-model="filters.status"
                                            class="w-full md:w-48 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-150 ease-in-out text-sm py-2.5"
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
                                    </div>

                                    <!-- Type Filter -->
                                    <div class="w-full md:w-auto">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Loan Type</label>
                                        <select 
                                            v-model="filters.type"
                                            class="w-full md:w-48 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-150 ease-in-out text-sm py-2.5"
                                            @change="filterLoans"
                                        >
                                            <option value="">All Types</option>
                                            <option value="personal">Personal Loan</option>
                                            <option value="business">Business Loan</option>
                                            <option value="mortgage">Mortgage</option>
                                            <option value="auto">Auto Loan</option>
                                            <option value="education">Education Loan</option>
                                        </select>
                                    </div>

                                    <!-- Purpose Filter -->
                                    <div class="w-full md:w-auto">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Purpose</label>
                                        <select 
                                            v-model="filters.purpose"
                                            class="w-full md:w-48 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-150 ease-in-out text-sm py-2.5"
                                            @change="filterLoans"
                                        >
                                            <option value="">All Purposes</option>
                                            <option value="debt_consolidation">Debt Consolidation</option>
                                            <option value="home_improvement">Home Improvement</option>
                                            <option value="business_expansion">Business Expansion</option>
                                            <option value="education">Education</option>
                                            <option value="medical">Medical Expenses</option>
                                            <option value="vehicle">Vehicle Purchase</option>
                                            <option value="wedding">Wedding</option>
                                            <option value="vacation">Vacation</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>

                                    <!-- Date Range Filter -->
                                    <div class="w-full md:w-auto">
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Date Range</label>
                                        <select 
                                            v-model="filters.date_range"
                                            class="w-full md:w-48 rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-150 ease-in-out text-sm py-2.5"
                                            @change="filterLoans"
                                        >
                                            <option value="">All Time</option>
                                            <option value="today">Today</option>
                                            <option value="yesterday">Yesterday</option>
                                            <option value="this_week">This Week</option>
                                            <option value="last_week">Last Week</option>
                                            <option value="this_month">This Month</option>
                                            <option value="last_month">Last Month</option>
                                            <option value="this_quarter">This Quarter</option>
                                            <option value="last_quarter">Last Quarter</option>
                                            <option value="this_year">This Year</option>
                                            <option value="last_year">Last Year</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Search Box -->
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input 
                                        type="text"
                                        v-model="filters.search"
                                        placeholder="Search by customer name, email, phone, loan ID, or guarantor..."
                                        class="pl-10 w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-150 ease-in-out text-sm py-2.5"
                                        @input="debouncedFilterLoans"
                                    >
                                </div>
                            </div>

                            <!-- Advanced Filters -->
                            <div v-else class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                    <!-- Amount Range -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Amount Range (₵)</label>
                                        <div class="flex space-x-2">
                                            <input 
                                                type="number"
                                                v-model="filters.min_amount"
                                                placeholder="Min"
                                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-150 ease-in-out text-sm py-2.5"
                                                @input="debouncedFilterLoans"
                                            >
                                            <input 
                                                type="number"
                                                v-model="filters.max_amount"
                                                placeholder="Max"
                                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-150 ease-in-out text-sm py-2.5"
                                                @input="debouncedFilterLoans"
                                            >
                                        </div>
                                    </div>

                                    <!-- Interest Rate Range -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Interest Rate (%)</label>
                                        <div class="flex space-x-2">
                                            <input 
                                                type="number"
                                                v-model="filters.min_interest"
                                                placeholder="Min"
                                                min="0.1"
                                                max="50"
                                                step="0.1"
                                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-150 ease-in-out text-sm py-2.5"
                                                @input="debouncedFilterLoans"
                                            >
                                            <input 
                                                type="number"
                                                v-model="filters.max_interest"
                                                placeholder="Max"
                                                min="0.1"
                                                max="50"
                                                step="0.1"
                                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-150 ease-in-out text-sm py-2.5"
                                                @input="debouncedFilterLoans"
                                            >
                                        </div>
                                    </div>

                                    <!-- Term Range -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Term (Months)</label>
                                        <div class="flex space-x-2">
                                            <input 
                                                type="number"
                                                v-model="filters.min_term"
                                                placeholder="Min"
                                                min="1"
                                                max="360"
                                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-150 ease-in-out text-sm py-2.5"
                                                @input="debouncedFilterLoans"
                                            >
                                            <input 
                                                type="number"
                                                v-model="filters.max_term"
                                                placeholder="Max"
                                                min="1"
                                                max="360"
                                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-150 ease-in-out text-sm py-2.5"
                                                @input="debouncedFilterLoans"
                                            >
                                        </div>
                                    </div>

                                    <!-- Created By -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Created By</label>
                                        <select 
                                            v-model="filters.created_by"
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-150 ease-in-out text-sm py-2.5"
                                            @change="filterLoans"
                                        >
                                            <option value="">All Users</option>
                                            <option v-for="user in uniqueCreators" :key="user.id" :value="user.id">
                                                {{ user.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Date Range Picker -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">From Date</label>
                                        <input 
                                            type="date"
                                            v-model="filters.start_date"
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-150 ease-in-out text-sm py-2.5"
                                            @change="filterLoans"
                                        >
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">To Date</label>
                                        <input 
                                            type="date"
                                            v-model="filters.end_date"
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-150 ease-in-out text-sm py-2.5"
                                            @change="filterLoans"
                                        >
                                    </div>
                                </div>

                                <!-- Sort Options -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Sort By</label>
                                        <select 
                                            v-model="filters.sort_by"
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-150 ease-in-out text-sm py-2.5"
                                            @change="filterLoans"
                                        >
                                            <option value="created_at">Created Date</option>
                                            <option value="amount">Loan Amount</option>
                                            <option value="interest_rate">Interest Rate</option>
                                            <option value="term_months">Loan Term</option>
                                            <option value="updated_at">Last Updated</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-700 mb-1">Sort Order</label>
                                        <select 
                                            v-model="filters.sort_order"
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-150 ease-in-out text-sm py-2.5"
                                            @change="filterLoans"
                                        >
                                            <option value="desc">Newest/Descending</option>
                                            <option value="asc">Oldest/Ascending</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Filter Actions -->
                            <div class="flex justify-between items-center mt-6 pt-6 border-t border-gray-200">
                                <div class="text-sm text-gray-600">
                                    Showing {{ loans.from || 0 }} to {{ loans.to || 0 }} of {{ loans.total }} loans
                                    <span v-if="hasActiveFilters" class="ml-2 px-2 py-1 bg-blue-50 text-blue-700 text-xs rounded-full">
                                        {{ activeFilterCount }} active filter(s)
                                    </span>
                                </div>
                                <div class="flex space-x-3">
                                    <button 
                                        @click="clearFilters"
                                        class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg border border-gray-300 transition duration-150 ease-in-out"
                                        v-if="hasActiveFilters"
                                    >
                                        Clear All Filters
                                    </button>
                                    <button 
                                        @click="exportLoans"
                                        class="px-4 py-2.5 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg border border-transparent transition duration-150 ease-in-out flex items-center"
                                    >
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Export
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Loans Table -->
                        <div class="overflow-x-auto rounded-xl border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider bg-gray-50/50">
                                            <div class="flex items-center">
                                                Loan Details
                                                <button @click="sortBy('id')" class="ml-1">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider bg-gray-50/50">
                                            Customer
                                        </th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider bg-gray-50/50">
                                            Amount & Terms
                                        </th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider bg-gray-50/50">
                                            Status & Dates
                                        </th>
                                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider bg-gray-50/50">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="loan in loans.data" :key="loan.id" class="hover:bg-gray-50/50 transition-colors duration-150">
                                        <td class="px-6 py-4">
                                            <div class="flex items-start space-x-3">
                                                <div :class="statusBadgeClass(loan.status)" class="flex-shrink-0 w-2 h-full rounded"></div>
                                                <div>
                                                    <div class="flex items-center space-x-2">
                                                        <span class="text-sm font-bold text-gray-900">
                                                            #{{ loan.id }}
                                                        </span>
                                                        <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">
                                                            {{ formatLoanType(loan.type) }}
                                                        </span>
                                                    </div>
                                                    <div class="text-sm text-gray-600 mt-1 capitalize">
                                                        {{ formatPurpose(loan.purpose) }}
                                                    </div>
                                                    <div class="text-xs text-gray-400 mt-1 flex items-center">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        {{ formatDate(loan.created_at) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center space-x-3">
                                                <div class="flex-shrink-0 h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                    <span class="text-blue-600 font-medium text-xs">
                                                        {{ getInitials(loan.customer.first_name + ' ' + loan.customer.last_name) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ getCustomerFullName(loan.customer) }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        {{ loan.customer.email }}
                                                    </div>
                                                    <div class="text-xs text-gray-400 flex items-center mt-1">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                        </svg>
                                                        {{ loan.customer.phone }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="space-y-1">
                                                <div class="text-lg font-bold text-gray-900">
                                                    ₵{{ formatCurrency(loan.amount) }}
                                                </div>
                                                <div class="flex items-center space-x-3 text-sm text-gray-600">
                                                    <span class="flex items-center">
                                                        <svg class="w-3 h-3 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                                        </svg>
                                                        {{ loan.interest_rate }}%
                                                    </span>
                                                    <span class="text-gray-300">•</span>
                                                    <span class="flex items-center">
                                                        <svg class="w-3 h-3 mr-1 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        {{ loan.term_months }} months
                                                    </span>
                                                </div>
                                                <div class="text-xs text-gray-500" v-if="loan.monthly_payment">
                                                    Monthly: ₵{{ formatCurrency(loan.monthly_payment) }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="space-y-2">
                                                <div>
                                                    <span :class="statusBadgeClass(loan.status)" class="px-3 py-1.5 text-xs font-bold rounded-full">
                                                        {{ formatStatus(loan.status) }}
                                                    </span>
                                                </div>
                                                <div class="space-y-1 text-xs text-gray-500">
                                                    <div v-if="loan.approved_at" class="flex items-center">
                                                        <svg class="w-3 h-3 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        Approved {{ formatDate(loan.approved_at) }}
                                                    </div>
                                                    <div v-if="loan.disbursement_date" class="flex items-center">
                                                        <svg class="w-3 h-3 mr-1 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0 6v1m0-1v1m6-10h2m-10 0h2m-1 14h1" />
                                                        </svg>
                                                        Disbursed {{ formatDate(loan.disbursement_date) }}
                                                    </div>
                                                    <div v-if="loan.due_date" class="flex items-center">
                                                        <svg class="w-3 h-3 mr-1 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                        Due {{ formatDate(loan.due_date) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-wrap gap-1.5">
                                                <!-- View Button -->
                                                <button 
                                                    @click="$inertia.visit(route('loans.show', loan.id))"
                                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-blue-500 transition-all duration-200 shadow-sm"
                                                    title="View Loan Details"
                                                >
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                    View
                                                </button>

                                                <!-- Edit Button -->
                                                <button 
                                                    v-if="loan.status === 'pending'"
                                                    @click="$inertia.visit(route('loans.edit', loan.id))"
                                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-green-500 transition-all duration-200 shadow-sm"
                                                    title="Edit Loan"
                                                >
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                    </svg>
                                                    Edit
                                                </button>

                                                <!-- Quick Actions Dropdown -->
                                                <div class="relative inline-block" v-if="loan.status === 'pending'">
                                                    <button 
                                                        @click="toggleQuickActions(loan.id)"
                                                        class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-lg text-white bg-gray-600 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-gray-500 transition-all duration-200 shadow-sm"
                                                    >
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                                        </svg>
                                                    </button>
                                                    
                                                    <!-- Dropdown Menu -->
                                                    <div v-if="quickActionsOpen === loan.id" class="absolute right-0 mt-1 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-10">
                                                        <div class="py-1">
                                                            <button 
                                                                @click="approveLoan(loan)"
                                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700 flex items-center"
                                                            >
                                                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                                </svg>
                                                                Approve Loan
                                                            </button>
                                                            <button 
                                                                @click="rejectLoan(loan)"
                                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 flex items-center"
                                                            >
                                                                <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                                </svg>
                                                                Reject Loan
                                                            </button>
                                                            <button 
                                                                @click="deleteLoan(loan)"
                                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700 flex items-center"
                                                            >
                                                                <svg class="w-4 h-4 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                                </svg>
                                                                Delete Loan
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Disburse Button -->
                                                <button 
                                                    v-if="loan.status === 'approved'"
                                                    @click="disburseLoan(loan)"
                                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-lg text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-purple-500 transition-all duration-200 shadow-sm"
                                                    title="Disburse Loan"
                                                >
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0 6v1m0-1v1m6-10h2m-10 0h2m-1 14h1"/>
                                                    </svg>
                                                    Disburse
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    <!-- Empty State -->
                                    <tr v-if="loans.data.length === 0">
                                        <td colspan="5" class="px-6 py-12 text-center">
                                            <div class="text-gray-400">
                                                <svg class="mx-auto h-16 w-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                                </svg>
                                                <h3 class="text-lg font-medium text-gray-900 mb-2">No loans found</h3>
                                                <p class="text-gray-600 mb-6 max-w-md mx-auto">
                                                    {{ hasActiveFilters 
                                                        ? 'No loans match your current filters. Try adjusting your search criteria.' 
                                                        : 'Get started by creating your first loan application.' 
                                                    }}
                                                </p>
                                                <div v-if="!hasActiveFilters">
                                                    <PrimaryButton 
                                                        @click="$inertia.visit(route('loans.create'))"
                                                        class="flex items-center space-x-2 mx-auto px-6 py-3"
                                                    >
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                        </svg>
                                                        <span>Create New Loan</span>
                                                    </PrimaryButton>
                                                </div>
                                                <button 
                                                    v-else
                                                    @click="clearFilters"
                                                    class="text-sm text-blue-600 hover:text-blue-700 font-medium"
                                                >
                                                    Clear all filters
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-8">
                            <Pagination :links="loans.links" />
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
import { Link, router } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import { ref, computed, onMounted } from 'vue';

const props = defineProps({
    loans: Object,
    filters: Object,
    creators: Array
});

const showAdvancedFilters = ref(false);
const quickActionsOpen = ref(null);
let debounceTimer;

const filters = ref({
    status: props.filters?.status || '',
    type: props.filters?.type || '',
    purpose: props.filters?.purpose || '',
    date_range: props.filters?.date_range || '',
    search: props.filters?.search || '',
    min_amount: props.filters?.min_amount || '',
    max_amount: props.filters?.max_amount || '',
    min_interest: props.filters?.min_interest || '',
    max_interest: props.filters?.max_interest || '',
    min_term: props.filters?.min_term || '',
    max_term: props.filters?.max_term || '',
    created_by: props.filters?.created_by || '',
    start_date: props.filters?.start_date || '',
    end_date: props.filters?.end_date || '',
    sort_by: props.filters?.sort_by || 'created_at',
    sort_order: props.filters?.sort_order || 'desc'
});

// Computed properties
const pendingCount = computed(() => {
    return props.loans.data.filter(loan => loan.status === 'pending').length;
});

const activeCount = computed(() => {
    return props.loans.data.filter(loan => ['active', 'disbursed'].includes(loan.status)).length;
});

const hasActiveFilters = computed(() => {
    return Object.values(filters.value).some(value => 
        value !== '' && value !== null && value !== undefined && value !== 'created_at' && value !== 'desc'
    );
});

const activeFilterCount = computed(() => {
    return Object.values(filters.value).filter(value => 
        value !== '' && value !== null && value !== undefined && value !== 'created_at' && value !== 'desc'
    ).length;
});

const uniqueCreators = computed(() => {
    const creators = props.creators || [];
    return [...new Map(creators.map(item => [item.id, item])).values()];
});

const filterLoans = () => {
    clearTimeout(debounceTimer);
    router.get(route('loans.index'), filters.value, {
        preserveState: true,
        replace: true,
        preserveScroll: true
    });
};

const debouncedFilterLoans = () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(filterLoans, 300);
};

const clearFilters = () => {
    filters.value = {
        status: '',
        type: '',
        purpose: '',
        date_range: '',
        search: '',
        min_amount: '',
        max_amount: '',
        min_interest: '',
        max_interest: '',
        min_term: '',
        max_term: '',
        created_by: '',
        start_date: '',
        end_date: '',
        sort_by: 'created_at',
        sort_order: 'desc'
    };
    filterLoans();
};

const toggleAdvancedFilters = () => {
    showAdvancedFilters.value = !showAdvancedFilters.value;
};

const toggleQuickActions = (loanId) => {
    quickActionsOpen.value = quickActionsOpen.value === loanId ? null : loanId;
};

const closeQuickActions = () => {
    quickActionsOpen.value = null;
};

const sortBy = (field) => {
    if (filters.value.sort_by === field) {
        filters.value.sort_order = filters.value.sort_order === 'asc' ? 'desc' : 'asc';
    } else {
        filters.value.sort_by = field;
        filters.value.sort_order = 'desc';
    }
    filterLoans();
};

const formatCurrency = (amount) => {
    if (!amount) return '0.00';
    return parseFloat(amount).toLocaleString('en-GH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
};

const getCustomerFullName = (customer) => {
    if (customer.full_name) {
        return customer.full_name;
    }
    return `${customer.first_name} ${customer.last_name}`;
};

const getInitials = (fullName) => {
    if (!fullName) return '';
    return fullName
        .split(' ')
        .map(name => name[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
};

const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('en-GH', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const formatStatus = (status) => {
    return status.replace(/_/g, ' ').toUpperCase();
};

const formatLoanType = (type) => {
    const types = {
        'personal': 'Personal',
        'business': 'Business',
        'mortgage': 'Mortgage',
        'auto': 'Auto',
        'education': 'Education'
    };
    return types[type] || type;
};

const formatPurpose = (purpose) => {
    return purpose?.replace('_', ' ') || '';
};

const statusBadgeClass = (status) => {
    const classes = {
        'pending': 'bg-gradient-to-r from-yellow-100 to-amber-100 text-yellow-800 border border-yellow-200',
        'under_review': 'bg-gradient-to-r from-blue-100 to-cyan-100 text-blue-800 border border-blue-200',
        'approved': 'bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 border border-green-200',
        'rejected': 'bg-gradient-to-r from-red-100 to-rose-100 text-red-800 border border-red-200',
        'disbursed': 'bg-gradient-to-r from-purple-100 to-violet-100 text-purple-800 border border-purple-200',
        'active': 'bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 border border-blue-200',
        'completed': 'bg-gradient-to-r from-gray-100 to-slate-100 text-gray-800 border border-gray-200',
        'defaulted': 'bg-gradient-to-r from-red-100 to-pink-100 text-red-800 border border-red-200',
    };
    return classes[status] || 'bg-gray-100 text-gray-800 border border-gray-200';
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

const exportLoans = () => {
    alert('Export functionality would be implemented here');
    // In a real app, this would trigger a CSV/Excel export
    // router.get(route('loans.export'), filters.value);
};

// Close quick actions when clicking outside
onMounted(() => {
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.relative.inline-block')) {
            closeQuickActions();
        }
    });
});
</script>

<style scoped>
/* Custom styles for better UX */
.relative.inline-block:hover .absolute {
    display: block;
}

tbody tr:hover {
    background-color: rgba(249, 250, 251, 0.8);
}

/* Ensure table cells have proper spacing */
td {
    vertical-align: top;
}

/* Status indicator bar */
.flex-shrink-0.w-2.h-full {
    min-height: 60px;
}
</style>