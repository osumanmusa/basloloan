<template>
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Edit Customer: {{ form.first_name }} {{ form.last_name }}
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Update customer information</p>
                </div>
                <SecondaryButton @click="$inertia.visit(route('customers.show', customer.id))">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Profile
                </SecondaryButton>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Success Message -->
                        <div v-if="$page.props.flash.success" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            {{ $page.props.flash.success }}
                        </div>

                        <!-- Error Message -->
                        <div v-if="$page.props.flash.error" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            {{ $page.props.flash.error }}
                        </div>

                        <form @submit.prevent="submit">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Personal Information -->
                                <div class="md:col-span-2">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Personal Information</h3>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        First Name *
                                    </label>
                                    <input 
                                        type="text"
                                        v-model="form.first_name"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-300': form.errors.first_name }"
                                        placeholder="John"
                                    >
                                    <p v-if="form.errors.first_name" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.first_name }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Last Name *
                                    </label>
                                    <input 
                                        type="text"
                                        v-model="form.last_name"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-300': form.errors.last_name }"
                                        placeholder="Doe"
                                    >
                                    <p v-if="form.errors.last_name" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.last_name }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Email Address *
                                    </label>
                                    <input 
                                        type="email"
                                        v-model="form.email"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-300': form.errors.email }"
                                        placeholder="john.doe@example.com"
                                    >
                                    <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.email }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Phone Number *
                                    </label>
                                    <input 
                                        type="tel"
                                        v-model="form.phone"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-300': form.errors.phone }"
                                        placeholder="+233 20 123 4567"
                                    >
                                    <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.phone }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Date of Birth *
                                    </label>
                                    <input 
                                        type="date"
                                        v-model="form.date_of_birth"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-300': form.errors.date_of_birth }"
                                    >
                                    <p v-if="form.errors.date_of_birth" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.date_of_birth }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        ID Type *
                                    </label>
                                    <select 
                                        v-model="form.id_type"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-300': form.errors.id_type }"
                                        @change="onIdTypeChange"
                                    >
                                        <option value="">Select ID Type</option>
                                        <option value="Ghana Card">Ghana Card</option>
                                        <option value="Voters ID">Voters ID</option>
                                        <option value="Passport">Passport</option>
                                        <option value="Drivers License">Driver's License</option>
                                        <option value="National ID">National ID</option>
                                        <option value="Other">Other</option>
                                    </select>
                                    <p v-if="form.errors.id_type" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.id_type }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        ID Number *
                                    </label>
                                    <input 
                                        type="text"
                                        v-model="form.id_number"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-300': form.errors.id_number }"
                                        :placeholder="idNumberPlaceholder"
                                    >
                                    <p v-if="form.errors.id_number" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.id_number }}
                                    </p>
                                </div>

                                <!-- Passport Picture -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Passport Picture (Update if needed)
                                    </label>
                                    <div class="flex flex-col items-start space-y-4">
                                        <div class="flex items-center space-x-4">
                                            <div v-if="customer.passport_picture_url" class="relative">
                                                <img :src="customer.passport_picture_url" 
                                                     alt="Current Passport Picture" 
                                                     class="w-24 h-24 rounded-lg object-cover border border-gray-300"
                                                     @error="handleImageError('current_passport')">
                                                <div class="text-xs text-gray-500 mt-1 text-center">Current Photo</div>
                                            </div>
                                            <div class="relative">
                                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-blue-400 transition-colors cursor-pointer"
                                                     @click="triggerFileInput('passport')"
                                                     :class="{ 
                                                        'border-blue-500': passportPreview,
                                                        'border-red-300': form.errors.passport_picture
                                                     }">
                                                    <div class="space-y-1 text-center">
                                                        <div v-if="!passportPreview" class="flex flex-col items-center">
                                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                            </svg>
                                                            <div class="flex text-sm text-gray-600">
                                                                <span class="font-medium text-blue-600 hover:text-blue-500">
                                                                    Upload new photo
                                                                </span>
                                                                <p class="pl-1">or drag and drop</p>
                                                            </div>
                                                            <p class="text-xs text-gray-500">
                                                                PNG, JPG, JPEG up to 5MB
                                                            </p>
                                                        </div>
                                                        <div v-else class="relative">
                                                            <img :src="passportPreview" alt="Passport Preview" class="mx-auto h-32 object-contain rounded-lg">
                                                            <button 
                                                                type="button"
                                                                @click.stop="removePassportPicture"
                                                                class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
                                                            >
                                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div v-if="passportPreview" class="text-xs text-green-600 mt-1 text-center">New photo ready</div>
                                            </div>
                                        </div>
                                        <input 
                                            id="passport-picture" 
                                            type="file" 
                                            accept="image/*"
                                            @change="handlePassportPicture"
                                            class="sr-only"
                                            ref="passportInput"
                                        >
                                        <div class="text-xs text-gray-500">
                                            <p>• Leave empty to keep current photo</p>
                                            <p>• Recent photo, clear face visible</p>
                                            <p>• Plain white or light-colored background</p>
                                        </div>
                                    </div>
                                    <p v-if="form.errors.passport_picture" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.passport_picture }}
                                    </p>
                                </div>

                                <!-- ID Document Uploads -->
                                <div class="md:col-span-2">
                                    <h3 class="text-lg font-medium text-gray-900 mt-6 mb-4">ID Documents (Update if needed)</h3>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- ID Front -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                {{ idFrontLabel }} {{ requiresBackSide ? '*' : '(Optional)' }}
                                            </label>
                                            <div class="flex flex-col space-y-3">
                                                <div v-if="customer.id_front_url" class="relative">
                                                    <img :src="customer.id_front_url" 
                                                         :alt="customer.id_type + ' Front'" 
                                                         class="w-full h-40 object-contain bg-gray-50 rounded-lg border border-gray-300"
                                                         @error="handleImageError('current_id_front')">
                                                    <div class="text-xs text-gray-500 mt-1 text-center">Current {{ idFrontLabel }}</div>
                                                </div>
                                                
                                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-blue-400 transition-colors cursor-pointer"
                                                     @click="triggerFileInput('id_front')"
                                                     :class="{ 
                                                        'border-blue-500': idFrontPreview,
                                                        'border-red-300': form.errors.id_front
                                                     }">
                                                    <div class="space-y-1 text-center">
                                                        <div v-if="!idFrontPreview" class="flex flex-col items-center">
                                                            <svg class="mx-auto h-8 w-8 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                            </svg>
                                                            <div class="text-sm text-gray-600">
                                                                <span class="font-medium text-blue-600 hover:text-blue-500">
                                                                    Update {{ idFrontLabel.toLowerCase() }}
                                                                </span>
                                                            </div>
                                                            <p class="text-xs text-gray-500">
                                                                PNG, JPG, JPEG up to 5MB
                                                            </p>
                                                        </div>
                                                        <div v-else class="relative">
                                                            <img :src="idFrontPreview" :alt="idFrontLabel + ' Preview'" class="mx-auto h-32 object-contain rounded-lg">
                                                            <button 
                                                                type="button"
                                                                @click.stop="removeIdFront"
                                                                class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
                                                            >
                                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <input 
                                                    id="id-front" 
                                                    type="file" 
                                                    accept="image/*"
                                                    @change="handleIdFront"
                                                    class="sr-only"
                                                    ref="idFrontInput"
                                                >
                                                <div v-if="idFrontPreview" class="text-xs text-green-600 text-center">New {{ idFrontLabel.toLowerCase() }} ready</div>
                                            </div>
                                            <p v-if="form.errors.id_front" class="mt-1 text-sm text-red-600">
                                                {{ form.errors.id_front }}
                                            </p>
                                        </div>

                                        <!-- ID Back -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                {{ idBackLabel }} {{ requiresBackSide ? '*' : '(Optional)' }}
                                            </label>
                                            <div class="flex flex-col space-y-3">
                                                <div v-if="customer.id_back_url" class="relative">
                                                    <img :src="customer.id_back_url" 
                                                         :alt="customer.id_type + ' Back'" 
                                                         class="w-full h-40 object-contain bg-gray-50 rounded-lg border border-gray-300"
                                                         @error="handleImageError('current_id_back')">
                                                    <div class="text-xs text-gray-500 mt-1 text-center">Current {{ idBackLabel }}</div>
                                                </div>
                                                <div v-else-if="!requiresBackSide" class="text-xs text-gray-500 italic">
                                                    No back side uploaded
                                                </div>
                                                
                                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-blue-400 transition-colors cursor-pointer"
                                                     @click="triggerFileInput('id_back')"
                                                     :class="{ 
                                                        'border-blue-500': idBackPreview,
                                                        'border-red-300': form.errors.id_back
                                                     }">
                                                    <div class="space-y-1 text-center">
                                                        <div v-if="!idBackPreview" class="flex flex-col items-center">
                                                            <svg class="mx-auto h-8 w-8 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                            </svg>
                                                            <div class="text-sm text-gray-600">
                                                                <span class="font-medium text-blue-600 hover:text-blue-500">
                                                                    Update {{ idBackLabel.toLowerCase() }}
                                                                </span>
                                                            </div>
                                                            <p class="text-xs text-gray-500">
                                                                PNG, JPG, JPEG up to 5MB
                                                            </p>
                                                        </div>
                                                        <div v-else class="relative">
                                                            <img :src="idBackPreview" :alt="idBackLabel + ' Preview'" class="mx-auto h-32 object-contain rounded-lg">
                                                            <button 
                                                                type="button"
                                                                @click.stop="removeIdBack"
                                                                class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
                                                            >
                                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <input 
                                                    id="id-back" 
                                                    type="file" 
                                                    accept="image/*"
                                                    @change="handleIdBack"
                                                    class="sr-only"
                                                    ref="idBackInput"
                                                >
                                                <div v-if="idBackPreview" class="text-xs text-green-600 text-center">New {{ idBackLabel.toLowerCase() }} ready</div>
                                            </div>
                                            <p v-if="form.errors.id_back" class="mt-1 text-sm text-red-600">
                                                {{ form.errors.id_back }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-4 text-sm text-gray-500">
                                        <p>• Leave empty to keep current documents</p>
                                        <p>• Maximum file size: 5MB per image</p>
                                        <p v-if="requiresBackSide" class="text-amber-600 font-medium">
                                            • {{ customer.id_type }} requires both front and back sides
                                        </p>
                                    </div>
                                </div>

                                <!-- Address Information -->
                                <div class="md:col-span-2 mt-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Address Information</h3>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Address *
                                    </label>
                                    <input 
                                        type="text"
                                        v-model="form.address"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-300': form.errors.address }"
                                        placeholder="123 Main Street"
                                    >
                                    <p v-if="form.errors.address" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.address }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        City *
                                    </label>
                                    <input 
                                        type="text"
                                        v-model="form.city"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-300': form.errors.city }"
                                        placeholder="Accra"
                                    >
                                    <p v-if="form.errors.city" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.city }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        State/Region *
                                    </label>
                                    <input 
                                        type="text"
                                        v-model="form.state"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-300': form.errors.state }"
                                        placeholder="Greater Accra"
                                    >
                                    <p v-if="form.errors.state" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.state }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        ZIP Code *
                                    </label>
                                    <input 
                                        type="text"
                                        v-model="form.zip_code"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-300': form.errors.zip_code }"
                                        placeholder="GA123"
                                    >
                                    <p v-if="form.errors.zip_code" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.zip_code }}
                                    </p>
                                </div>

                                <!-- Financial Information -->
                                <div class="md:col-span-2 mt-6">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Financial Information</h3>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Monthly Income (GH₵)
                                    </label>
                                    <input 
                                        type="number"
                                        v-model="form.monthly_income"
                                        min="0"
                                        step="0.01"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-300': form.errors.monthly_income }"
                                        placeholder="5000.00"
                                    >
                                    <p v-if="form.errors.monthly_income" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.monthly_income }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Employment Status *
                                    </label>
                                    <select 
                                        v-model="form.employment_status"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-300': form.errors.employment_status }"
                                    >
                                        <option value="">Select employment status</option>
                                        <option value="employed">Employed</option>
                                        <option value="self-employed">Self-Employed</option>
                                        <option value="unemployed">Unemployed</option>
                                        <option value="retired">Retired</option>
                                        <option value="student">Student</option>
                                    </select>
                                    <p v-if="form.errors.employment_status" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.employment_status }}
                                    </p>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Employment Details
                                    </label>
                                    <textarea 
                                        v-model="form.employment_details"
                                        rows="3"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-300': form.errors.employment_details }"
                                        placeholder="Company name, position, years employed..."
                                    ></textarea>
                                    <p v-if="form.errors.employment_details" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.employment_details }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Credit Score
                                    </label>
                                    <input 
                                        type="number"
                                        v-model="form.credit_score"
                                        min="300"
                                        max="850"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-300': form.errors.credit_score }"
                                        placeholder="750"
                                    >
                                    <p v-if="form.errors.credit_score" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.credit_score }}
                                    </p>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                                <SecondaryButton 
                                    type="button"
                                    @click="$inertia.visit(route('customers.show', customer.id))"
                                >
                                    Cancel
                                </SecondaryButton>
                                <PrimaryButton 
                                    type="submit"
                                    :disabled="form.processing"
                                    class="flex items-center space-x-2"
                                >
                                    <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>{{ form.processing ? 'Updating...' : 'Update Customer' }}</span>
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
import { ref, computed } from 'vue';

const props = defineProps({
    customer: Object
});

// Reactive references for file previews
const passportPreview = ref(null);
const idFrontPreview = ref(null);
const idBackPreview = ref(null);
const passportInput = ref(null);
const idFrontInput = ref(null);
const idBackInput = ref(null);

// Computed properties
const idFrontLabel = computed(() => {
    switch (form.id_type) {
        case 'Ghana Card': return 'Front Side';
        case 'Voters ID': return 'Front Side';
        case 'Passport': return 'Passport Page';
        case 'Drivers License': return 'Front Side';
        case 'National ID': return 'Front Side';
        default: return 'Front Side';
    }
});

const idBackLabel = computed(() => {
    switch (form.id_type) {
        case 'Ghana Card': return 'Back Side';
        case 'Voters ID': return 'Back Side (Optional)';
        case 'Passport': return 'Back Page (Optional)';
        case 'Drivers License': return 'Back Side';
        case 'National ID': return 'Back Side';
        default: return 'Back Side';
    }
});

const requiresBackSide = computed(() => {
    return ['Ghana Card', 'Drivers License', 'National ID'].includes(form.id_type);
});

const idNumberPlaceholder = computed(() => {
    switch (form.id_type) {
        case 'Ghana Card': return 'GHA-XXXXXXXX-X';
        case 'Voters ID': return 'VOT-XXXXXXXX';
        case 'Passport': return 'PXXXXXXXX';
        case 'Drivers License': return 'DL-XXXXXXXX';
        case 'National ID': return 'NID-XXXXXXXX';
        default: return 'ID Number';
    }
});

// Format date for input field (YYYY-MM-DD)
const formatDateForInput = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toISOString().split('T')[0];
};

// Handle ID type change
const onIdTypeChange = () => {
    // Reset file previews if ID type changes
    removeIdFront();
    removeIdBack();
};

const form = useForm({
    first_name: props.customer.first_name || '',
    last_name: props.customer.last_name || '',
    email: props.customer.email || '',
    phone: props.customer.phone || '',
    address: props.customer.address || '',
    city: props.customer.city || '',
    state: props.customer.state || '',
    zip_code: props.customer.zip_code || '',
    date_of_birth: formatDateForInput(props.customer.date_of_birth),
    id_type: props.customer.id_type || '',
    id_number: props.customer.id_number || '',
    monthly_income: props.customer.monthly_income || '',
    employment_status: props.customer.employment_status || '',
    employment_details: props.customer.employment_details || '',
    credit_score: props.customer.credit_score || '',
    passport_picture: null,
    id_front: null,
    id_back: null
});

// File validation function
const validateFile = (file) => {
    if (!file) return false;
    
    // Validate file size (5MB limit)
    if (file.size > 5 * 1024 * 1024) {
        alert('File size must be less than 5MB');
        return false;
    }
    
    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
    if (!allowedTypes.includes(file.type)) {
        alert('Please upload an image file (JPG, JPEG, or PNG)');
        return false;
    }
    
    return true;
};

// Trigger file input
const triggerFileInput = (type) => {
    switch (type) {
        case 'passport':
            passportInput.value?.click();
            break;
        case 'id_front':
            idFrontInput.value?.click();
            break;
        case 'id_back':
            idBackInput.value?.click();
            break;
    }
};

// Handle passport picture upload
const handlePassportPicture = (event) => {
    const file = event.target.files[0];
    if (file && validateFile(file)) {
        form.passport_picture = file;
        
        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            passportPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

// Handle ID front upload
const handleIdFront = (event) => {
    const file = event.target.files[0];
    if (file && validateFile(file)) {
        form.id_front = file;
        
        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            idFrontPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

// Handle ID back upload
const handleIdBack = (event) => {
    const file = event.target.files[0];
    if (file && validateFile(file)) {
        form.id_back = file;
        
        // Create preview
        const reader = new FileReader();
        reader.onload = (e) => {
            idBackPreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

// Remove passport picture
const removePassportPicture = () => {
    passportPreview.value = null;
    form.passport_picture = null;
    if (passportInput.value) {
        passportInput.value.value = '';
    }
};

// Remove ID front
const removeIdFront = () => {
    idFrontPreview.value = null;
    form.id_front = null;
    if (idFrontInput.value) {
        idFrontInput.value.value = '';
    }
};

// Remove ID back
const removeIdBack = () => {
    idBackPreview.value = null;
    form.id_back = null;
    if (idBackInput.value) {
        idBackInput.value.value = '';
    }
};

// Handle image error
const handleImageError = (type) => {
    console.error(`Failed to load image: ${type}`);
};

const submit = () => {
    // Validate that if back side is required and user is uploading a new front,
    // they should also upload a new back (or keep existing)
    if (requiresBackSide.value && form.id_front && !form.id_back && !props.customer.id_back) {
        alert(`${form.id_type} requires both front and back sides. Please upload the back side or keep the existing one.`);
        return;
    }
    
    form.transform((data) => ({
        ...data,
        passport_picture: data.passport_picture,
        id_front: data.id_front,
        id_back: data.id_back,
    })).put(route('customers.update', props.customer.id), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            // Clear file previews on successful submission
            removePassportPicture();
            removeIdFront();
            removeIdBack();
        }
    });
};
</script>