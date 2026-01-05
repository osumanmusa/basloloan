<template>
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Customer Management
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Manage your customer database</p>
                </div>
                <PrimaryButton 
                    @click="$inertia.visit(route('customers.create'))"
                    class="flex items-center space-x-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 shadow-lg"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    <span>Add New Customer</span>
                </PrimaryButton>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-xl shadow-sm border border-blue-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-medium text-blue-700">Total Customers</div>
                                <div class="text-2xl font-bold text-gray-900 mt-1">{{ customers.total }}</div>
                            </div>
                            <div class="bg-blue-500 p-2 rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-green-50 to-green-100 p-4 rounded-xl shadow-sm border border-green-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-medium text-green-700">Active Loans</div>
                                <div class="text-2xl font-bold text-gray-900 mt-1">{{ activeLoansCount }}</div>
                            </div>
                            <div class="bg-green-500 p-2 rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-xl shadow-sm border border-purple-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-medium text-purple-700">Good Credit Score</div>
                                <div class="text-2xl font-bold text-gray-900 mt-1">{{ goodCreditCount }}</div>
                                <div class="text-xs text-purple-600 mt-1">(≥ 670)</div>
                            </div>
                            <div class="bg-purple-500 p-2 rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-4 rounded-xl shadow-sm border border-yellow-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-medium text-yellow-700">New This Month</div>
                                <div class="text-2xl font-bold text-gray-900 mt-1">{{ newThisMonthCount }}</div>
                            </div>
                            <div class="bg-yellow-500 p-2 rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-200">
                    <div class="p-6">
                        <!-- Header with Search and Export -->
                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
                            <div class="flex-1">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                    <input 
                                        type="text"
                                        v-model="filters.search"
                                        placeholder="Search customers by name, email, phone, or ID..."
                                        class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                        @input="handleSearchInput"
                                    >
                                    <div v-if="filters.search" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                        <button @click="clearSearch" class="text-gray-400 hover:text-gray-600">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <!-- Filter Button -->
                                <button @click="toggleFilterPanel" 
                                        class="inline-flex items-center px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                                    </svg>
                                    Filters
                                    <span v-if="activeFilterCount > 0" class="ml-1.5 bg-blue-100 text-blue-800 text-xs font-semibold px-2 py-0.5 rounded-full">
                                        {{ activeFilterCount }}
                                    </span>
                                </button>
                                
                                <!-- Export Button -->
                                <button @click="exportCustomers" 
                                        class="inline-flex items-center px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Export
                                </button>
                            </div>
                        </div>

                        <!-- Filter Panel -->
                        <div v-if="showFilterPanel" class="mb-6 bg-gray-50 border border-gray-200 rounded-xl p-4 transition-all duration-300">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Employment Status Filter -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Employment Status
                                    </label>
                                    <select v-model="filters.employment_status" 
                                            @change="filterCustomers"
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        <option value="">All Statuses</option>
                                        <option value="employed">Employed</option>
                                        <option value="self-employed">Self-Employed</option>
                                        <option value="unemployed">Unemployed</option>
                                        <option value="retired">Retired</option>
                                        <option value="student">Student</option>
                                    </select>
                                </div>
                                
                                <!-- ID Type Filter -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        ID Type
                                    </label>
                                    <select v-model="filters.id_type" 
                                            @change="filterCustomers"
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        <option value="">All ID Types</option>
                                        <option value="Ghana Card">Ghana Card</option>
                                        <option value="Voters ID">Voters ID</option>
                                        <option value="Passport">Passport</option>
                                        <option value="Drivers License">Driver's License</option>
                                        <option value="National ID">National ID</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                
                                <!-- Credit Score Filter -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Credit Score Range
                                    </label>
                                    <div class="flex items-center space-x-2">
                                        <input v-model="filters.credit_score_min" 
                                               type="number" 
                                               min="300" 
                                               max="850" 
                                               placeholder="Min"
                                               @change="filterCustomers"
                                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                        <span class="text-gray-500">-</span>
                                        <input v-model="filters.credit_score_max" 
                                               type="number" 
                                               min="300" 
                                               max="850" 
                                               placeholder="Max"
                                               @change="filterCustomers"
                                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Active Filters -->
                            <div v-if="activeFilterCount > 0" class="mt-4 pt-4 border-t border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex flex-wrap gap-2">
                                        <span v-if="filters.employment_status" 
                                              class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ formatEmploymentStatus(filters.employment_status) }}
                                            <button @click="clearFilter('employment_status')" class="ml-1.5 text-blue-600 hover:text-blue-800">
                                                ×
                                            </button>
                                        </span>
                                        <span v-if="filters.id_type" 
                                              class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ filters.id_type }}
                                            <button @click="clearFilter('id_type')" class="ml-1.5 text-green-600 hover:text-green-800">
                                                ×
                                            </button>
                                        </span>
                                        <span v-if="filters.credit_score_min || filters.credit_score_max" 
                                              class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            Credit: {{ filters.credit_score_min || '300' }} - {{ filters.credit_score_max || '850' }}
                                            <button @click="clearCreditScoreFilter" class="ml-1.5 text-purple-600 hover:text-purple-800">
                                                ×
                                            </button>
                                        </span>
                                    </div>
                                    <button @click="clearAllFilters" 
                                            class="text-sm text-red-600 hover:text-red-800 font-medium">
                                        Clear All
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Cards/Table -->
                        <div class="mb-4 flex items-center justify-between">
                            <div class="text-sm text-gray-600">
                                Showing <span class="font-semibold">{{ customers.from || 0 }}</span> to 
                                <span class="font-semibold">{{ customers.to || 0 }}</span> of 
                                <span class="font-semibold">{{ customers.total }}</span> customers
                            </div>
                            <div class="flex items-center space-x-2">
                                <!-- View Toggle -->
                                <div class="flex border border-gray-300 rounded-lg overflow-hidden">
                                    <button @click="viewMode = 'grid'" 
                                            :class="['px-3 py-1.5 text-sm', viewMode === 'grid' ? 'bg-blue-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50']">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                                        </svg>
                                    </button>
                                    <button @click="viewMode = 'table'" 
                                            :class="['px-3 py-1.5 text-sm', viewMode === 'table' ? 'bg-blue-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-50']">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Grid View -->
                        <div v-if="viewMode === 'grid' && customers.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div v-for="customer in customers.data" :key="customer.id" 
                                 class="bg-white border border-gray-200 rounded-xl p-4 hover:shadow-md transition-all duration-200">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="relative">
                                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-sm">
                                                {{ getInitials(getFullName(customer)) }}
                                            </div>
                                            <span v-if="customer.credit_score" 
                                                  :class="creditScoreBadgeClass(customer.credit_score)"
                                                  class="absolute -bottom-1 -right-1 text-xs font-bold px-1.5 py-0.5 rounded-full">
                                                {{ customer.credit_score }}
                                            </span>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-gray-900">{{ getFullName(customer) }}</h3>
                                            <p class="text-xs text-gray-500">{{ customer.id_type }}: {{ customer.id_number }}</p>
                                        </div>
                                    </div>
                                    <span :class="employmentStatusClass(customer.employment_status)" 
                                          class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium">
                                        {{ formatEmploymentStatus(customer.employment_status) }}
                                    </span>
                                </div>
                                
                                <div class="space-y-2 mb-4">
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                        {{ customer.email }}
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        {{ customer.phone }}
                                    </div>
                                    <div class="flex items-center text-sm text-gray-600">
                                        <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ customer.city }}, {{ customer.state }}
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                    <div class="flex items-center space-x-4">
                                        <div class="text-sm">
                                            <div class="font-medium text-gray-900">GH₵ {{ formatCurrency(customer.monthly_income || 0) }}</div>
                                            <div class="text-xs text-gray-500">Monthly</div>
                                        </div>
                                        <div class="text-sm">
                                            <div class="font-medium text-gray-900">{{ customer.loans_count || 0 }}</div>
                                            <div class="text-xs text-gray-500">Loans</div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center space-x-1">
                                        <button @click="$inertia.visit(route('customers.show', customer.id))"
                                                class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                                                title="View Profile">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                        <button @click="$inertia.visit(route('customers.edit', customer.id))"
                                                class="p-1.5 text-gray-500 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200"
                                                title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </button>
                                        <button @click="createLoanForCustomer(customer)"
                                                class="p-1.5 text-gray-500 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors duration-200"
                                                title="New Loan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Table View -->
                        <div v-if="viewMode === 'table'">
                            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Customer
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Contact Information
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Financial Info
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
                                        <tr v-for="customer in customers.data" :key="customer.id" class="hover:bg-gray-50 transition-colors duration-150">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-sm">
                                                        <span class="text-white font-bold text-sm">
                                                            {{ getInitials(getFullName(customer)) }}
                                                        </span>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-semibold text-gray-900">
                                                            {{ getFullName(customer) }}
                                                        </div>
                                                        <div class="text-xs text-gray-500">
                                                            {{ customer.id_type }}: {{ customer.id_number }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ customer.email }}</div>
                                                <div class="text-sm text-gray-500">{{ customer.phone }}</div>
                                                <div class="text-xs text-gray-400">
                                                    {{ customer.city }}, {{ customer.state }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    GH₵ {{ formatCurrency(customer.monthly_income || 0) }}
                                                </div>
                                                <div class="text-xs">
                                                    <span :class="creditScoreTextClass(customer.credit_score)">
                                                        {{ customer.credit_score || 'N/A' }}
                                                    </span>
                                                    <span class="text-gray-500"> credit score</span>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ customer.loans_count || 0 }} loans
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span :class="employmentStatusClass(customer.employment_status)" 
                                                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                                    {{ formatEmploymentStatus(customer.employment_status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex items-center space-x-1">
                                                    <button @click="$inertia.visit(route('customers.show', customer.id))"
                                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                                        View
                                                    </button>
                                                    <button @click="$inertia.visit(route('customers.edit', customer.id))"
                                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                                                        Edit
                                                    </button>
                                                    <button @click="createLoanForCustomer(customer)"
                                                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200">
                                                        Loan
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Empty State -->
                        <div v-if="customers.data.length === 0" class="text-center py-12">
                            <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">No customers found</h3>
                            <p class="text-gray-500 mb-6 max-w-md mx-auto">
                                {{ hasActiveFilters ? 'Try adjusting your filters to find what you\'re looking for.' : 'Get started by adding your first customer.' }}
                            </p>
                            <div v-if="!hasActiveFilters">
                                <PrimaryButton 
                                    @click="$inertia.visit(route('customers.create'))"
                                    class="flex items-center space-x-2 mx-auto"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    <span>Add New Customer</span>
                                </PrimaryButton>
                            </div>
                            <div v-else>
                                <button @click="clearAllFilters" 
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-600 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                    Clear all filters
                                </button>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <Pagination v-if="customers.data.length > 0" :links="customers.links" class="mt-6" />
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
import { ref, computed, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    customers: Object,
    filters: Object
});

// Reactive state
const viewMode = ref('grid'); // 'grid' or 'table'
const showFilterPanel = ref(false);
let searchTimeout = null;

const filters = ref({
    search: props.filters?.search || '',
    employment_status: props.filters?.employment_status || '',
    id_type: props.filters?.id_type || '',
    credit_score_min: props.filters?.credit_score_min || '',
    credit_score_max: props.filters?.credit_score_max || ''
});

// Computed properties
const activeFilterCount = computed(() => {
    let count = 0;
    if (filters.value.search) count++;
    if (filters.value.employment_status) count++;
    if (filters.value.id_type) count++;
    if (filters.value.credit_score_min || filters.value.credit_score_max) count++;
    return count;
});

const hasActiveFilters = computed(() => {
    return activeFilterCount.value > 0;
});

const activeLoansCount = computed(() => {
    return props.customers.data.filter(c => (c.loans_count || 0) > 0).length;
});

const goodCreditCount = computed(() => {
    return props.customers.data.filter(c => c.credit_score && c.credit_score >= 670).length;
});

const newThisMonthCount = computed(() => {
    const currentMonth = new Date().getMonth();
    const currentYear = new Date().getFullYear();
    return props.customers.data.filter(customer => {
        const createdDate = new Date(customer.created_at);
        return createdDate.getMonth() === currentMonth && 
               createdDate.getFullYear() === currentYear;
    }).length;
});

// Debounced search function
const handleSearchInput = () => {
    // Clear existing timeout
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
    
    // Set new timeout
    searchTimeout = setTimeout(() => {
        filterCustomers();
    }, 300); // 300ms delay
};

// Filter functions
const filterCustomers = () => {
    // Clear any pending timeout
    if (searchTimeout) {
        clearTimeout(searchTimeout);
        searchTimeout = null;
    }
    
    router.get(route('customers.index'), filters.value, {
        preserveState: true,
        replace: true,
    });
};

const clearSearch = () => {
    filters.value.search = '';
    if (searchTimeout) {
        clearTimeout(searchTimeout);
        searchTimeout = null;
    }
    filterCustomers();
};

const clearFilter = (filterName) => {
    filters.value[filterName] = '';
    filterCustomers();
};

const clearCreditScoreFilter = () => {
    filters.value.credit_score_min = '';
    filters.value.credit_score_max = '';
    filterCustomers();
};

const clearAllFilters = () => {
    filters.value = {
        search: '',
        employment_status: '',
        id_type: '',
        credit_score_min: '',
        credit_score_max: ''
    };
    showFilterPanel.value = false;
    
    if (searchTimeout) {
        clearTimeout(searchTimeout);
        searchTimeout = null;
    }
    
    filterCustomers();
};

const toggleFilterPanel = () => {
    showFilterPanel.value = !showFilterPanel.value;
};

// Helper functions
const getFullName = (customer) => {
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

const formatCurrency = (amount) => {
    if (!amount) return '0.00';
    return new Intl.NumberFormat('en-GH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(amount);
};

const formatEmploymentStatus = (status) => {
    if (!status) return 'Not Specified';
    return status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
};

const employmentStatusClass = (status) => {
    const classes = {
        'employed': 'bg-green-100 text-green-800',
        'self-employed': 'bg-blue-100 text-blue-800',
        'unemployed': 'bg-yellow-100 text-yellow-800',
        'retired': 'bg-purple-100 text-purple-800',
        'student': 'bg-indigo-100 text-indigo-800'
    };
    return classes[status] || 'bg-gray-100 text-gray-800';
};

const creditScoreBadgeClass = (score) => {
    if (!score) return 'bg-gray-100 text-gray-800';
    if (score >= 750) return 'bg-green-100 text-green-800';
    if (score >= 670) return 'bg-yellow-100 text-yellow-800';
    if (score >= 580) return 'bg-orange-100 text-orange-800';
    return 'bg-red-100 text-red-800';
};

const creditScoreTextClass = (score) => {
    if (!score) return 'text-gray-500';
    if (score >= 750) return 'text-green-600 font-semibold';
    if (score >= 670) return 'text-yellow-600 font-semibold';
    if (score >= 580) return 'text-orange-600 font-semibold';
    return 'text-red-600 font-semibold';
};

const createLoanForCustomer = (customer) => {
    router.visit(route('loans.create'), {
        data: { customer_id: customer.id }
    });
};

const exportCustomers = () => {
    // Implement export functionality
    alert('Export functionality will be implemented here');
};

// Cleanup on unmount
onUnmounted(() => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }
});
</script>