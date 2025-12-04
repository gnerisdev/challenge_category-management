<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    show: {
        type: Boolean,
        required: true,
    },
});

const emit = defineEmits(['close']);

const close = () => {
    emit('close');
};
</script>

<template>
    <div v-if="show" class="fixed inset-0 flex justify-center items-center z-50" style="background-color: rgba(0, 0, 0, 0.5);"> 
        <div class="bg-white rounded-lg shadow-md p-6 mx-4 max-h-[90vh] overflow-y-auto" style="max-width: 800px; width: 100%;"> 
            <header class="flex items-center justify-between mb-4 pb-4 border-b border-gray-200">   
                <h2 class="text-xl font-bold text-gray-900">
                    {{ title }}
                </h2>
                <button 
                    class="p-2 text-gray-400 hover:text-gray-600 transition-colors rounded-lg hover:bg-gray-100" 
                    @click="close"
                    title="Fechar"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </header>

            <slot />
        </div>
    </div>
</template>