<template>
    <div class="flex items-center justify-center w-full transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0 min-h-screen p-6 lg:p-8">
        <div class="w-full lg:max-w-4xl max-w-[335px] mt-6 p-6 bg-white dark:bg-[#161615] rounded-lg shadow-lg">
            <h2 class="text-lg font-medium mb-4 dark:text-[#EDEDEC]">
              {{ message }}
            </h2>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const message = ref('Hello World!');
</script>

