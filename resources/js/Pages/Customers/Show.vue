<template>
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <div class="flex items-center space-x-3">
                        <div class="relative">
                            <img v-if="customer.passport_picture_url" 
                                 :src="customer.passport_picture_url" 
                                 alt="Profile" 
                                 class="w-12 h-12 rounded-full border-2 border-white shadow-sm object-cover"
                                 @error="handleImageError('passport')">
                            <div v-else class="w-12 h-12 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-lg shadow-sm">
                                {{ customer.first_name.charAt(0) }}{{ customer.last_name.charAt(0) }}
                            </div>
                            <span v-if="customer.credit_score" class="absolute -bottom-1 -right-1 bg-green-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full">
                                {{ customer.credit_score }}
                            </span>
                        </div>
                        <div>
                            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                                {{ customer.first_name }} {{ customer.last_name }}
                            </h2>
                            <div class="flex items-center space-x-2 text-sm text-gray-600 mt-1">
                                <span>{{ customer.id_type }}: {{ customer.id_number }}</span>
                                <span class="text-gray-400">•</span>
                                <span>{{ customer.email }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <SecondaryButton @click="$inertia.visit(route('customers.index'))">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Back
                    </SecondaryButton>
                    <PrimaryButton 
                        @click="$inertia.visit(route('customers.edit', customer.id))"
                        class="flex items-center space-x-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Profile
                    </PrimaryButton>
                    <PrimaryButton 
                        @click="$inertia.visit(route('loans.create'), { data: { customer_id: customer.id } })"
                        class="flex items-center space-x-2 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 border-transparent"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        New Loan
                    </PrimaryButton>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Customer Stats -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-5 rounded-xl shadow-sm border border-blue-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-medium text-blue-700">Total Loans</div>
                                <div class="text-2xl font-bold text-gray-900 mt-1">{{ stats.total_loans }}</div>
                            </div>
                            <div class="bg-blue-500 p-2 rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-green-50 to-green-100 p-5 rounded-xl shadow-sm border border-green-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-medium text-green-700">Active Loans</div>
                                <div class="text-2xl font-bold text-gray-900 mt-1">{{ stats.active_loans }}</div>
                            </div>
                            <div class="bg-green-500 p-2 rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-5 rounded-xl shadow-sm border border-purple-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-medium text-purple-700">Total Borrowed</div>
                                <div class="text-2xl font-bold text-gray-900 mt-1">GH₵ {{ formatCurrency(stats.total_borrowed) }}</div>
                            </div>
                            <div class="bg-purple-500 p-2 rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 p-5 rounded-xl shadow-sm border border-yellow-100">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm font-medium text-yellow-700">Pending Loans</div>
                                <div class="text-2xl font-bold text-gray-900 mt-1">{{ stats.pending_loans }}</div>
                            </div>
                            <div class="bg-yellow-500 p-2 rounded-lg">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Customer Details -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Personal Information Card -->
                        <div class="bg-white overflow-hidden rounded-xl shadow-sm border border-gray-200">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-100">Personal Information</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-1">
                                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Full Name</label>
                                        <p class="text-sm font-medium text-gray-900">{{ customer.first_name }} {{ customer.last_name }}</p>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Email</label>
                                        <p class="text-sm font-medium text-gray-900">{{ customer.email }}</p>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</label>
                                        <p class="text-sm font-medium text-gray-900">{{ customer.phone }}</p>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Date of Birth</label>
                                        <div class="flex items-center space-x-2">
                                            <p class="text-sm font-medium text-gray-900">{{ formatDate(customer.date_of_birth) }}</p>
                                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded">
                                                {{ calculateAge(customer.date_of_birth) }} years
                                            </span>
                                        </div>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">ID Type</label>
                                        <p class="text-sm font-medium text-gray-900">{{ customer.id_type }}</p>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">ID Number</label>
                                        <p class="text-sm font-medium text-gray-900">{{ customer.id_number }}</p>
                                    </div>
                                    <div class="md:col-span-2 space-y-1">
                                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Address</label>
                                        <p class="text-sm font-medium text-gray-900">{{ customer.address }}</p>
                                        <p class="text-sm text-gray-600">{{ customer.city }}, {{ customer.state }} {{ customer.zip_code }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ID Documents Card -->
                        <div class="bg-white overflow-hidden rounded-xl shadow-sm border border-gray-200">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-100">ID Documents</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- Passport Picture -->
                                    <div class="space-y-2">
                                        <label class="text-sm font-medium text-gray-700">Passport Picture</label>
                                        <div class="relative aspect-square border-2 border-dashed border-gray-300 rounded-lg overflow-hidden hover:border-blue-400 transition-colors">
                                            <img v-if="customer.passport_picture_url" 
                                                 :src="customer.passport_picture_url" 
                                                 alt="Passport Picture" 
                                                 class="w-full h-full object-cover"
                                                 @error="handleImageError('passport')">
                                            <div v-else class="w-full h-full flex flex-col items-center justify-center bg-gray-50 p-4">
                                                <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <span class="text-xs text-gray-500 text-center">No passport picture uploaded</span>
                                            </div>
                                            <div v-if="customer.passport_picture_url" class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 hover:opacity-100 transition-opacity">
                                                <a :href="customer.passport_picture_url" target="_blank" class="absolute bottom-2 right-2 bg-white p-1.5 rounded-full shadow hover:bg-gray-100">
                                                    <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ID Front -->
                                    <div class="space-y-2">
                                        <label class="text-sm font-medium text-gray-700">ID Front</label>
                                        <div class="relative aspect-video border-2 border-dashed border-gray-300 rounded-lg overflow-hidden hover:border-blue-400 transition-colors">
                                            <img v-if="customer.id_front_url" 
                                                 :src="customer.id_front_url" 
                                                 :alt="customer.id_type + ' Front'" 
                                                 class="w-full h-full object-contain bg-gray-50"
                                                 @error="handleImageError('id_front')">
                                            <div v-else class="w-full h-full flex flex-col items-center justify-center bg-gray-50 p-4">
                                                <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <span class="text-xs text-gray-500 text-center">No ID front uploaded</span>
                                            </div>
                                            <div v-if="customer.id_front_url" class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 hover:opacity-100 transition-opacity">
                                                <a :href="customer.id_front_url" target="_blank" class="absolute bottom-2 right-2 bg-white p-1.5 rounded-full shadow hover:bg-gray-100">
                                                    <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- ID Back -->
                                    <div class="space-y-2">
                                        <label class="text-sm font-medium text-gray-700">ID Back</label>
                                        <div class="relative aspect-video border-2 border-dashed border-gray-300 rounded-lg overflow-hidden hover:border-blue-400 transition-colors">
                                            <img v-if="customer.id_back_url" 
                                                 :src="customer.id_back_url" 
                                                 :alt="customer.id_type + ' Back'" 
                                                 class="w-full h-full object-contain bg-gray-50"
                                                 @error="handleImageError('id_back')">
                                            <div v-else class="w-full h-full flex flex-col items-center justify-center bg-gray-50 p-4">
                                                <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                                <span class="text-xs text-gray-500 text-center">No ID back uploaded</span>
                                            </div>
                                            <div v-if="customer.id_back_url" class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 hover:opacity-100 transition-opacity">
                                                <a :href="customer.id_back_url" target="_blank" class="absolute bottom-2 right-2 bg-white p-1.5 rounded-full shadow hover:bg-gray-100">
                                                    <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Financial Information Card -->
                        <div class="bg-white overflow-hidden rounded-xl shadow-sm border border-gray-200">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-100">Financial Information</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="space-y-1">
                                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Monthly Income</label>
                                        <p v-if="customer.monthly_income" class="text-sm font-medium text-gray-900">
                                            GH₵ {{ formatCurrency(customer.monthly_income) }}
                                        </p>
                                        <p v-else class="text-sm text-gray-500">Not provided</p>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Employment Status</label>
                                        <div class="flex items-center">
                                            <span :class="employmentStatusClass(customer.employment_status)" 
                                                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                                {{ formatEmploymentStatus(customer.employment_status) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div v-if="customer.employment_details" class="md:col-span-2 space-y-1">
                                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Employment Details</label>
                                        <p class="text-sm text-gray-900">{{ customer.employment_details }}</p>
                                    </div>
                                    <div class="space-y-1">
                                        <label class="text-xs font-medium text-gray-500 uppercase tracking-wider">Credit Score</label>
                                        <div class="flex items-center">
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-2">
                                                    <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                                                        <div :class="creditScoreColor(customer.credit_score)" 
                                                             :style="{ width: creditScorePercentage(customer.credit_score) + '%' }"
                                                             class="h-full rounded-full"></div>
                                                    </div>
                                                    <span v-if="customer.credit_score" class="text-sm font-bold" :class="creditScoreTextColor(customer.credit_score)">
                                                        {{ customer.credit_score }}
                                                    </span>
                                                    <span v-else class="text-sm text-gray-500">N/A</span>
                                                </div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    {{ creditScoreLabel(customer.credit_score) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Loans & Quick Actions -->
                    <div class="space-y-6">
                        <!-- Recent Loans Card -->
                        <div class="bg-white overflow-hidden rounded-xl shadow-sm border border-gray-200">
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-4 pb-2 border-b border-gray-100">
                                    <h3 class="text-lg font-semibold text-gray-900">Recent Loans</h3>
                                    <Link 
                                        :href="route('loans.index', { customer: customer.id })"
                                        class="text-xs font-medium text-blue-600 hover:text-blue-800"
                                    >
                                        View All
                                    </Link>
                                </div>
                                
                                <div class="space-y-3">
                                    <div v-if="recentLoans.length === 0" class="text-center py-6">
                                        <div class="mx-auto w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                            </svg>
                                        </div>
                                        <p class="text-sm text-gray-500">No loans found for this customer</p>
                                        <PrimaryButton 
                                            @click="$inertia.visit(route('loans.create'), { data: { customer_id: customer.id } })"
                                            class="mt-3 text-xs"
                                        >
                                            Create First Loan
                                        </PrimaryButton>
                                    </div>
                                    
                                    <div v-for="loan in recentLoans" :key="loan.id"
                                         class="group border border-gray-200 rounded-lg p-4 hover:border-blue-200 hover:bg-blue-50 transition-all duration-200 cursor-pointer"
                                         @click="$inertia.visit(route('loans.show', loan.id))">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <p class="text-base font-bold text-gray-900">
                                                    GH₵ {{ formatCurrency(loan.amount) }}
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    {{ loan.purpose }}
                                                </p>
                                            </div>
                                            <span :class="statusBadgeClass(loan.status)" 
                                                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                                {{ formatLoanStatus(loan.status) }}
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between text-xs text-gray-400">
                                            <span>{{ formatDate(loan.created_at) }}</span>
                                            <span class="opacity-0 group-hover:opacity-100 transition-opacity text-blue-600">
                                                View →
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions Card -->
                        <div class="bg-gradient-to-br from-blue-50 to-blue-100 overflow-hidden rounded-xl shadow-sm border border-blue-200">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                                
                                <div class="space-y-2">
                                    <button @click="$inertia.visit(route('loans.create'), { data: { customer_id: customer.id } })"
                                            class="w-full flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-all duration-200">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                </svg>
                                            </div>
                                            <div class="text-left">
                                                <p class="text-sm font-medium text-gray-900">New Loan</p>
                                                <p class="text-xs text-gray-500">Create a new loan application</p>
                                            </div>
                                        </div>
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </button>
                                    
                                    <button @click="$inertia.visit(route('customers.edit', customer.id))"
                                            class="w-full flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-all duration-200">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center">
                                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </div>
                                            <div class="text-left">
                                                <p class="text-sm font-medium text-gray-900">Edit Profile</p>
                                                <p class="text-xs text-gray-500">Update customer information</p>
                                            </div>
                                        </div>
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </button>
                                    
                                    <button @click="downloadCustomerInfo"
                                            class="w-full flex items-center justify-between p-3 bg-white rounded-lg border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-all duration-200">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center">
                                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                </svg>
                                            </div>
                                            <div class="text-left">
                                                <p class="text-sm font-medium text-gray-900">Download Info</p>
                                                <p class="text-xs text-gray-500">Export customer details</p>
                                            </div>
                                        </div>
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </button>
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
import { ref } from 'vue';

const props = defineProps({
    customer: Object,
    stats: Object,
    recentLoans: Array
});

const imageErrors = ref({
    passport: false,
    id_front: false,
    id_back: false
});

const handleImageError = (type) => {
    imageErrors.value[type] = true;
};

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

const formatLoanStatus = (status) => {
    if (!status) return '';
    return status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
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

const creditScorePercentage = (score) => {
    if (!score) return 0;
    return ((score - 300) / (850 - 300)) * 100;
};

const creditScoreColor = (score) => {
    if (!score) return 'bg-gray-300';
    if (score >= 750) return 'bg-green-500';
    if (score >= 670) return 'bg-yellow-500';
    if (score >= 580) return 'bg-orange-500';
    return 'bg-red-500';
};

const creditScoreTextColor = (score) => {
    if (!score) return 'text-gray-500';
    if (score >= 750) return 'text-green-600';
    if (score >= 670) return 'text-yellow-600';
    if (score >= 580) return 'text-orange-600';
    return 'text-red-600';
};

const creditScoreLabel = (score) => {
    if (!score) return 'No credit score';
    if (score >= 750) return 'Excellent';
    if (score >= 670) return 'Good';
    if (score >= 580) return 'Fair';
    return 'Poor';
};

const downloadCustomerInfo = () => {
    // Implement customer info download functionality
    alert('Download functionality will be implemented here');
};
</script>