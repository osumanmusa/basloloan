<template>
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Create New Loan
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Create a new loan application</p>
                </div>
                <SecondaryButton @click="$inertia.visit(route('loans.index'))">
                    Back to Loans
                </SecondaryButton>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit">
                            <!-- Loan Details Section -->
                            <div class="mb-10">
                                <div class="flex items-center mb-6 pb-3 border-b border-gray-200">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-lg font-bold text-gray-900">Loan Details</h3>
                                        <p class="text-sm text-gray-600">Basic information about the loan</p>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Customer Selection -->
                                    <div class="md:col-span-2">
                                        <label for="customer_id" class="block text-sm font-bold text-gray-900 mb-2">
                                            Customer <span class="text-red-500">*</span>
                                        </label>
                                        <select 
                                            id="customer_id"
                                            v-model="form.customer_id"
                                            required
                                            class="mt-1 w-full rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-150 ease-in-out"
                                            :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-200': errors.customer_id }"
                                        >
                                            <option value="">Select a customer</option>
                                            <option 
                                                v-for="customer in customers" 
                                                :key="customer.id" 
                                                :value="customer.id"
                                            >
                                                {{ customer.full_name }} - {{ customer.email }}
                                            </option>
                                        </select>
                                        <p v-if="errors.customer_id" class="mt-2 text-sm text-red-600">
                                            {{ errors.customer_id }}
                                        </p>
                                    </div>

                                    <!-- Loan Amount -->
                                    <div>
                                        <label for="amount" class="block text-sm font-bold text-gray-900 mb-2">
                                            Loan Amount ($) <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative mt-1">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-700 font-medium">$</span>
                                            </div>
                                            <input 
                                                id="amount"
                                                type="number"
                                                v-model="form.amount"
                                                required
                                                min="100"
                                                step="0.01"
                                                class="pl-10 w-full rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-150 ease-in-out"
                                                :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-200': errors.amount }"
                                                placeholder="0.00"
                                            >
                                        </div>
                                        <p v-if="errors.amount" class="mt-2 text-sm text-red-600">
                                            {{ errors.amount }}
                                        </p>
                                    </div>

                                    <!-- Interest Rate -->
                                    <div>
                                        <label for="interest_rate" class="block text-sm font-bold text-gray-900 mb-2">
                                            Interest Rate (%) <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative mt-1">
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <span class="text-gray-700 font-medium">%</span>
                                            </div>
                                            <input 
                                                id="interest_rate"
                                                type="number"
                                                v-model="form.interest_rate"
                                                required
                                                min="0.1"
                                                max="50"
                                                step="0.01"
                                                class="pr-10 w-full rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-150 ease-in-out"
                                                :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-200': errors.interest_rate }"
                                                placeholder="0.00"
                                            >
                                        </div>
                                        <p v-if="errors.interest_rate" class="mt-2 text-sm text-red-600">
                                            {{ errors.interest_rate }}
                                        </p>
                                    </div>

                                    <!-- Term Months -->
                                    <div>
                                        <label for="term_months" class="block text-sm font-bold text-gray-900 mb-2">
                                            Loan Term (Months) <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative mt-1">
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <span class="text-gray-700 font-medium">months</span>
                                            </div>
                                            <input 
                                                id="term_months"
                                                type="number"
                                                v-model="form.term_months"
                                                required
                                                min="1"
                                                max="360"
                                                class="pr-20 w-full rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-150 ease-in-out"
                                                :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-200': errors.term_months }"
                                                placeholder="12"
                                            >
                                        </div>
                                        <p v-if="errors.term_months" class="mt-2 text-sm text-red-600">
                                            {{ errors.term_months }}
                                        </p>
                                    </div>

                                    <!-- Loan Type -->
                                    <div>
                                        <label for="type" class="block text-sm font-bold text-gray-900 mb-2">
                                            Loan Type <span class="text-red-500">*</span>
                                        </label>
                                        <select 
                                            id="type"
                                            v-model="form.type"
                                            required
                                            class="mt-1 w-full rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-150 ease-in-out"
                                            :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-200': errors.type }"
                                        >
                                            <option value="">Select loan type</option>
                                            <option value="personal">Personal Loan</option>
                                            <option value="business">Business Loan</option>
                                            <option value="mortgage">Mortgage</option>
                                            <option value="auto">Auto Loan</option>
                                            <option value="education">Education Loan</option>
                                        </select>
                                        <p v-if="errors.type" class="mt-2 text-sm text-red-600">
                                            {{ errors.type }}
                                        </p>
                                    </div>

                                    <!-- Purpose -->
                                    <div>
                                        <label for="purpose" class="block text-sm font-bold text-gray-900 mb-2">
                                            Purpose <span class="text-red-500">*</span>
                                        </label>
                                        <select 
                                            id="purpose"
                                            v-model="form.purpose"
                                            required
                                            class="mt-1 w-full rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-150 ease-in-out"
                                            :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-200': errors.purpose }"
                                        >
                                            <option value="">Select purpose</option>
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
                                        <p v-if="errors.purpose" class="mt-2 text-sm text-red-600">
                                            {{ errors.purpose }}
                                        </p>
                                    </div>

                                    <!-- Purpose Description -->
                                    <div class="md:col-span-2">
                                        <label for="purpose_description" class="block text-sm font-bold text-gray-900 mb-2">
                                            Purpose Description
                                        </label>
                                        <textarea 
                                            id="purpose_description"
                                            v-model="form.purpose_description"
                                            rows="3"
                                            class="mt-1 w-full rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-150 ease-in-out"
                                            :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-200': errors.purpose_description }"
                                            placeholder="Describe the purpose of this loan in detail..."
                                        ></textarea>
                                        <p v-if="errors.purpose_description" class="mt-2 text-sm text-red-600">
                                            {{ errors.purpose_description }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Guarantor Information Section -->
                            <div class="mb-10">
                                <div class="flex items-center mb-6 pb-3 border-b border-gray-200">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                        <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-lg font-bold text-gray-900">Guarantor Information</h3>
                                        <p class="text-sm text-gray-600">Guarantor details and documents</p>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Guarantor Full Name -->
                                    <div class="md:col-span-2">
                                        <label for="guarantor_name" class="block text-sm font-bold text-gray-900 mb-2">
                                            Guarantor Full Name <span class="text-red-500">*</span>
                                        </label>
                                        <input 
                                            id="guarantor_name"
                                            type="text"
                                            v-model="form.guarantor.full_name"
                                            required
                                            class="mt-1 w-full rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-150 ease-in-out"
                                            :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-200': errors['guarantor.full_name'] }"
                                            placeholder="Enter guarantor's full name"
                                        >
                                        <p v-if="errors['guarantor.full_name']" class="mt-2 text-sm text-red-600">
                                            {{ errors['guarantor.full_name'] }}
                                        </p>
                                    </div>

                                    <!-- Email Address -->
                                    <div>
                                        <label for="guarantor_email" class="block text-sm font-bold text-gray-900 mb-2">
                                            Email Address
                                        </label>
                                        <input 
                                            id="guarantor_email"
                                            type="email"
                                            v-model="form.guarantor.email"
                                            class="mt-1 w-full rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-150 ease-in-out"
                                            :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-200': errors['guarantor.email'] }"
                                            placeholder="guarantor@example.com"
                                        >
                                        <p v-if="errors['guarantor.email']" class="mt-2 text-sm text-red-600">
                                            {{ errors['guarantor.email'] }}
                                        </p>
                                    </div>

                                    <!-- Phone Number -->
                                    <div>
                                        <label for="guarantor_phone" class="block text-sm font-bold text-gray-900 mb-2">
                                            Phone Number <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative mt-1">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                </svg>
                                            </div>
                                            <input 
                                                id="guarantor_phone"
                                                type="tel"
                                                v-model="form.guarantor.phone"
                                                required
                                                class="pl-10 w-full rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-150 ease-in-out"
                                                :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-200': errors['guarantor.phone'] }"
                                                placeholder="+233 XX XXX XXXX"
                                            >
                                        </div>
                                        <p v-if="errors['guarantor.phone']" class="mt-2 text-sm text-red-600">
                                            {{ errors['guarantor.phone'] }}
                                        </p>
                                    </div>

                                    <!-- ID Type and Number -->
                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-bold text-gray-900 mb-2">
                                            Identification Details <span class="text-red-500">*</span>
                                        </label>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-1">
                                            <div>
                                                <label for="id_type" class="block text-xs font-medium text-gray-700 mb-1">ID Type</label>
                                                <select 
                                                    id="id_type"
                                                    v-model="form.guarantor.id_type"
                                                    required
                                                    class="w-full rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-150 ease-in-out"
                                                    :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-200': errors['guarantor.id_type'] }"
                                                >
                                                    <option value="">Select ID Type</option>
                                                    <option value="ghana_card">Ghana Card</option>
                                                    <option value="voters_id">Voter's ID</option>
                                                    <option value="drivers_license">Driver's License</option>
                                                    <option value="passport">Passport</option>
                                                    <option value="national_id">National ID</option>
                                                </select>
                                                <p v-if="errors['guarantor.id_type']" class="mt-2 text-sm text-red-600">
                                                    {{ errors['guarantor.id_type'] }}
                                                </p>
                                            </div>
                                            <div>
                                                <label for="id_number" class="block text-xs font-medium text-gray-700 mb-1">ID Number</label>
                                                <input 
                                                    id="id_number"
                                                    type="text"
                                                    v-model="form.guarantor.id_number"
                                                    required
                                                    class="w-full rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-150 ease-in-out"
                                                    :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-200': errors['guarantor.id_number'] }"
                                                    :placeholder="getIDPlaceholder(form.guarantor.id_type)"
                                                >
                                                <p v-if="errors['guarantor.id_number']" class="mt-2 text-sm text-red-600">
                                                    {{ errors['guarantor.id_number'] }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Relationship -->
                                    <div>
                                        <label for="relationship" class="block text-sm font-bold text-gray-900 mb-2">
                                            Relationship to Applicant <span class="text-red-500">*</span>
                                        </label>
                                        <select 
                                            id="relationship"
                                            v-model="form.guarantor.relationship"
                                            required
                                            class="mt-1 w-full rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-150 ease-in-out"
                                            :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-200': errors['guarantor.relationship'] }"
                                        >
                                            <option value="">Select relationship</option>
                                            <option value="parent">Parent</option>
                                            <option value="spouse">Spouse</option>
                                            <option value="sibling">Sibling</option>
                                            <option value="friend">Friend</option>
                                            <option value="colleague">Colleague</option>
                                            <option value="relative">Relative</option>
                                            <option value="other">Other</option>
                                        </select>
                                        <p v-if="errors['guarantor.relationship']" class="mt-2 text-sm text-red-600">
                                            {{ errors['guarantor.relationship'] }}
                                        </p>
                                    </div>

                                    <!-- Address -->
                                    <div class="md:col-span-2">
                                        <label for="guarantor_address" class="block text-sm font-bold text-gray-900 mb-2">
                                            Address <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative mt-1">
                                            <div class="absolute top-3 left-3">
                                                <svg class="h-5 w-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                            </div>
                                            <textarea 
                                                id="guarantor_address"
                                                v-model="form.guarantor.address"
                                                required
                                                rows="3"
                                                class="pl-10 w-full rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-150 ease-in-out"
                                                :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-200': errors['guarantor.address'] }"
                                                placeholder="Enter guarantor's complete address including city and region"
                                            ></textarea>
                                        </div>
                                        <p v-if="errors['guarantor.address']" class="mt-2 text-sm text-red-600">
                                            {{ errors['guarantor.address'] }}
                                        </p>
                                    </div>

                                    <!-- Document Uploads Section -->
                                    <div class="md:col-span-2">
                                        <div class="flex items-center mb-6 pb-3 border-b border-gray-200">
                                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                                <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <h4 class="text-md font-bold text-gray-900">Guarantor Documents</h4>
                                                <p class="text-sm text-gray-600">Upload required identification documents</p>
                                            </div>
                                        </div>
                                        
                                        <div class="space-y-8">
                                            <!-- ID Front Upload -->
                                            <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                                                <div class="flex items-center justify-between mb-4">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-6 w-6 text-blue-600">
                                                            <svg fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                                            </svg>
                                                        </div>
                                                        <div class="ml-3">
                                                            <h5 class="font-bold text-gray-900">ID Front Side</h5>
                                                            <p class="text-xs text-gray-600 mt-1">Upload the front side of the ID card <span class="text-red-500">*</span></p>
                                                        </div>
                                                    </div>
                                                    <button
                                                        v-if="!form.guarantor.id_front"
                                                        type="button"
                                                        @click="$refs.idFrontInput.click()"
                                                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-150 ease-in-out"
                                                    >
                                                        Upload File
                                                    </button>
                                                </div>
                                                
                                                <!-- File Preview Area -->
                                                <div v-if="form.guarantor.id_front" class="mt-4">
                                                    <div class="flex items-start space-x-4">
                                                        <!-- Preview Container -->
                                                        <div class="relative">
                                                            <div class="w-40 h-40 rounded-lg overflow-hidden border-2 border-gray-200 bg-white shadow-sm">
                                                                <!-- Image Preview -->
                                                                <img 
                                                                    v-if="isImageFile(form.guarantor.id_front)"
                                                                    :src="getFilePreview(form.guarantor.id_front)"
                                                                    alt="ID Front Preview"
                                                                    class="w-full h-full object-contain p-2"
                                                                />
                                                                <!-- PDF Preview -->
                                                                <div v-else class="w-full h-full flex flex-col items-center justify-center bg-gray-50 p-4">
                                                                    <svg class="h-16 w-16 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                                                    </svg>
                                                                    <span class="mt-2 text-sm font-medium text-gray-700">PDF Document</span>
                                                                    <span class="text-xs text-gray-500 mt-1">{{ formatFileSize(form.guarantor.id_front.size) }}</span>
                                                                </div>
                                                            </div>
                                                            <!-- Remove Button -->
                                                            <button
                                                                type="button"
                                                                @click="form.guarantor.id_front = null"
                                                                class="absolute -top-2 -right-2 h-8 w-8 rounded-full bg-red-500 text-white flex items-center justify-center hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300 transition duration-150 ease-in-out"
                                                            >
                                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                        
                                                        <!-- File Info -->
                                                        <div class="flex-1">
                                                            <div class="flex items-center justify-between mb-2">
                                                                <p class="font-bold text-gray-900 truncate">{{ form.guarantor.id_front.name }}</p>
                                                                <button
                                                                    type="button"
                                                                    @click="$refs.idFrontInput.click()"
                                                                    class="text-sm font-medium text-blue-600 hover:text-blue-700"
                                                                >
                                                                    Change File
                                                                </button>
                                                            </div>
                                                            <div class="space-y-2">
                                                                <p class="text-sm text-gray-600">
                                                                    <span class="font-medium">Size:</span> {{ formatFileSize(form.guarantor.id_front.size) }}
                                                                </p>
                                                                <p class="text-sm text-gray-600">
                                                                    <span class="font-medium">Type:</span> {{ getFileType(form.guarantor.id_front) }}
                                                                </p>
                                                                <p class="text-sm text-gray-600">
                                                                    <span class="font-medium">Status:</span> 
                                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 ml-1">
                                                                        Uploaded
                                                                    </span>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Empty State -->
                                                <div v-else 
                                                    @click="$refs.idFrontInput.click()"
                                                    @dragover.prevent="dragOver = 'id_front'"
                                                    @dragleave.prevent="dragOver = null"
                                                    @drop.prevent="handleDrop($event, 'id_front')"
                                                    class="mt-4 cursor-pointer border-2 border-dashed rounded-lg p-8 text-center transition-all duration-200 ease-in-out"
                                                    :class="dragOver === 'id_front' ? 'border-blue-400 bg-blue-50' : 'border-gray-300 hover:border-blue-400 hover:bg-blue-50'"
                                                >
                                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                    </svg>
                                                    <p class="mt-4 text-sm font-medium text-gray-900">
                                                        Click to upload or drag and drop
                                                    </p>
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        PNG, JPG, or PDF (Max: 5MB)
                                                    </p>
                                                </div>
                                                
                                                <input 
                                                    ref="idFrontInput"
                                                    type="file"
                                                    accept="image/*,.pdf"
                                                    @change="handleFileUpload($event, 'id_front')"
                                                    class="hidden"
                                                />
                                                <p v-if="errors['guarantor.id_front']" class="mt-3 text-sm text-red-600">
                                                    {{ errors['guarantor.id_front'] }}
                                                </p>
                                            </div>

                                            <!-- ID Back Upload -->
                                            <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                                                <div class="flex items-center justify-between mb-4">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-6 w-6 text-blue-600">
                                                            <svg fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                                            </svg>
                                                        </div>
                                                        <div class="ml-3">
                                                            <h5 class="font-bold text-gray-900">ID Back Side</h5>
                                                            <p class="text-xs text-gray-600 mt-1">Upload the back side of the ID card <span class="text-red-500">*</span></p>
                                                        </div>
                                                    </div>
                                                    <button
                                                        v-if="!form.guarantor.id_back"
                                                        type="button"
                                                        @click="$refs.idBackInput.click()"
                                                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-150 ease-in-out"
                                                    >
                                                        Upload File
                                                    </button>
                                                </div>
                                                
                                                <!-- File Preview Area -->
                                                <div v-if="form.guarantor.id_back" class="mt-4">
                                                    <div class="flex items-start space-x-4">
                                                        <!-- Preview Container -->
                                                        <div class="relative">
                                                            <div class="w-40 h-40 rounded-lg overflow-hidden border-2 border-gray-200 bg-white shadow-sm">
                                                                <!-- Image Preview -->
                                                                <img 
                                                                    v-if="isImageFile(form.guarantor.id_back)"
                                                                    :src="getFilePreview(form.guarantor.id_back)"
                                                                    alt="ID Back Preview"
                                                                    class="w-full h-full object-contain p-2"
                                                                />
                                                                <!-- PDF Preview -->
                                                                <div v-else class="w-full h-full flex flex-col items-center justify-center bg-gray-50 p-4">
                                                                    <svg class="h-16 w-16 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                                                    </svg>
                                                                    <span class="mt-2 text-sm font-medium text-gray-700">PDF Document</span>
                                                                    <span class="text-xs text-gray-500 mt-1">{{ formatFileSize(form.guarantor.id_back.size) }}</span>
                                                                </div>
                                                            </div>
                                                            <!-- Remove Button -->
                                                            <button
                                                                type="button"
                                                                @click="form.guarantor.id_back = null"
                                                                class="absolute -top-2 -right-2 h-8 w-8 rounded-full bg-red-500 text-white flex items-center justify-center hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300 transition duration-150 ease-in-out"
                                                            >
                                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                        
                                                        <!-- File Info -->
                                                        <div class="flex-1">
                                                            <div class="flex items-center justify-between mb-2">
                                                                <p class="font-bold text-gray-900 truncate">{{ form.guarantor.id_back.name }}</p>
                                                                <button
                                                                    type="button"
                                                                    @click="$refs.idBackInput.click()"
                                                                    class="text-sm font-medium text-blue-600 hover:text-blue-700"
                                                                >
                                                                    Change File
                                                                </button>
                                                            </div>
                                                            <div class="space-y-2">
                                                                <p class="text-sm text-gray-600">
                                                                    <span class="font-medium">Size:</span> {{ formatFileSize(form.guarantor.id_back.size) }}
                                                                </p>
                                                                <p class="text-sm text-gray-600">
                                                                    <span class="font-medium">Type:</span> {{ getFileType(form.guarantor.id_back) }}
                                                                </p>
                                                                <p class="text-sm text-gray-600">
                                                                    <span class="font-medium">Status:</span> 
                                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 ml-1">
                                                                        Uploaded
                                                                    </span>
                                                                </p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Empty State -->
                                                <div v-else 
                                                    @click="$refs.idBackInput.click()"
                                                    @dragover.prevent="dragOver = 'id_back'"
                                                    @dragleave.prevent="dragOver = null"
                                                    @drop.prevent="handleDrop($event, 'id_back')"
                                                    class="mt-4 cursor-pointer border-2 border-dashed rounded-lg p-8 text-center transition-all duration-200 ease-in-out"
                                                    :class="dragOver === 'id_back' ? 'border-blue-400 bg-blue-50' : 'border-gray-300 hover:border-blue-400 hover:bg-blue-50'"
                                                >
                                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                    </svg>
                                                    <p class="mt-4 text-sm font-medium text-gray-900">
                                                        Click to upload or drag and drop
                                                    </p>
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        PNG, JPG, or PDF (Max: 5MB)
                                                    </p>
                                                </div>
                                                
                                                <input 
                                                    ref="idBackInput"
                                                    type="file"
                                                    accept="image/*,.pdf"
                                                    @change="handleFileUpload($event, 'id_back')"
                                                    class="hidden"
                                                />
                                                <p v-if="errors['guarantor.id_back']" class="mt-3 text-sm text-red-600">
                                                    {{ errors['guarantor.id_back'] }}
                                                </p>
                                            </div>

                                            <!-- Passport Picture Upload -->
                                            <div class="bg-gray-50 p-6 rounded-xl border border-gray-200">
                                                <div class="flex items-center justify-between mb-4">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-6 w-6 text-purple-600">
                                                            <svg fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 005 10a6 6 0 0112 0c0 .459-.031.907-.086 1.333A5 5 0 0010 11z" clip-rule="evenodd" />
                                                            </svg>
                                                        </div>
                                                        <div class="ml-3">
                                                            <h5 class="font-bold text-gray-900">Passport Photograph</h5>
                                                            <p class="text-xs text-gray-600 mt-1">Recent passport-sized photo <span class="text-red-500">*</span></p>
                                                        </div>
                                                    </div>
                                                    <button
                                                        v-if="!form.guarantor.passport_picture"
                                                        type="button"
                                                        @click="$refs.passportInput.click()"
                                                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-150 ease-in-out"
                                                    >
                                                        Upload File
                                                    </button>
                                                </div>
                                                
                                                <!-- File Preview Area -->
                                                <div v-if="form.guarantor.passport_picture" class="mt-4">
                                                    <div class="flex items-start space-x-4">
                                                        <!-- Preview Container -->
                                                        <div class="relative">
                                                            <div class="w-40 h-40 rounded-lg overflow-hidden border-2 border-gray-200 bg-white shadow-sm">
                                                                <!-- Image Preview -->
                                                                <img 
                                                                    :src="getFilePreview(form.guarantor.passport_picture)"
                                                                    alt="Passport Preview"
                                                                    class="w-full h-full object-cover"
                                                                />
                                                            </div>
                                                            <!-- Remove Button -->
                                                            <button
                                                                type="button"
                                                                @click="form.guarantor.passport_picture = null"
                                                                class="absolute -top-2 -right-2 h-8 w-8 rounded-full bg-red-500 text-white flex items-center justify-center hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300 transition duration-150 ease-in-out"
                                                            >
                                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                        
                                                        <!-- File Info -->
                                                        <div class="flex-1">
                                                            <div class="flex items-center justify-between mb-2">
                                                                <p class="font-bold text-gray-900 truncate">{{ form.guarantor.passport_picture.name }}</p>
                                                                <button
                                                                    type="button"
                                                                    @click="$refs.passportInput.click()"
                                                                    class="text-sm font-medium text-blue-600 hover:text-blue-700"
                                                                >
                                                                    Change File
                                                                </button>
                                                            </div>
                                                            <div class="space-y-2">
                                                                <p class="text-sm text-gray-600">
                                                                    <span class="font-medium">Size:</span> {{ formatFileSize(form.guarantor.passport_picture.size) }}
                                                                </p>
                                                                <p class="text-sm text-gray-600">
                                                                    <span class="font-medium">Type:</span> {{ getFileType(form.guarantor.passport_picture) }}
                                                                </p>
                                                                <p class="text-sm text-gray-600">
                                                                    <span class="font-medium">Status:</span> 
                                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 ml-1">
                                                                        Uploaded
                                                                    </span>
                                                                </p>
                                                            </div>
                                                            <div class="mt-4 p-3 bg-yellow-50 rounded-lg border border-yellow-200">
                                                                <p class="text-xs font-medium text-yellow-800 mb-1">Photo Requirements:</p>
                                                                <ul class="text-xs text-yellow-700 space-y-1">
                                                                    <li class="flex items-center">
                                                                        <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                                        </svg>
                                                                        Recent (within 6 months)
                                                                    </li>
                                                                    <li class="flex items-center">
                                                                        <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                                        </svg>
                                                                        Clear frontal view
                                                                    </li>
                                                                    <li class="flex items-center">
                                                                        <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                                        </svg>
                                                                        Plain background
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Empty State -->
                                                <div v-else 
                                                    @click="$refs.passportInput.click()"
                                                    @dragover.prevent="dragOver = 'passport_picture'"
                                                    @dragleave.prevent="dragOver = null"
                                                    @drop.prevent="handleDrop($event, 'passport_picture')"
                                                    class="mt-4 cursor-pointer border-2 border-dashed rounded-lg p-8 text-center transition-all duration-200 ease-in-out"
                                                    :class="dragOver === 'passport_picture' ? 'border-blue-400 bg-blue-50' : 'border-gray-300 hover:border-blue-400 hover:bg-blue-50'"
                                                >
                                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                    </svg>
                                                    <p class="mt-4 text-sm font-medium text-gray-900">
                                                        Click to upload or drag and drop
                                                    </p>
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        PNG or JPG only (Max: 5MB)
                                                    </p>
                                                </div>
                                                
                                                <input 
                                                    ref="passportInput"
                                                    type="file"
                                                    accept="image/*"
                                                    @change="handleFileUpload($event, 'passport_picture')"
                                                    class="hidden"
                                                />
                                                <p v-if="errors['guarantor.passport_picture']" class="mt-3 text-sm text-red-600">
                                                    {{ errors['guarantor.passport_picture'] }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Calculation Preview -->
                            <div class="mb-8">
                                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 rounded-xl border border-blue-100" v-if="calculationPreview.monthly_payment">
                                    <h3 class="text-lg font-bold text-gray-900 mb-4">Loan Calculation Preview</h3>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                                            <p class="text-sm font-medium text-gray-600">Monthly Payment</p>
                                            <p class="text-xl font-bold text-gray-900 mt-1">GH₵{{ calculationPreview.monthly_payment }}</p>
                                        </div>
                                        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                                            <p class="text-sm font-medium text-gray-600">Total Payment</p>
                                            <p class="text-xl font-bold text-gray-900 mt-1">GH₵{{ calculationPreview.total_payment }}</p>
                                        </div>
                                        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                                            <p class="text-sm font-medium text-gray-600">Total Interest</p>
                                            <p class="text-xl font-bold text-gray-900 mt-1">GH₵{{ calculationPreview.total_interest }}</p>
                                        </div>
                                        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                                            <p class="text-sm font-medium text-gray-600">Term</p>
                                            <p class="text-xl font-bold text-gray-900 mt-1">{{ form.term_months }} months</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                                <SecondaryButton 
                                    type="button"
                                    @click="$inertia.visit(route('loans.index'))"
                                    class="px-6 py-2.5 font-medium"
                                >
                                    Cancel
                                </SecondaryButton>
                                <PrimaryButton 
                                    type="submit"
                                    :disabled="form.processing"
                                    class="px-6 py-2.5 font-medium"
                                >
                                    <div class="flex items-center space-x-2">
                                        <svg v-if="form.processing" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span>{{ form.processing ? 'Creating...' : 'Create Loan' }}</span>
                                    </div>
                                </PrimaryButton>
                            </div>
                        </form>
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
import { useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    customers: Array,
    errors: Object
});

const form = useForm({
    customer_id: '',
    amount: '',
    interest_rate: '',
    term_months: '',
    purpose: '',
    purpose_description: '',
    type: '',
    guarantor: {
        full_name: '',
        email: '',
        phone: '',
        address: '',
        relationship: '',
        id_type: '',
        id_number: '',
        id_front: null,
        id_back: null,
        passport_picture: null
    }
});

const dragOver = ref(null);

const handleFileUpload = (event, field) => {
    const file = event.target.files[0];
    if (file) {
        form.guarantor[field] = file;
    }
};

const handleDrop = (event, field) => {
    dragOver.value = null;
    const file = event.dataTransfer.files[0];
    if (file) {
        // Validate file type
        const acceptedTypes = field === 'passport_picture' 
            ? ['image/jpeg', 'image/png', 'image/jpg']
            : ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
        
        if (acceptedTypes.includes(file.type) || 
            (file.type === '' && file.name.toLowerCase().endsWith('.pdf'))) {
            form.guarantor[field] = file;
        }
    }
};

// Helper functions
const getIDPlaceholder = (idType) => {
    const placeholders = {
        'ghana_card': 'GHA-XXXXXXXX-X',
        'voters_id': 'VOT-XXXX-XXXX',
        'drivers_license': 'DL-XXXX-XXXX-XX',
        'passport': 'PXXXXXXXX',
        'national_id': 'NID-XXXX-XXXX-XXXX',
        '': 'Enter ID number'
    };
    return placeholders[idType] || 'Enter ID number';
};

const isImageFile = (file) => {
    return file && file.type.startsWith('image/');
};

const getFilePreview = (file) => {
    if (file) {
        return URL.createObjectURL(file);
    }
    return '';
};

const getFileType = (file) => {
    if (!file) return '';
    if (file.type.startsWith('image/')) {
        return file.type.split('/')[1].toUpperCase();
    } else if (file.type === 'application/pdf') {
        return 'PDF';
    }
    return file.name.split('.').pop().toUpperCase();
};

const formatFileSize = (bytes) => {
    if (!bytes) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

// Calculate loan preview
const calculationPreview = computed(() => {
    if (!form.amount || !form.interest_rate || !form.term_months) {
        return {};
    }

    const principal = parseFloat(form.amount);
    const annualRate = parseFloat(form.interest_rate);
    const termMonths = parseInt(form.term_months);

    if (principal <= 0 || annualRate <= 0 || termMonths <= 0) {
        return {};
    }

    const monthlyRate = annualRate / 100 / 12;
    let monthlyPayment, totalPayment, totalInterest;

    if (monthlyRate > 0) {
        monthlyPayment = principal * (monthlyRate * Math.pow(1 + monthlyRate, termMonths)) / (Math.pow(1 + monthlyRate, termMonths) - 1);
    } else {
        monthlyPayment = principal / termMonths;
    }

    totalPayment = monthlyPayment * termMonths;
    totalInterest = totalPayment - principal;

    return {
        monthly_payment: monthlyPayment.toFixed(2),
        total_payment: totalPayment.toFixed(2),
        total_interest: totalInterest.toFixed(2)
    };
});

const submit = () => {
    const formData = new FormData();
    
    // Add loan data
    Object.keys(form.data()).forEach(key => {
        if (key !== 'guarantor') {
            formData.append(key, form[key]);
        }
    });
    
    // Add guarantor data
    Object.keys(form.guarantor).forEach(key => {
        formData.append(`guarantor[${key}]`, form.guarantor[key]);
    });
    
    form.post(route('loans.store'), {
        data: formData,
        headers: {
            'Content-Type': 'multipart/form-data'
        }
    });
};
</script>