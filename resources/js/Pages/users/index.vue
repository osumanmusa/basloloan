<template>
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        User Management
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Manage system users and their roles</p>
                </div>
                <PrimaryButton 
                    v-if="$page.props.auth.can.create_users"
                    @click="showCreateModal = true"
                >
                    Add New User
                </PrimaryButton>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <!-- Users Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            User
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Email
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Roles
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Last Login
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="user in users.data" :key="user.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                        <span class="text-blue-800 font-semibold">
                                                            {{ user.name.charAt(0).toUpperCase() }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ user.name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        Joined {{ formatDate(user.created_at) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ user.email }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex flex-wrap gap-1">
                                                <span 
                                                    v-for="role in user.roles" 
                                                    :key="role"
                                                    class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800"
                                                >
                                                    {{ role }}
                                                </span>
                                                <span v-if="user.roles.length === 0" class="text-xs text-gray-500">
                                                    No roles assigned
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDate(user.last_login_at) || 'Never' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            <button 
                                                v-if="$page.props.auth.can.assign_roles"
                                                @click="editUserRoles(user)"
                                                class="text-blue-600 hover:text-blue-900"
                                            >
                                                Edit Roles
                                            </button>
                                            <button 
                                                v-if="user.id !== $page.props.auth.user.id && $page.props.auth.can.edit_users"
                                                @click="editUser(user)"
                                                class="text-green-600 hover:text-green-900"
                                            >
                                                Edit
                                            </button>
                                            <button 
                                                v-if="user.id !== $page.props.auth.user.id && $page.props.auth.can.delete_users"
                                                @click="deleteUser(user)"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <Pagination :links="users.links" class="mt-6" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit User Roles Modal -->
        <Modal :show="showRoleModal" @close="closeRoleModal">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">Edit Roles for {{ editingUser?.name }}</h3>
                
                <div class="space-y-3 mb-6">
                    <div v-for="role in roles" :key="role.id" class="flex items-center">
                        <input 
                            type="checkbox" 
                            :value="role.name" 
                            v-model="roleForm.roles"
                            :id="`role-${role.id}`"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                        >
                        <label :for="`role-${role.id}`" class="ml-2 text-sm text-gray-700">
                            {{ role.name }}
                            <span class="text-gray-500 text-xs ml-1">
                                ({{ role.users_count }} users)
                            </span>
                        </label>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <SecondaryButton @click="closeRoleModal">
                        Cancel
                    </SecondaryButton>
                    <PrimaryButton @click="updateUserRoles" :disabled="roleForm.processing">
                        Update Roles
                    </PrimaryButton>
                </div>
            </div>
        </Modal>

        <!-- Create/Edit User Modal -->
        <Modal :show="showCreateModal || showEditModal" @close="closeUserModal">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">
                    {{ showEditModal ? 'Edit User' : 'Create New User' }}
                </h3>
                
                <form @submit.prevent="showEditModal ? updateUser() : createUser()">
                    <div class="space-y-4">
                        <div>
                            <InputLabel for="name" value="Name" />
                            <TextInput
                                id="name"
                                v-model="userForm.name"
                                type="text"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError class="mt-2" :message="userForm.errors.name" />
                        </div>

                        <div>
                            <InputLabel for="email" value="Email" />
                            <TextInput
                                id="email"
                                v-model="userForm.email"
                                type="email"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError class="mt-2" :message="userForm.errors.email" />
                        </div>

                        <div v-if="!showEditModal">
                            <InputLabel for="password" value="Password" />
                            <TextInput
                                id="password"
                                v-model="userForm.password"
                                type="password"
                                class="mt-1 block w-full"
                                required
                            />
                            <InputError class="mt-2" :message="userForm.errors.password" />
                        </div>

                        <div v-if="!showEditModal">
                            <InputLabel for="password_confirmation" value="Confirm Password" />
                            <TextInput
                                id="password_confirmation"
                                v-model="userForm.password_confirmation"
                                type="password"
                                class="mt-1 block w-full"
                                required
                            />
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3 mt-6">
                        <SecondaryButton @click="closeUserModal" type="button">
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton :disabled="userForm.processing">
                            {{ showEditModal ? 'Update User' : 'Create User' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import Pagination from '@/Components/Pagination.vue';
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    users: Object,
    roles: Array
});

const showCreateModal = ref(false);
const showEditModal = ref(false);
const showRoleModal = ref(false);
const editingUser = ref(null);

const userForm = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
});

const roleForm = useForm({
    roles: []
});

const formatDate = (date) => {
    if (!date) return null;
    return new Date(date).toLocaleDateString();
};

const editUserRoles = (user) => {
    editingUser.value = user;
    roleForm.roles = user.roles;
    showRoleModal.value = true;
};

const updateUserRoles = () => {
    roleForm.put(route('users.roles.update', editingUser.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeRoleModal();
        }
    });
};

const editUser = (user) => {
    editingUser.value = user;
    userForm.name = user.name;
    userForm.email = user.email;
    showEditModal.value = true;
};

const createUser = () => {
    userForm.post(route('users.store'), {
        preserveScroll: true,
        onSuccess: () => {
            closeUserModal();
        }
    });
};

const updateUser = () => {
    userForm.put(route('users.update', editingUser.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeUserModal();
        }
    });
};

const deleteUser = (user) => {
    if (confirm(`Are you sure you want to delete user "${user.name}"?`)) {
        router.delete(route('users.destroy', user.id));
    }
};

const closeRoleModal = () => {
    showRoleModal.value = false;
    editingUser.value = null;
    roleForm.reset();
    roleForm.clearErrors();
};

const closeUserModal = () => {
    showCreateModal.value = false;
    showEditModal.value = false;
    editingUser.value = null;
    userForm.reset();
    userForm.clearErrors();
};
</script>