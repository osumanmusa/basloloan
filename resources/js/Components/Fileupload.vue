<!-- FileUpload.vue -->
<template>
    <div>
        <!-- Upload Area -->
        <div 
            class="relative border-2 border-dashed rounded-lg p-6 text-center transition-all duration-200 ease-in-out hover:border-blue-400 hover:bg-blue-50"
            :class="[
                file ? 'border-blue-300 bg-blue-50' : 'border-gray-300',
                error ? 'border-red-300' : '',
                isDragging ? 'border-blue-400 bg-blue-100 scale-[1.02]' : ''
            ]"
            @dragover.prevent="isDragging = true"
            @dragleave.prevent="isDragging = false"
            @drop.prevent="handleDrop"
        >
            <!-- Preview for image files -->
            <div v-if="file && previewType === 'image' && file.type.startsWith('image/')" class="mb-4">
                <img 
                    :src="filePreview" 
                    alt="Preview" 
                    class="mx-auto h-32 w-32 object-cover rounded-lg shadow-sm border border-gray-200"
                />
            </div>

            <!-- Preview for PDF -->
            <div v-if="file && file.type === 'application/pdf'" class="mb-4">
                <div class="mx-auto h-32 w-32 bg-red-50 rounded-lg flex flex-col items-center justify-center border border-red-200">
                    <svg class="h-12 w-12 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd" />
                    </svg>
                    <span class="mt-2 text-xs font-medium text-red-600">PDF Document</span>
                </div>
            </div>

            <!-- Upload Icon -->
            <div class="flex flex-col items-center">
                <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center mb-3">
                    <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                </div>

                <!-- Instructions -->
                <div class="text-sm">
                    <p v-if="!file" class="text-gray-600">
                        <span class="font-medium text-blue-600">Click to upload</span>
                        or drag and drop
                    </p>
                    <p v-if="!file" class="text-xs text-gray-500 mt-1">
                        {{ accept.includes('image') && accept.includes('pdf') ? 'PNG, JPG, GIF or PDF' : 'PNG, JPG, GIF' }}
                        (Max: 5MB)
                    </p>
                    
                    <!-- File Info -->
                    <div v-if="file" class="mt-2">
                        <p class="font-medium text-gray-900 truncate">{{ file.name }}</p>
                        <p class="text-xs text-gray-500">
                            {{ formatFileSize(file.size) }}
                            • {{ file.type.split('/')[1]?.toUpperCase() || 'File' }}
                        </p>
                    </div>
                </div>

                <!-- File Input -->
                <input 
                    type="file"
                    :accept="accept"
                    :required="required"
                    @change="handleFileSelect"
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                />

                <!-- Change Button -->
                <button
                    v-if="file"
                    type="button"
                    @click.stop="$refs.fileInput.click()"
                    class="mt-4 text-sm font-medium text-blue-600 hover:text-blue-700 focus:outline-none"
                >
                    Change file
                </button>
            </div>
        </div>

        <!-- Hidden file input for change functionality -->
        <input 
            ref="fileInput"
            type="file"
            :accept="accept"
            @change="handleFileSelect"
            class="hidden"
        />

        <!-- Error Message -->
        <InputError :message="error" class="mt-2" />
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    file: [File, null],
    error: String,
    accept: {
        type: String,
        default: 'image/*,.pdf'
    },
    required: Boolean,
    previewType: {
        type: String,
        default: 'auto' // 'auto', 'image', 'none'
    }
});

const emit = defineEmits(['file-selected']);
const isDragging = ref(false);

const filePreview = computed(() => {
    if (props.file && props.file.type.startsWith('image/')) {
        return URL.createObjectURL(props.file);
    }
    return null;
});

const handleFileSelect = (event) => {
    const selectedFile = event.target.files[0];
    if (selectedFile) {
        emit('file-selected', selectedFile);
    }
    isDragging.value = false;
};

const handleDrop = (event) => {
    const droppedFile = event.dataTransfer.files[0];
    if (droppedFile) {
        // Check if file type is accepted
        const acceptedTypes = props.accept.split(',').map(type => type.trim());
        const isAccepted = acceptedTypes.some(type => {
            if (type.includes('*')) {
                const mimeType = type.replace('*', droppedFile.type.split('/')[1]);
                return droppedFile.type.startsWith(mimeType.split('/')[0]);
            }
            return droppedFile.type === type || droppedFile.name.endsWith(type.replace('.', ''));
        });

        if (isAccepted) {
            emit('file-selected', droppedFile);
        }
    }
    isDragging.value = false;
};

const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};
</script>