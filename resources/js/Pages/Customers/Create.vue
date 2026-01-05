<template>
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Add New Customer
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Create a new customer profile</p>
                </div>
                <SecondaryButton @click="$inertia.visit(route('customers.index'))">
                    Back to Customers
                </SecondaryButton>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
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
                                        :class="{ 'border-red-300': errors.first_name }"
                                        placeholder="John"
                                    >
                                    <p v-if="errors.first_name" class="mt-1 text-sm text-red-600">
                                        {{ errors.first_name }}
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
                                        :class="{ 'border-red-300': errors.last_name }"
                                        placeholder="Doe"
                                    >
                                    <p v-if="errors.last_name" class="mt-1 text-sm text-red-600">
                                        {{ errors.last_name }}
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
                                        :class="{ 'border-red-300': errors.email }"
                                        placeholder="john.doe@example.com"
                                    >
                                    <p v-if="errors.email" class="mt-1 text-sm text-red-600">
                                        {{ errors.email }}
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
                                        :class="{ 'border-red-300': errors.phone }"
                                        placeholder="+1 (555) 123-4567"
                                    >
                                    <p v-if="errors.phone" class="mt-1 text-sm text-red-600">
                                        {{ errors.phone }}
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
                                        :class="{ 'border-red-300': errors.date_of_birth }"
                                    >
                                    <p v-if="errors.date_of_birth" class="mt-1 text-sm text-red-600">
                                        {{ errors.date_of_birth }}
                                    </p>
                                </div>

                                <!-- Passport Picture Section -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Passport Picture *
                                    </label>
                                    <div class="flex flex-col items-center">
                                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md w-full"
                                            :class="{ 
                                                'border-blue-500': passportPreview,
                                                'border-red-300': errors.passport_picture
                                            }">
                                            <div class="space-y-1 text-center">
                                                <div v-if="!passportPreview" class="flex flex-col items-center">
                                                    <svg class="mx-auto h-24 w-24 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                    <div class="flex text-sm text-gray-600">
                                                        <label for="passport-picture" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                            <span>Upload passport picture</span>
                                                            <input 
                                                                id="passport-picture" 
                                                                type="file" 
                                                                accept="image/*"
                                                                @change="handlePassportPicture"
                                                                class="sr-only"
                                                                required
                                                            >
                                                        </label>
                                                        <p class="pl-1">or drag and drop</p>
                                                    </div>
                                                    <p class="text-xs text-gray-500">
                                                        Recent photo, clear face visible<br>
                                                        PNG, JPG, JPEG up to 5MB
                                                    </p>
                                                </div>
                                                <div v-else class="relative">
                                                    <img :src="passportPreview" alt="Passport Picture Preview" class="mx-auto h-64 object-contain rounded-lg">
                                                    <button 
                                                        type="button"
                                                        @click="removePassportPicture"
                                                        class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
                                                    >
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-3 text-sm text-gray-500">
                                            <p>• Recent photo (taken within last 6 months)</p>
                                            <p>• Clear view of full face, looking directly at camera</p>
                                            <p>• Plain white or light-colored background</p>
                                            <p>• No hats, sunglasses, or face coverings (except for religious purposes)</p>
                                        </div>
                                    </div>
                                    <p v-if="errors.passport_picture" class="mt-1 text-sm text-red-600">
                                        {{ errors.passport_picture }}
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
                                        :class="{ 'border-red-300': errors.id_type }"
                                    >
                                        <option value="">Select ID Type</option>
                                        <option value="Ghana Card">Ghana Card</option>
                                        <option value="Voters ID">Voters ID</option>
                                        <option value="Passport">Passport</option>
                                        <option value="Drivers License">Driver's License</option>
                                        <option value="National ID">National ID</option>
                                        <option value="Other">Other</option>
                                    </select>
                                    <p v-if="errors.id_type" class="mt-1 text-sm text-red-600">
                                        {{ errors.id_type }}
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
                                        :class="{ 'border-red-300': errors.id_number }"
                                        :placeholder="idNumberPlaceholder"
                                    >
                                    <p v-if="errors.id_number" class="mt-1 text-sm text-red-600">
                                        {{ errors.id_number }}
                                    </p>
                                </div>

                                <!-- ID Document Upload Section -->
                                <div class="md:col-span-2 mt-4">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">ID Document Upload</h3>
                                    <p class="text-sm text-gray-600 mb-4">Upload clear photos of your selected ID document</p>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Front Side -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                {{ idFrontLabel }} *
                                            </label>
                                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md"
                                                :class="{ 
                                                    'border-blue-500': idFrontPreview,
                                                    'border-red-300': errors.id_front
                                                }">
                                                <div class="space-y-1 text-center">
                                                    <div v-if="!idFrontPreview" class="flex flex-col items-center">
                                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                        <div class="flex text-sm text-gray-600">
                                                            <label for="id-front" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                                <span>Upload {{ idFrontLabel.toLowerCase() }}</span>
                                                                <input 
                                                                    id="id-front" 
                                                                    type="file" 
                                                                    accept="image/*"
                                                                    @change="handleIdFront"
                                                                    class="sr-only"
                                                                    required
                                                                >
                                                            </label>
                                                            <p class="pl-1">or drag and drop</p>
                                                        </div>
                                                        <p class="text-xs text-gray-500">
                                                            PNG, JPG, JPEG up to 5MB
                                                        </p>
                                                    </div>
                                                    <div v-else class="relative">
                                                        <img :src="idFrontPreview" :alt="idFrontLabel + ' Preview'" class="mx-auto h-48 object-contain rounded-lg">
                                                        <button 
                                                            type="button"
                                                            @click="removeIdFront"
                                                            class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
                                                        >
                                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <p v-if="errors.id_front" class="mt-1 text-sm text-red-600">
                                                {{ errors.id_front }}
                                            </p>
                                        </div>

                                        <!-- Back Side (Conditional) -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                {{ idBackLabel }} {{ requiresBackSide ? '*' : '(Optional)' }}
                                            </label>
                                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md"
                                                :class="{ 
                                                    'border-blue-500': idBackPreview,
                                                    'border-red-300': errors.id_back
                                                }">
                                                <div class="space-y-1 text-center">
                                                    <div v-if="!idBackPreview" class="flex flex-col items-center">
                                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                        <div class="flex text-sm text-gray-600">
                                                            <label for="id-back" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                                <span>Upload {{ idBackLabel.toLowerCase() }}</span>
                                                                <input 
                                                                    id="id-back" 
                                                                    type="file" 
                                                                    accept="image/*"
                                                                    @change="handleIdBack"
                                                                    class="sr-only"
                                                                    :required="requiresBackSide"
                                                                >
                                                            </label>
                                                            <p class="pl-1">or drag and drop</p>
                                                        </div>
                                                        <p class="text-xs text-gray-500">
                                                            PNG, JPG, JPEG up to 5MB
                                                        </p>
                                                    </div>
                                                    <div v-else class="relative">
                                                        <img :src="idBackPreview" :alt="idBackLabel + ' Preview'" class="mx-auto h-48 object-contain rounded-lg">
                                                        <button 
                                                            type="button"
                                                            @click="removeIdBack"
                                                            class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
                                                        >
                                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <p v-if="errors.id_back" class="mt-1 text-sm text-red-600">
                                                {{ errors.id_back }}
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4 text-sm text-gray-500">
                                        <p class="font-medium mb-2">Upload Requirements:</p>
                                        <p>• Ensure the document is valid and not expired</p>
                                        <p>• Upload clear photos with all details visible</p>
                                        <p>• Maximum file size: 5MB per image</p>
                                        <p>• Accepted formats: JPG, JPEG, PNG</p>
                                        <p v-if="form.id_type === 'Passport'" class="mt-2">
                                            <span class="font-medium">For Passport:</span> Upload the photo page with personal details
                                        </p>
                                        <p v-if="form.id_type === 'Ghana Card'" class="mt-2">
                                            <span class="font-medium">For Ghana Card:</span> Both front and back sides are required
                                        </p>
                                        <p v-if="form.id_type === 'Drivers License'" class="mt-2">
                                            <span class="font-medium">For Driver's License:</span> Both sides are required
                                        </p>
                                        <p v-if="form.id_type === 'Voters ID'" class="mt-2">
                                            <span class="font-medium">For Voter's ID:</span> Front side only is required
                                        </p>
                                    </div>
                                </div>

                                <!-- Address Information -->
                                <div class="md:col-span-2 mt-4">
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
                                        :class="{ 'border-red-300': errors.address }"
                                        placeholder="123 Main Street"
                                    >
                                    <p v-if="errors.address" class="mt-1 text-sm text-red-600">
                                        {{ errors.address }}
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
                                        :class="{ 'border-red-300': errors.city }"
                                        placeholder="New York"
                                    >
                                    <p v-if="errors.city" class="mt-1 text-sm text-red-600">
                                        {{ errors.city }}
                                    </p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        State *
                                    </label>
                                    <input 
                                        type="text"
                                        v-model="form.state"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-300': errors.state }"
                                        placeholder="NY"
                                    >
                                    <p v-if="errors.state" class="mt-1 text-sm text-red-600">
                                        {{ errors.state }}
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
                                        :class="{ 'border-red-300': errors.zip_code }"
                                        placeholder="10001"
                                    >
                                    <p v-if="errors.zip_code" class="mt-1 text-sm text-red-600">
                                        {{ errors.zip_code }}
                                    </p>
                                </div>

                                <!-- Financial Information -->
                                <div class="md:col-span-2 mt-4">
                                    <h3 class="text-lg font-medium text-gray-900 mb-4">Financial Information</h3>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Monthly Income ($)
                                    </label>
                                    <input 
                                        type="number"
                                        v-model="form.monthly_income"
                                        min="0"
                                        step="0.01"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-300': errors.monthly_income }"
                                        placeholder="5000.00"
                                    >
                                    <p v-if="errors.monthly_income" class="mt-1 text-sm text-red-600">
                                        {{ errors.monthly_income }}
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
                                        :class="{ 'border-red-300': errors.employment_status }"
                                    >
                                        <option value="">Select employment status</option>
                                        <option value="employed">Employed</option>
                                        <option value="self-employed">Self-Employed</option>
                                        <option value="unemployed">Unemployed</option>
                                        <option value="retired">Retired</option>
                                        <option value="student">Student</option>
                                    </select>
                                    <p v-if="errors.employment_status" class="mt-1 text-sm text-red-600">
                                        {{ errors.employment_status }}
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
                                        :class="{ 'border-red-300': errors.employment_details }"
                                        placeholder="Company name, position, years employed..."
                                    ></textarea>
                                    <p v-if="errors.employment_details" class="mt-1 text-sm text-red-600">
                                        {{ errors.employment_details }}
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
                                        :class="{ 'border-red-300': errors.credit_score }"
                                        placeholder="750"
                                    >
                                    <p v-if="errors.credit_score" class="mt-1 text-sm text-red-600">
                                        {{ errors.credit_score }}
                                    </p>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
                                <SecondaryButton 
                                    type="button"
                                    @click="$inertia.visit(route('customers.index'))"
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
                                    <span>{{ form.processing ? 'Creating...' : 'Create Customer' }}</span>
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
import { useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    errors: Object
});

// Reactive references for file previews
const passportPreview = ref(null);
const passportFile = ref(null);
const idFrontPreview = ref(null);
const idBackPreview = ref(null);
const idFrontFile = ref(null);
const idBackFile = ref(null);

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

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    address: '',
    city: '',
    state: '',
    zip_code: '',
    date_of_birth: '',
    id_type: '',
    id_number: '',
    monthly_income: '',
    employment_status: '',
    employment_details: '',
    credit_score: '',
    passport_picture: null,
    id_front: null,
    id_back: null
});

// File validation function
const validateFile = (file) => {
    // Validate file size (5MB limit)
    if (file.size > 5 * 1024 * 1024) {
        alert('File size must be less than 5MB');
        return false;
    }
    
    // Validate file type
    if (!file.type.startsWith('image/')) {
        alert('Please upload an image file (JPG, JPEG, PNG)');
        return false;
    }
    
    return true;
};

// Handle passport picture upload
const handlePassportPicture = (event) => {
    const file = event.target.files[0];
    if (file && validateFile(file)) {
        passportFile.value = file;
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
        idFrontFile.value = file;
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
        idBackFile.value = file;
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
    passportFile.value = null;
    form.passport_picture = null;
    
    const fileInput = document.getElementById('passport-picture');
    if (fileInput) fileInput.value = '';
};

// Remove ID front
const removeIdFront = () => {
    idFrontPreview.value = null;
    idFrontFile.value = null;
    form.id_front = null;
    
    const fileInput = document.getElementById('id-front');
    if (fileInput) fileInput.value = '';
};

// Remove ID back
const removeIdBack = () => {
    idBackPreview.value = null;
    idBackFile.value = null;
    form.id_back = null;
    
    const fileInput = document.getElementById('id-back');
    if (fileInput) fileInput.value = '';
};

const submit = () => {
    // Validate required fields
    if (!form.passport_picture) {
        alert('Please upload a passport picture');
        return;
    }
    
    if (!form.id_front) {
        alert(`Please upload ${idFrontLabel.value.toLowerCase()} of your ID`);
        return;
    }
    
    if (requiresBackSide.value && !form.id_back) {
        alert(`Please upload ${idBackLabel.value.toLowerCase()} of your ID`);
        return;
    }
    
    // Use post method with form data for file uploads
    form.transform((data) => ({
        ...data,
        passport_picture: data.passport_picture,
        id_front: data.id_front,
        id_back: data.id_back,
    })).post(route('customers.store'), {
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