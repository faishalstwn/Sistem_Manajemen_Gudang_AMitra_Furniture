<script setup>
import { computed } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import FlashMessage from '@/Components/FlashMessage.vue'

const page = usePage()
const user = computed(() => page.props.auth?.user)
</script>

<template>
    <div class="min-h-screen bg-gray-50 flex flex-col items-center justify-center p-4">
        <!-- Logo -->
        <div class="mb-8 text-center">
            <Link href="/" class="inline-flex items-center gap-2">
                <div class="w-10 h-10 bg-[#FFD600] rounded-xl flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-[#CC1F1A]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1" />
                    </svg>
                </div>
                <span class="text-xl font-bold text-[#1565C0]">A Mitra Furniture</span>
            </Link>
        </div>

        <!-- Flash Messages -->
        <FlashMessage />

        <!-- Content -->
        <div class="w-full max-w-md">
            <slot />
        </div>

        <!-- Footer -->
        <p class="mt-8 text-sm text-gray-400">
            &copy; {{ new Date().getFullYear() }} A Mitra Furniture. All rights reserved.
        </p>
    </div>
</template>
