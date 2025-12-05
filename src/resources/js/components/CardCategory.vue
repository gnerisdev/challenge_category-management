<script setup> 
import { defineProps, defineEmits, computed } from 'vue';

const props = defineProps({ 
    category: { 
        default: () => ({}),
        type: Object,
        required: true,
    },
    index: {
        type: Number,
        default: 0
    },
    total: {
        type: Number,
        default: 0
    }
});

const emit = defineEmits(['edit', 'delete', 'add-subcategory', 'move-up', 'move-down']);

const hasParent = computed(() => {
    return props.category.parent_id !== null && props.category.parent_id !== undefined;
});

const onEdit = () => emit('edit', props.category);
const onDelete = () => emit('delete', props.category.id);
const onCreateSubcategory = () => emit('add-subcategory', props.category);
const handleMoveUp = (category = null) => emit('move-up', category || props.category);
const handleMoveDown = (category = null) => emit('move-down', category || props.category);
</script>

<template>
    <div
        :key="props.category.id"
        class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6"
    >
        <div class="flex items-start justify-between mb-4">
            <h2 class="font-semibold text-gray-900">{{ props.category.name }}</h2>
            <div class="flex items-center gap-2">
                <div class="flex gap-1">
                    <button
                        v-if="hasParent"
                        @click="handleMoveUp"
                        :disabled="props.index === 0"
                        class="p-1 text-gray-400 transition-colors hover:text-gray-600"
                        :class="{ 'opacity-30 cursor-not-allowed': props.index === 0 }"
                        title="Mover para cima"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                        </svg>
                    </button>
                    <button
                        v-if="hasParent"
                        @click="handleMoveDown"
                        :disabled="props.index === props.total - 1"
                        class="p-1 text-gray-400 transition-colors hover:text-gray-600"
                        :class="{ 'opacity-30 cursor-not-allowed': props.index === props.total - 1 }"
                        title="Mover para baixo"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <button
                        v-if="!hasParent"
                        @click="handleMoveUp"
                        :disabled="props.index === 0"
                        class="p-1 text-gray-400 transition-colors hover:text-gray-600"
                        :class="{ 'opacity-30 cursor-not-allowed': props.index === 0 }"
                        title="Mover para esquerda"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <button
                        v-if="!hasParent"
                        @click="handleMoveDown"
                        :disabled="props.index === props.total - 1"
                        class="p-1 text-gray-400 transition-colors hover:text-gray-600"
                        :class="{ 'opacity-30 cursor-not-allowed': props.index === props.total - 1 }"
                        title="Mover para direita"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
                <button
                    @click="onEdit"
                    class="p-2 text-gray-400 hover:text-blue-600 transition-colors"
                    title="Editar"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </button>
                <button
                    @click="onDelete"
                    class="p-2 text-gray-400 hover:text-red-600 transition-colors"
                    title="Excluir"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>
        <p class="text-gray-600 text-sm mb-4 line-clamp-3">
            {{ props.category?.description || '-' }}
        </p>

    <details v-if="props.category.children && props.category.children.length > 0" class="mt-4">
        <summary>Subcategorias</summary>
        <div class="space-y-3 pl-4 border-l-2 border-gray-200">
            <div 
                v-for="(child, childIndex) in props.category.children" 
                :key="child.id" 
                class="bg-white rounded-lg border border-gray-200 p-4"
            >  
                <div class="flex items-start justify-between mb-2">
                    <div class="flex items-center gap-2 flex-1">
                        <h3 class="font-medium text-gray-800 text-sm">{{ child.name }}</h3>
                    </div>
                    <div class="flex items-center gap-1">
                        <button
                            @click="handleMoveUp(child)"
                            :disabled="childIndex === 0"
                            class="p-1 text-gray-400 transition-colors hover:text-gray-600"
                            :class="{ 'opacity-30 cursor-not-allowed': childIndex === 0 }"
                            title="Mover para cima"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />
                            </svg>
                        </button>
                        <button
                            @click="handleMoveDown(child)"
                            :disabled="childIndex === props.category.children.length - 1"
                            class="p-1 text-gray-400 transition-colors hover:text-gray-600"
                            :class="{ 'opacity-30 cursor-not-allowed': childIndex === props.category.children.length - 1 }"
                            title="Mover para baixo"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <button
                            @click="$emit('edit', child)"
                            class="p-1.5 text-gray-400 hover:text-blue-600 transition-colors"
                            title="Editar"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </button>
                        <button
                            @click="$emit('delete', child.id)"
                            class="p-1.5 text-gray-400 hover:text-red-600 transition-colors"
                            title="Excluir"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>
                </div>
                <p v-if="child.description" class="text-gray-600 text-xs ml-3">
                    {{ child.description }}
                </p>
            </div>
        </div>
    </details>
        
    <div class="flex justify-end mt-4">
        <button
            @click="onCreateSubcategory"
            class="px-4 py-2 text-sm bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors flex items-center gap-2"
            title="Adicionar Subcategoria"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add Subcategoria
        </button>
    </div>
    </div>
</template>