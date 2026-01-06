<template>
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Edit Loan #{{ loan.id }}
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Update loan information</p>
                </div>
                <SecondaryButton @click="$inertia.visit(route('loans.show', loan.id))">
                    Back to Loan Details
                </SecondaryButton>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Success Message -->
                        <div v-if="$page.props.flash.success" class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            {{ $page.props.flash.success }}
                        </div>

                        <!-- Error Message -->
                        <div v-if="$page.props.flash.error" class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            {{ $page.props.flash.error }}
                        </div>

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
                                            :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-200': form.errors.customer_id }"
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
                                        <p v-if="form.errors.customer_id" class="mt-2 text-sm text-red-600">
                                            {{ form.errors.customer_id }}
                                        </p>
                                    </div>

                                    <!-- Loan Amount -->
                                    <div>
                                        <label for="amount" class="block text-sm font-bold text-gray-900 mb-2">
                                            Loan Amount (GH₵) <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative mt-1">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <span class="text-gray-700 font-medium">GH₵</span>
                                            </div>
                                            <input 
                                                id="amount"
                                                type="number"
                                                v-model="form.amount"
                                                required
                                                min="100"
                                                step="0.01"
                                                class="pl-10 w-full rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition duration-150 ease-in-out"
                                                :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-200': form.errors.amount }"
                                                placeholder="0.00"
                                            >
                                        </div>
                                        <p v-if="form.errors.amount" class="mt-2 text-sm text-red-600">
                                            {{ form.errors.amount }}
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
                                                :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-200': form.errors.interest_rate }"
                                                placeholder="0.00"
                                            >
                                        </div>
                                        <p v-if="form.errors.interest_rate" class="mt-2 text-sm text-red-600">
                                            {{ form.errors.interest_rate }}
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
                                                :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-200': form.errors.term_months }"
                                                placeholder="12"
                                            >
                                        </div>
                                        <p v-if="form.errors.term_months" class="mt-2 text-sm text-red-600">
                                            {{ form.errors.term_months }}
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
                                            :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-200': form.errors.type }"
                                        >
                                            <option value="">Select loan type</option>
                                            <option value="personal">Personal Loan</option>
                                            <option value="business">Business Loan</option>
                                            <option value="mortgage">Mortgage</option>
                                            <option value="auto">Auto Loan</option>
                                            <option value="education">Education Loan</option>
                                        </select>
                                        <p v-if="form.errors.type" class="mt-2 text-sm text-red-600">
                                            {{ form.errors.type }}
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
                                            :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-200': form.errors.purpose }"
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
                                        <p v-if="form.errors.purpose" class="mt-2 text-sm text-red-600">
                                            {{ form.errors.purpose }}
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
                                            :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-200': form.errors.purpose_description }"
                                            placeholder="Describe the purpose of this loan in detail..."
                                        ></textarea>
                                        <p v-if="form.errors.purpose_description" class="mt-2 text-sm text-red-600">
                                            {{ form.errors.purpose_description }}
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
                                        <p class="text-sm text-gray-600">Update guarantor details and documents</p>
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
                                            :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-200': form.errors['guarantor.full_name'] }"
                                            placeholder="Enter guarantor's full name"
                                        >
                                        <p v-if="form.errors['guarantor.full_name']" class="mt-2 text-sm text-red-600">
                                            {{ form.errors['guarantor.full_name'] }}
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
                                            :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-200': form.errors['guarantor.email'] }"
                                            placeholder="guarantor@example.com"
                                        >
                                        <p v-if="form.errors['guarantor.email']" class="mt-2 text-sm text-red-600">
                                            {{ form.errors['guarantor.email'] }}
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
                                                :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-200': form.errors['guarantor.phone'] }"
                                                placeholder="+233 XX XXX XXXX"
                                            >
                                        </div>
                                        <p v-if="form.errors['guarantor.phone']" class="mt-2 text-sm text-red-600">
                                            {{ form.errors['guarantor.phone'] }}
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
                                                    :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-200': form.errors['guarantor.id_type'] }"
                                                >
                                                    <option value="">Select ID Type</option>
                                                    <option value="ghana_card">Ghana Card</option>
                                                    <option value="voters_id">Voter's ID</option>
                                                    <option value="drivers_license">Driver's License</option>
                                                    <option value="passport">Passport</option>
                                                    <option value="national_id">National ID</option>
                                                </select>
                                                <p v-if="form.errors['guarantor.id_type']" class="mt-2 text-sm text-red-600">
                                                    {{ form.errors['guarantor.id_type'] }}
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
                                                    :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-200': form.errors['guarantor.id_number'] }"
                                                    :placeholder="getIDPlaceholder(form.guarantor.id_type)"
                                                >
                                                <p v-if="form.errors['guarantor.id_number']" class="mt-2 text-sm text-red-600">
                                                    {{ form.errors['guarantor.id_number'] }}
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
                                            :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-200': form.errors['guarantor.relationship'] }"
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
                                        <p v-if="form.errors['guarantor.relationship']" class="mt-2 text-sm text-red-600">
                                            {{ form.errors['guarantor.relationship'] }}
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
                                                :class="{ 'border-red-300 focus:border-red-500 focus:ring-red-200': form.errors['guarantor.address'] }"
                                                placeholder="Enter guarantor's complete address including city and region"
                                            ></textarea>
                                        </div>
                                        <p v-if="form.errors['guarantor.address']" class="mt-2 text-sm text-red-600">
                                            {{ form.errors['guarantor.address'] }}
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
                                                <p class="text-sm text-gray-600">Update guarantor documents (upload new files if needed)</p>
                                            </div>
                                        </div>
                                        
                                        <!-- Existing Files Info -->
                                        <div v-if="loan.guarantor" class="mb-8 bg-blue-50 p-5 rounded-xl border border-blue-200">
                                            <h5 class="text-md font-bold text-gray-900 mb-3">Current Uploaded Documents</h5>
                                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                <div v-if="loan.guarantor.id_front_path" class="text-center">
                                                    <div class="bg-white p-4 rounded-lg shadow-sm">
                                                        <div class="h-12 w-12 mx-auto mb-2 text-blue-600">
                                                            <svg fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                                            </svg>
                                                        </div>
                                                        <p class="text-sm font-medium text-gray-900">ID Front</p>
                                                        <p class="text-xs text-gray-600 mt-1">Uploaded</p>
                                                        <a :href="loan.guarantor.id_front_url" target="_blank" class="mt-2 inline-block text-xs text-blue-600 hover:text-blue-700">
                                                            View Document
                                                        </a>
                                                    </div>
                                                </div>
                                                <div v-if="loan.guarantor.id_back_path" class="text-center">
                                                    <div class="bg-white p-4 rounded-lg shadow-sm">
                                                        <div class="h-12 w-12 mx-auto mb-2 text-blue-600">
                                                            <svg fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                                                            </svg>
                                                        </div>
                                                        <p class="text-sm font-medium text-gray-900">ID Back</p>
                                                        <p class="text-xs text-gray-600 mt-1">Uploaded</p>
                                                        <a :href="loan.guarantor.id_back_url" target="_blank" class="mt-2 inline-block text-xs text-blue-600 hover:text-blue-700">
                                                            View Document
                                                        </a>
                                                    </div>
                                                </div>
                                                <div v-if="loan.guarantor.passport_picture_path" class="text-center">
                                                    <div class="bg-white p-4 rounded-lg shadow-sm">
                                                        <div class="h-12 w-12 mx-auto mb-2 text-purple-600">
                                                            <svg fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 005 10a6 6 0 0112 0c0 .459-.031.907-.086 1.333A5 5 0 0010 11z" clip-rule="evenodd" />
                                                            </svg>
                                                        </div>
                                                        <p class="text-sm font-medium text-gray-900">Passport Photo</p>
                                                        <p class="text-xs text-gray-600 mt-1">Uploaded</p>
                                                        <a :href="loan.guarantor.passport_picture_url" target="_blank" class="mt-2 inline-block text-xs text-blue-600 hover:text-blue-700">
                                                            View Photo
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <p class="text-xs text-gray-600 mt-4 text-center">
                                                Upload new files below to replace existing documents
                                            </p>
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
                                                            <p class="text-xs text-gray-600 mt-1">Upload new front side of the ID card (optional)</p>
                                                        </div>
                                                    </div>
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
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Empty State -->
                                                <div v-else 
                                                    @click="$refs.idFrontInput.click()"
                                                    class="mt-4 cursor-pointer border-2 border-dashed rounded-lg p-8 text-center transition-all duration-200 ease-in-out border-gray-300 hover:border-blue-400 hover:bg-blue-50"
                                                >
                                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                    </svg>
                                                    <p class="mt-4 text-sm font-medium text-gray-900">
                                                        Click to upload new ID front
                                                    </p>
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        PNG, JPG, or PDF (Max: 5MB)
                                                    </p>
                                                    <p v-if="loan.guarantor?.id_front_path" class="text-xs text-gray-600 mt-2">
                                                        Current file will be replaced
                                                    </p>
                                                </div>
                                                
                                                <input 
                                                    ref="idFrontInput"
                                                    type="file"
                                                    accept="image/*,.pdf"
                                                    @change="handleFileUpload($event, 'id_front')"
                                                    class="hidden"
                                                />
                                                <p v-if="form.errors['guarantor.id_front']" class="mt-3 text-sm text-red-600">
                                                    {{ form.errors['guarantor.id_front'] }}
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
                                                            <p class="text-xs text-gray-600 mt-1">Upload new back side of the ID card (optional)</p>
                                                        </div>
                                                    </div>
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
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Empty State -->
                                                <div v-else 
                                                    @click="$refs.idBackInput.click()"
                                                    class="mt-4 cursor-pointer border-2 border-dashed rounded-lg p-8 text-center transition-all duration-200 ease-in-out border-gray-300 hover:border-blue-400 hover:bg-blue-50"
                                                >
                                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                    </svg>
                                                    <p class="mt-4 text-sm font-medium text-gray-900">
                                                        Click to upload new ID back
                                                    </p>
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        PNG, JPG, or PDF (Max: 5MB)
                                                    </p>
                                                    <p v-if="loan.guarantor?.id_back_path" class="text-xs text-gray-600 mt-2">
                                                        Current file will be replaced
                                                    </p>
                                                </div>
                                                
                                                <input 
                                                    ref="idBackInput"
                                                    type="file"
                                                    accept="image/*,.pdf"
                                                    @change="handleFileUpload($event, 'id_back')"
                                                    class="hidden"
                                                />
                                                <p v-if="form.errors['guarantor.id_back']" class="mt-3 text-sm text-red-600">
                                                    {{ form.errors['guarantor.id_back'] }}
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
                                                            <p class="text-xs text-gray-600 mt-1">Upload new passport photo (optional)</p>
                                                        </div>
                                                    </div>
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
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Empty State -->
                                                <div v-else 
                                                    @click="$refs.passportInput.click()"
                                                    class="mt-4 cursor-pointer border-2 border-dashed rounded-lg p-8 text-center transition-all duration-200 ease-in-out border-gray-300 hover:border-blue-400 hover:bg-blue-50"
                                                >
                                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                                    </svg>
                                                    <p class="mt-4 text-sm font-medium text-gray-900">
                                                        Click to upload new passport photo
                                                    </p>
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        PNG or JPG only (Max: 5MB)
                                                    </p>
                                                    <p v-if="loan.guarantor?.passport_picture_path" class="text-xs text-gray-600 mt-2">
                                                        Current photo will be replaced
                                                    </p>
                                                </div>
                                                
                                                <input 
                                                    ref="passportInput"
                                                    type="file"
                                                    accept="image/*"
                                                    @change="handleFileUpload($event, 'passport_picture')"
                                                    class="hidden"
                                                />
                                                <p v-if="form.errors['guarantor.passport_picture']" class="mt-3 text-sm text-red-600">
                                                    {{ form.errors['guarantor.passport_picture'] }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Current Loan Info -->
                            <div class="mb-8">
                                <div class="bg-blue-50 p-6 rounded-xl border border-blue-100">
                                    <h3 class="text-lg font-bold text-gray-900 mb-4">Current Loan Information</h3>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                                            <p class="text-sm font-medium text-gray-600">Current Amount</p>
                                            <p class="text-xl font-bold text-gray-900 mt-1">GH₵{{ loan.amount.toLocaleString() }}</p>
                                        </div>
                                        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                                            <p class="text-sm font-medium text-gray-600">Current Rate</p>
                                            <p class="text-xl font-bold text-gray-900 mt-1">{{ loan.interest_rate }}%</p>
                                        </div>
                                        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                                            <p class="text-sm font-medium text-gray-600">Current Term</p>
                                            <p class="text-xl font-bold text-gray-900 mt-1">{{ loan.term_months }} months</p>
                                        </div>
                                        <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                                            <p class="text-sm font-medium text-gray-600">Status</p>
                                            <div class="mt-1">
                                                <span :class="statusBadgeClass(loan.status)" class="px-3 py-1 text-sm font-medium rounded-full">
                                                    {{ formatStatus(loan.status) }}
                                                </span>
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
                                    @click="$inertia.visit(route('loans.show', loan.id))"
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
                                        <span>{{ form.processing ? 'Updating...' : 'Update Loan' }}</span>
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
import { computed } from 'vue';

const props = defineProps({
    loan: Object,
    customers: Array
});

const form = useForm({
    customer_id: props.loan.customer_id,
    amount: props.loan.amount,
    interest_rate: props.loan.interest_rate,
    term_months: props.loan.term_months,
    purpose: props.loan.purpose,
    purpose_description: props.loan.purpose_description,
    type: props.loan.type,
    guarantor: {
        full_name: props.loan.guarantor?.full_name || '',
        email: props.loan.guarantor?.email || '',
        phone: props.loan.guarantor?.phone || '',
        address: props.loan.guarantor?.address || '',
        relationship: props.loan.guarantor?.relationship || '',
        id_type: props.loan.guarantor?.id_type || '',
        id_number: props.loan.guarantor?.id_number || '',
        id_front: null,
        id_back: null,
        passport_picture: null
    }
});

const handleFileUpload = (event, field) => {
    const file = event.target.files[0];
    if (file) {
        form.guarantor[field] = file;
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

const formatStatus = (status) => {
    return status.replace(/_/g, ' ').toUpperCase();
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

const submit = () => {
    const formData = new FormData();
    
    // Add loan data
    formData.append('customer_id', form.customer_id);
    formData.append('amount', form.amount);
    formData.append('interest_rate', form.interest_rate);
    formData.append('term_months', form.term_months);
    formData.append('purpose', form.purpose);
    formData.append('type', form.type);
    formData.append('purpose_description', form.purpose_description);
    
    // Add guarantor data
    formData.append('guarantor[full_name]', form.guarantor.full_name);
    formData.append('guarantor[email]', form.guarantor.email || '');
    formData.append('guarantor[phone]', form.guarantor.phone);
    formData.append('guarantor[address]', form.guarantor.address);
    formData.append('guarantor[relationship]', form.guarantor.relationship);
    formData.append('guarantor[id_type]', form.guarantor.id_type);
    formData.append('guarantor[id_number]', form.guarantor.id_number);
    
    // Add files only if they exist (optional for updates)
    if (form.guarantor.id_front) {
        formData.append('guarantor[id_front]', form.guarantor.id_front);
    }
    if (form.guarantor.id_back) {
        formData.append('guarantor[id_back]', form.guarantor.id_back);
    }
    if (form.guarantor.passport_picture) {
        formData.append('guarantor[passport_picture]', form.guarantor.passport_picture);
    }
    
    form.put(route('loans.update', props.loan.id), {
        data: formData,
        headers: {
            'Content-Type': 'multipart/form-data'
        },
        preserveScroll: true
    });
};
</script>