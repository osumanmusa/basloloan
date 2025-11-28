<template>
    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        Role Management
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Manage user roles and permissions</p>
                </div>
                <PrimaryButton 
                    v-if="$page.props.auth.can.create_roles"
                    @click="showCreateModal = true"
                >
                    Create New Role
                </PrimaryButton>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Roles Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div 
                        v-for="role in roles" 
                        :key="role.id"
                        class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow"
                    >
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-lg font-semibold text-gray-800">
                                {{ role.name }}
                            </h3>
                            <span class="text-sm text-gray-500 bg-gray-100 px-2 py-1 rounded">
                                {{ role.users_count }} users
                            </span>
                        </div>
                        
                        <p class="text-gray-600 text-sm mb-4">
                            {{ role.description || 'No description' }}
                        </p>
                        
                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">
                                Permissions ({{ role.permissions.length }}):
                            </h4>
                            <div class="flex flex-wrap gap-1 max-h-20 overflow-y-auto">
                                <span 
                                    v-for="permission in role.permissions"
                                    :key="permission.id"
                                    class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded"
                                >
                                    {{ permission.name }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="flex space-x-2" v-if="$page.props.auth.can.edit_roles">
                            <SecondaryButton @click="editRole(role)">
                                Edit
                            </SecondaryButton>
                            <DangerButton 
                                v-if="role.users_count === 0 && role.name !== 'admin' && $page.props.auth.can.delete_roles"
                                @click="deleteRole(role)"
                            >
                                Delete
                            </DangerButton>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create/Edit Role Modal -->
        <Modal :show="showCreateModal || showEditModal" @close="closeModal">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">
                    {{ showEditModal ? 'Edit Role' : 'Create New Role' }}
                </h3>
                
                <form @submit.prevent="showEditModal ? updateRole() : createRole()">
                    <div class="space-y-4">
                        <div>
                            <InputLabel for="name" value="Role Name" />
                            <TextInput
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="mt-1 block w-full"
                                required
                                :disabled="showEditModal && form.name === 'admin'"
                            />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div>
                            <InputLabel for="description" value="Description" />
                            <TextInput
                                id="description"
                                v-model="form.description"
                                type="text"
                                class="mt-1 block w-full"
                            />
                            <InputError class="mt-2" :message="form.errors.description" />
                        </div>

                        <div>
                            <InputLabel value="Permissions" />
                            <div class="mt-2 border border-gray-200 rounded-lg p-4 max-h-64 overflow-y-auto">
                                <div class="space-y-3">
                                    <div v-for="(modulePermissions, module) in groupedPermissions" :key="module" class="border-b pb-3 last:border-b-0">
                                        <h5 class="font-medium text-gray-700 mb-2 capitalize">{{ module }}</h5>
                                        <div class="grid grid-cols-2 gap-2">
                                            <label 
                                                v-for="permission in modulePermissions" 
                                                :key="permission.id"
                                                class="flex items-center space-x-2"
                                            >
                                                <input 
                                                    type="checkbox" 
                                                    :value="permission.name" 
                                                    v-model="form.permissions"
                                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                                >
                                                <span class="text-sm text-gray-700">{{ permission.name }}</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <InputError class="mt-2" :message="form.errors.permissions" />
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3 mt-6">
                        <SecondaryButton @click="closeModal" type="button">
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton :disabled="form.processing">
                            {{ showEditModal ? 'Update Role' : 'Create Role' }}
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
import { ref, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    roles: Array,
    permissions: Object
});

const showCreateModal = ref(false);
const showEditModal = ref(false);
const editingRole = ref(null);

const form = useForm({
    name: '',
    description: '',
    permissions: []
});

const groupedPermissions = computed(() => {
    return props.permissions;
});

const editRole = (role) => {
    editingRole.value = role;
    form.name = role.name;
    form.description = role.description || '';
    form.permissions = role.permissions.map(p => p.name);
    showEditModal.value = true;
};

const createRole = () => {
    form.post(route('roles.store'), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            form.reset();
        }
    });
};

const updateRole = () => {
    form.put(route('roles.update', editingRole.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            form.reset();
        }
    });
};

const deleteRole = (role) => {
    if (confirm(`Are you sure you want to delete the role "${role.name}"?`)) {
        router.delete(route('roles.destroy', role.id));
    }
};

const closeModal = () => {
    showCreateModal.value = false;
    showEditModal.value = false;
    editingRole.value = null;
    form.reset();
    form.clearErrors();
};
</script>