<script setup>
import { usePage } from '@inertiajs/vue3';

const page = usePage();
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <nav class="bg-white shadow-md border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="/" class="text-xl font-bold text-gray-900">
                            Gerenciamento de Categorias
                        </a>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a
                            href="/"
                            class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 transition-colors"
                            :class="{ 'bg-gray-100': $page.url === '/' }"
                        >
                            Home
                        </a>
                        <a
                            href="/categories"
                            class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 transition-colors"
                            :class="{ 'bg-gray-100': $page.url.startsWith('/categories') }"
                        >
                            Categorias
                        </a>
                    </div>
                </div>
            </div>
        </nav>
        
        <main>
            <slot />
        </main>
    </div>
</template>

