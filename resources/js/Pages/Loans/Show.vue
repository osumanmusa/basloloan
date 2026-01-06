<template>
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <div class="flex items-center space-x-3">
                        <div :class="statusBadgeClass(loan.status)" class="px-4 py-1.5 text-sm font-bold rounded-full">
                            {{ formatStatus(loan.status) }}
                        </div>
                        <div>
                            <h1 class="font-bold text-2xl text-gray-900 leading-tight">
                                Loan #{{ loan.id }}
                            </h1>
                            <div class="flex items-center space-x-4 mt-1">
                                <p class="text-sm text-gray-600">
                                    Created {{ formatDate(loan.created_at) }}
                                </p>
                                <span class="text-gray-400">•</span>
                                <p class="text-sm text-gray-600">
                                    {{ loan.customer.first_name }} {{ loan.customer.last_name }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <SecondaryButton @click="$inertia.visit(route('loans.index'))">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Loans
                    </SecondaryButton>
                    <PrimaryButton 
                        v-if="$page.props.auth.can['edit loans'] && loan.status === 'pending'"
                        @click="$inertia.visit(route('loans.edit', loan.id))"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Loan
                    </PrimaryButton>
                    <button 
                        v-if="$page.props.auth.can['delete loans'] && loan.status === 'pending'"
                        @click="deleteLoan"
                        class="inline-flex items-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 shadow-sm"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete
                    </button>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Success Message -->
                <div v-if="$page.props.flash.success" class="mb-8 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 text-green-800 px-6 py-4 rounded-xl shadow-sm">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-medium">{{ $page.props.flash.success }}</span>
                    </div>
                </div>

                <!-- Error Message -->
                <div v-if="$page.props.flash.error" class="mb-8 bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 text-red-800 px-6 py-4 rounded-xl shadow-sm">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="font-medium">{{ $page.props.flash.error }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left Column - Loan Details & Actions -->
                    <div class="lg:col-span-2 space-y-8">
                        <!-- Loan Overview Card -->
                        <div class="bg-white overflow-hidden rounded-2xl shadow-lg border border-gray-100">
                            <div class="p-8">
                                <div class="flex items-center justify-between mb-6">
                                    <h2 class="text-xl font-bold text-gray-900">Loan Overview</h2>
                                    <div class="text-sm text-gray-500">
                                        Created by {{ loan.creator?.name || 'System' }}
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                    <!-- Amount Section -->
                                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 p-6 rounded-xl border border-blue-100">
                                        <div class="flex items-center mb-4">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm font-medium text-gray-600">Loan Amount</p>
                                                <p class="text-2xl font-bold text-gray-900">GH₵{{ loan.amount.toLocaleString() }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Interest Section -->
                                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-6 rounded-xl border border-green-100">
                                        <div class="flex items-center mb-4">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-lg bg-green-100 flex items-center justify-center">
                                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                                </svg>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm font-medium text-gray-600">Interest Rate</p>
                                                <p class="text-2xl font-bold text-gray-900">{{ loan.interest_rate }}%</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Term Section -->
                                    <div class="bg-gradient-to-br from-purple-50 to-violet-50 p-6 rounded-xl border border-purple-100">
                                        <div class="flex items-center mb-4">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-lg bg-purple-100 flex items-center justify-center">
                                                <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                            <div class="ml-4">
                                                <p class="text-sm font-medium text-gray-600">Loan Term</p>
                                                <p class="text-2xl font-bold text-gray-900">{{ loan.term_months }} months</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Loan Details Grid -->
                                <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-6">
                                    <div>
                                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Loan Type</p>
                                        <p class="text-sm font-semibold text-gray-900 mt-1 capitalize">{{ loan.type }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Purpose</p>
                                        <p class="text-sm font-semibold text-gray-900 mt-1 capitalize">{{ loan.purpose?.replace('_', ' ') }}</p>
                                    </div>
                                    <div v-if="loan.monthly_payment">
                                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Monthly Payment</p>
                                        <p class="text-sm font-semibold text-gray-900 mt-1">GH₵{{ loan.monthly_payment.toLocaleString() }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">Created</p>
                                        <p class="text-sm font-semibold text-gray-900 mt-1">{{ formatDate(loan.created_at) }}</p>
                                    </div>
                                </div>

                                <!-- Purpose Description -->
                                <div class="mt-8 pt-6 border-t border-gray-200" v-if="loan.purpose_description">
                                    <p class="text-sm font-medium text-gray-700 mb-2">Purpose Description</p>
                                    <p class="text-gray-600 bg-gray-50 p-4 rounded-lg border border-gray-100">{{ loan.purpose_description }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Guarantor Information Card -->
                        <div class="bg-white overflow-hidden rounded-2xl shadow-lg border border-gray-100" v-if="loan.guarantor">
                            <div class="p-8">
                                <div class="flex items-center mb-6">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-lg bg-purple-100 flex items-center justify-center">
                                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                    </div>
                                    <h2 class="ml-4 text-xl font-bold text-gray-900">Guarantor Information</h2>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <p class="text-sm font-medium text-gray-600">Full Name</p>
                                        <p class="text-lg font-semibold text-gray-900 mt-1">{{ loan.guarantor.full_name }}</p>
                                    </div>
                                    
                                    <div>
                                        <p class="text-sm font-medium text-gray-600">Relationship</p>
                                        <p class="text-lg font-semibold text-gray-900 mt-1 capitalize">{{ loan.guarantor.relationship }}</p>
                                    </div>
                                    
                                    <div>
                                        <p class="text-sm font-medium text-gray-600">Phone Number</p>
                                        <div class="flex items-center mt-1">
                                            <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                            <p class="text-lg font-semibold text-gray-900">{{ loan.guarantor.phone }}</p>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <p class="text-sm font-medium text-gray-600">Email Address</p>
                                        <p class="text-lg font-semibold text-gray-900 mt-1">{{ loan.guarantor.email || 'Not provided' }}</p>
                                    </div>
                                    
                                    <div>
                                        <p class="text-sm font-medium text-gray-600">ID Type</p>
                                        <p class="text-lg font-semibold text-gray-900 mt-1">{{ formatIdType(loan.guarantor.id_type) }}</p>
                                    </div>
                                    
                                    <div>
                                        <p class="text-sm font-medium text-gray-600">ID Number</p>
                                        <p class="text-lg font-semibold text-gray-900 mt-1">{{ loan.guarantor.id_number }}</p>
                                    </div>
                                </div>

                                <!-- Address -->
                                <div class="mt-6">
                                    <p class="text-sm font-medium text-gray-600">Address</p>
                                    <p class="text-gray-600 mt-2 bg-gray-50 p-4 rounded-lg border border-gray-100">{{ loan.guarantor.address }}</p>
                                </div>

                                <!-- Documents Section -->
                                <div class="mt-8 pt-6 border-t border-gray-200">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Guarantor Documents</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <!-- ID Front -->
                                        <div v-if="loan.guarantor.id_front_path" class="bg-gray-50 p-4 rounded-xl border border-gray-200 hover:border-blue-300 transition-colors">
                                            <div class="flex items-center mb-3">
                                                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <svg class="h-4 w-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                                <span class="ml-2 text-sm font-medium text-gray-900">ID Front</span>
                                            </div>
                                            <a :href="loan.guarantor.id_front_url" target="_blank" 
                                               class="inline-flex items-center text-sm text-blue-600 hover:text-blue-700">
                                                View Document
                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                </svg>
                                            </a>
                                        </div>

                                        <!-- ID Back -->
                                        <div v-if="loan.guarantor.id_back_path" class="bg-gray-50 p-4 rounded-xl border border-gray-200 hover:border-blue-300 transition-colors">
                                            <div class="flex items-center mb-3">
                                                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <svg class="h-4 w-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                                <span class="ml-2 text-sm font-medium text-gray-900">ID Back</span>
                                            </div>
                                            <a :href="loan.guarantor.id_back_url" target="_blank" 
                                               class="inline-flex items-center text-sm text-blue-600 hover:text-blue-700">
                                                View Document
                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                </svg>
                                            </a>
                                        </div>

                                        <!-- Passport Photo -->
                                        <div v-if="loan.guarantor.passport_picture_path" class="bg-gray-50 p-4 rounded-xl border border-gray-200 hover:border-purple-300 transition-colors">
                                            <div class="flex items-center mb-3">
                                                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-purple-100 flex items-center justify-center">
                                                    <svg class="h-4 w-4 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 005 10a6 6 0 0112 0c0 .459-.031.907-.086 1.333A5 5 0 0010 11z" clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                                <span class="ml-2 text-sm font-medium text-gray-900">Passport Photo</span>
                                            </div>
                                            <a :href="loan.guarantor.passport_picture_url" target="_blank" 
                                               class="inline-flex items-center text-sm text-purple-600 hover:text-purple-700">
                                                View Photo
                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons Card -->
                        <div class="bg-white overflow-hidden rounded-2xl shadow-lg border border-gray-100">
                            <div class="p-8">
                                <h2 class="text-xl font-bold text-gray-900 mb-6">Loan Actions</h2>
                                <div class="flex flex-wrap gap-3">
                                    <!-- Edit Button -->
                                    <PrimaryButton 
                                        v-if="loan.status === 'pending'"
                                        @click="$inertia.visit(route('loans.edit', loan.id))"
                                        class="px-6 py-3"
                                    >
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit Loan
                                    </PrimaryButton>

                                    <!-- Approve Button -->
                                    <button 
                                        v-if="loan.status === 'pending' && $page.props.auth.can['approve-loans']"
                                        @click="approveLoan"
                                        class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 shadow-sm"
                                    >
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Approve Loan
                                    </button>

                                    <!-- Reject Button -->
                                    <button 
                                        v-if="loan.status === 'pending' && $page.props.auth.can['approve loans']"
                                        @click="rejectLoan"
                                        class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 shadow-sm"
                                    >
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                        Reject Loan
                                    </button>

                                    <!-- Disburse Button -->
                                    <button 
                                        v-if="loan.status === 'approved' && $page.props.auth.can['disburse-loans']"
                                        @click="disburseLoan"
                                        class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-purple-500 to-violet-600 hover:from-purple-600 hover:to-violet-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-200 shadow-sm"
                                    >
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0 6v1m0-1v1m6-10h2m-10 0h2m-1 14h1"/>
                                        </svg>
                                        Disburse Loan
                                    </button>

                                    <!-- Delete Button -->
                                    <button 
                                        v-if="loan.status === 'pending' && $page.props.auth.can['delete loans']"
                                        @click="deleteLoan"
                                        class="inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-red-500 to-rose-600 hover:from-red-600 hover:to-rose-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 shadow-sm"
                                    >
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Delete Loan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Customer & Summary -->
                    <div class="space-y-8">
                        <!-- Customer Information Card -->
                        <div class="bg-white overflow-hidden rounded-2xl shadow-lg border border-gray-100">
                            <div class="p-8">
                                <div class="flex items-center mb-6">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                    <h2 class="ml-4 text-xl font-bold text-gray-900">Customer</h2>
                                </div>
                                
                                <div class="flex items-center mb-6">
                                    <div class="flex-shrink-0 h-16 w-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-md">
                                        {{ getInitials(loan.customer.first_name + ' ' + loan.customer.last_name) }}
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-lg font-bold text-gray-900">
                                            {{ loan.customer.first_name }} {{ loan.customer.last_name }}
                                        </h3>
                                        <p class="text-sm text-gray-600">{{ loan.customer.email }}</p>
                                        <div class="flex items-center mt-1">
                                            <svg class="h-4 w-4 text-gray-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                            <span class="text-sm text-gray-600">{{ loan.customer.phone }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">ID Number</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ loan.customer.id_number }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Employment</span>
                                        <span class="text-sm font-semibold text-gray-900 capitalize">{{ loan.customer.employment_status?.replace('_', ' ') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center" v-if="loan.customer.monthly_income">
                                        <span class="text-sm text-gray-600">Monthly Income</span>
                                        <span class="text-sm font-semibold text-gray-900">GH₵{{ loan.customer.monthly_income.toLocaleString() }}</span>
                                    </div>
                                    <div class="flex justify-between items-center" v-if="loan.customer.date_of_birth">
                                        <span class="text-sm text-gray-600">Date of Birth</span>
                                        <span class="text-sm font-semibold text-gray-900">{{ formatDate(loan.customer.date_of_birth) }}</span>
                                    </div>
                                </div>
                                
                                <div class="mt-6">
                                    <Link 
                                        :href="route('customers.show', loan.customer.id)"
                                        class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-700"
                                    >
                                        View Customer Profile
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                        </svg>
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- Loan Summary Card -->
                        <div class="bg-white overflow-hidden rounded-2xl shadow-lg border border-gray-100">
                            <div class="p-8">
                                <div class="flex items-center mb-6">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                                        <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                    </div>
                                    <h2 class="ml-4 text-xl font-bold text-gray-900">Loan Summary</h2>
                                </div>
                                
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center py-3 border-b border-gray-100">
                                        <span class="text-sm text-gray-600">Principal Amount</span>
                                        <span class="text-lg font-bold text-gray-900">GH₵{{ loan.amount.toLocaleString() }}</span>
                                    </div>
                                    
                                    <div class="flex justify-between items-center py-3 border-b border-gray-100" v-if="loan.total_amount">
                                        <span class="text-sm text-gray-600">Total Interest</span>
                                        <span class="text-lg font-bold text-gray-900">GH₵{{ (loan.total_amount - loan.amount).toLocaleString() }}</span>
                                    </div>
                                    
                                    <div class="flex justify-between items-center py-3 border-b border-gray-100" v-if="loan.total_amount">
                                        <span class="text-sm text-gray-600">Total Payment</span>
                                        <span class="text-lg font-bold text-gray-900">GH₵{{ loan.total_amount.toLocaleString() }}</span>
                                    </div>
                                    
                                    <div class="flex justify-between items-center py-3" v-if="loan.remaining_balance">
                                        <span class="text-sm text-gray-600">Remaining Balance</span>
                                        <span class="text-lg font-bold text-gray-900">GH₵{{ loan.remaining_balance.toLocaleString() }}</span>
                                    </div>
                                    
                                    <div class="mt-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border border-blue-100" v-if="!loan.total_amount">
                                        <div class="flex items-center">
                                            <svg class="h-5 w-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p class="text-sm text-blue-700">Loan calculation pending disbursement</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline Card -->
                        <div class="bg-white overflow-hidden rounded-2xl shadow-lg border border-gray-100">
                            <div class="p-8">
                                <h2 class="text-xl font-bold text-gray-900 mb-6">Loan Timeline</h2>
                                
                                <div class="space-y-4">
                                    <!-- Created -->
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                            <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-semibold text-gray-900">Loan Created</p>
                                            <p class="text-sm text-gray-600">{{ formatDate(loan.created_at) }}</p>
                                            <p class="text-xs text-gray-500 mt-1">By {{ loan.creator?.name || 'System' }}</p>
                                        </div>
                                    </div>

                                    <!-- Approved -->
                                    <div class="flex items-start" v-if="loan.approved_at">
                                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                            <svg class="h-4 w-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-semibold text-gray-900">Loan Approved</p>
                                            <p class="text-sm text-gray-600">{{ formatDate(loan.approved_at) }}</p>
                                            <p class="text-xs text-gray-500 mt-1">By {{ loan.approver?.name || 'Admin' }}</p>
                                        </div>
                                    </div>

                                    <!-- Disbursed -->
                                    <div class="flex items-start" v-if="loan.disbursement_date">
                                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-purple-100 flex items-center justify-center">
                                            <svg class="h-4 w-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v1m0 6v1m0-1v1m6-10h2m-10 0h2m-1 14h1" />
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-semibold text-gray-900">Loan Disbursed</p>
                                            <p class="text-sm text-gray-600">{{ formatDate(loan.disbursement_date) }}</p>
                                        </div>
                                    </div>

                                    <!-- Due Date -->
                                    <div class="flex items-start" v-if="loan.due_date">
                                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-yellow-100 flex items-center justify-center">
                                            <svg class="h-4 w-4 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-semibold text-gray-900">Due Date</p>
                                            <p class="text-sm text-gray-600">{{ formatDate(loan.due_date) }}</p>
                                        </div>
                                    </div>
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
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
    loan: Object
});

const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const formatStatus = (status) => {
    return status.replace(/_/g, ' ').toUpperCase();
};

const formatIdType = (idType) => {
    const types = {
        'ghana_card': 'Ghana Card',
        'voters_id': "Voter's ID",
        'drivers_license': "Driver's License",
        'passport': 'Passport',
        'national_id': 'National ID'
    };
    return types[idType] || idType;
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

const getInitials = (fullName) => {
    if (!fullName) return '';
    return fullName
        .split(' ')
        .map(name => name[0])
        .join('')
        .toUpperCase()
        .slice(0, 2);
};

const deleteLoan = () => {
    if (confirm('Are you sure you want to delete this loan? This action cannot be undone.')) {
        router.delete(route('loans.destroy', props.loan.id));
    }
};

const approveLoan = () => {
    if (confirm('Are you sure you want to approve this loan?')) {
        router.put(route('loans.approve', props.loan.id));
    }
};

const rejectLoan = () => {
    if (confirm('Are you sure you want to reject this loan?')) {
        router.put(route('loans.reject', props.loan.id));
    }
};

const disburseLoan = () => {
    if (confirm('Are you sure you want to disburse this loan?')) {
        router.put(route('loans.disburse', props.loan.id));
    }
};
</script>

<style scoped>
/* Add some smooth transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}

/* Custom shadow on hover */
.shadow-lg {
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.shadow-lg:hover {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}
</style>