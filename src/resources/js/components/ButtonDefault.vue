<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    type: {
        type: String,
        default: 'button',
        validator: (value) => ['button', 'submit', 'reset'].includes(value)
    },
    disabled: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['click']);

const onClick = (event) => {
    if (!props.disabled) {
        emit('click', event);
    }
};
</script>

<template>
    <button
        :type="type"
        class="rounded-md bg-blue-600 px-4 py-2 text-white w-full sm:w-fit m-auto sm:m-0 sm:min-w-[140px] cursor-pointer hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
        :disabled="disabled"
        @click="onClick"
    >
        <slot />
    </button>
</template>
