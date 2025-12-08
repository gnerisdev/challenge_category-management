<script setup>
import { defineProps, defineEmits } from 'vue';

const props = defineProps({
    category: {
        type: Object,
        required: true
    },
    isSubcategory: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['restore', 'delete']);

const onRestore = () => emit('restore', props.category.id);
const onDelete = () => emit('delete', props.category.id);
</script>

<template>
    <div class="bg-white rounded-lg shadow-md p-4 border-l-4" :class="isSubcategory ? 'border-orange-500' : 'border-red-500'">
        <div class="flex items-start justify-between">
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                    <h3 class="font-medium text-lg text-gray-800">{{ category.name }}</h3>
                    <span v-if="isSubcategory && category.parent" class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                        Categoria: {{ category.parent.name }}
                    </span>
                </div>
                <p v-if="category.description" class="text-gray-600 text-sm mb-2">{{ category.description }}</p>
            </div>
            <div class="flex items-center gap-2 ml-4">
                <button
                    @click="onRestore"
                    class="px-3 py-1.5 text-sm bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors flex items-center gap-1"
                    title="Restaurar"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Restaurar
                </button>
                <button
                    @click="onDelete"
                    class="px-3 py-1.5 text-sm bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors flex items-center gap-1"
                    title="Excluir Permanentemente"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Excluir
                </button>
            </div>
        </div>

        <div v-if="category.children && category.children.length > 0" class="mt-4 pl-4 border-l-2 border-gray-200 space-y-2">
            <div
                v-for="child in category.children"
                :key="child.id"
                class="bg-gray-50 rounded-lg p-3 border-l-2 border-orange-400"
            >
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <h4 class="font-medium text-gray-800 text-sm">{{ child.name }}</h4>
                        </div>
                        <p v-if="child.description" class="text-gray-600 text-xs mb-1">{{ child.description }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
