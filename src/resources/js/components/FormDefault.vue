<script setup>
import { defineProps, defineEmits, onMounted, onBeforeUnmount, ref, reactive, watch } from 'vue';
import ButtonDefault from '@/components/ButtonDefault.vue'; 

const props = defineProps({
    fields: {
        type: Array,
        required: true,
    },
    submitText: {
        type: String,
        default: 'Enviar'
    }
});

// Verifica a largura da tela
const screenIsMobile = ref(window.innerWidth <= 640);
const handleResize = () => screenIsMobile.value = window.innerWidth <= 640;

const emit = defineEmits(['submit', 'close']);

const onSubmit = () => {
    emit('submit', form);
};

const onClose = () => {
    emit('close');
};

const form = reactive({});
const errors = reactive({});

const initForm = () => {
    Object.keys(form).forEach(key => delete form[key]);
    
    if (props.fields && Array.isArray(props.fields)) {
        props.fields.forEach(field => {
            if (field && field.name) form[field.name] = field.value || '';
        });
    }
};

onMounted(() => {
    window.addEventListener('resize', handleResize);
    initForm();
});

watch(() => props.fields, () => {
    initForm();
}, { deep: true });

onBeforeUnmount(() => window.removeEventListener('resize', handleResize));
</script>

<template>
    <form class="rounded-lg space-y-4 flex flex-col h-full" @submit.prevent="onSubmit">
        <div class="flex flex-wrap overflow-y-auto max-h-[70vh]">
            <template v-for="field in props.fields" :key="field?.name || field">
            <div 
                v-if="field && field.name"
                :style="{ width: screenIsMobile ? (field.widthMobile || '100%') : (field.width || '100%') }" 
                class="p-1"
            >
                <div>
                    <label :for="field.name" class="block text-[14px] font-medium text-gray-700">
                        {{ field.label }} 
                        <span v-if="field.required" class="text-red-500 text-[14px]">*</span>
                    </label>

                    <div v-if="field.type === 'text'">
                        <input 
                            :type="field.type || 'text'"
                            :id="field.name"
                            :name="field.name"
                            :placeholder="field.placeholder || ''"
                            v-model="form[field.name]"
                            class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm bg-white"
                        />
                    </div>

                    <div v-if="field.type === 'textarea'">
                        <textarea 
                            :id="field.name"
                            :name="field.name"
                            :placeholder="field.placeholder || ''"
                            v-model="form[field.name]"
                            class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                        />
                    </div>

                    <p v-if="errors[field.name]" class="text-red-500 text-[10px] mt-1">
                        {{ errors[field.name] }}
                    </p>
                </div>
            </div>
            </template>

            <slot />
        </div>

        <div class="mt-auto flex justify-end gap-2 pt-2 pl-4 border-t border-gray-200">
            <button 
                type="button" 
                class="rounded-md px-4 py-2 text-red-500 w-full sm:w-fit m-auto sm:m-0 sm:min-w-[140px] cursor-pointer border border-red-500 hover:bg-red-50 transition-colors"
                @click="onClose"
            >
                Cancelar
            </button>
            
            <ButtonDefault type="submit">
                {{ props.submitText || 'Enviar' }}
            </ButtonDefault>
        </div>
    </form>
</template>