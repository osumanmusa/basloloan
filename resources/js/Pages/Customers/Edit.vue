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
                <SecondaryButton @click="$inertia.visit(route('customers.index'))">
                    Back to Customers
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
                                        placeholder="+1 (555) 123-4567"
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
                                        ID Number *
                                    </label>
                                    <input 
                                        type="text"
                                        v-model="form.id_number"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                        :class="{ 'border-red-300': form.errors.id_number }"
                                        placeholder="A123456789"
                                    >
                                    <p v-if="form.errors.id_number" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.id_number }}
                                    </p>
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
                                        placeholder="New York"
                                    >
                                    <p v-if="form.errors.city" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.city }}
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
                                        :class="{ 'border-red-300': form.errors.state }"
                                        placeholder="NY"
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
                                        placeholder="10001"
                                    >
                                    <p v-if="form.errors.zip_code" class="mt-1 text-sm text-red-600">
                                        {{ form.errors.zip_code }}
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

const props = defineProps({
    customer: Object
});

// Format date for input field (YYYY-MM-DD)
const formatDateForInput = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toISOString().split('T')[0];
};

const form = useForm({
    first_name: props.customer.first_name,
    last_name: props.customer.last_name,
    email: props.customer.email,
    phone: props.customer.phone,
    address: props.customer.address,
    city: props.customer.city,
    state: props.customer.state,
    zip_code: props.customer.zip_code,
    date_of_birth: formatDateForInput(props.customer.date_of_birth),
    id_number: props.customer.id_number,
    monthly_income: props.customer.monthly_income,
    employment_status: props.customer.employment_status,
    employment_details: props.customer.employment_details,
    credit_score: props.customer.credit_score
});

const submit = () => {
    form.put(route('customers.update', props.customer.id));
};
</script>