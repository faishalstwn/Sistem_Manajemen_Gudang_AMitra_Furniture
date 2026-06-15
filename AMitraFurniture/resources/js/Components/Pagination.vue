<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    links: {
        type: Array,
        required: true,
    },
})

const filteredLinks = computed(() => {
    // Remove first (prev) and last (next) — we'll handle them separately
    if (!props.links || props.links.length <= 2) return []
    return props.links.slice(1, -1)
})

const prevLink = computed(() => props.links?.[0] ?? null)
const nextLink = computed(() => props.links?.[props.links.length - 1] ?? null)
</script>

<template>
    <nav v-if="links && links.length > 3" class="flex items-center justify-center gap-1.5 mt-8">
        <!-- Previous -->
        <component
            :is="prevLink?.url ? Link : 'span'"
            :href="prevLink?.url"
            class="flex items-center justify-center w-9 h-9 rounded-lg text-sm transition-colors"
            :class="prevLink?.url
                ? 'text-gray-600 hover:bg-gray-100 cursor-pointer'
                : 'text-gray-300 cursor-not-allowed'"
            preserve-scroll
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </component>

        <!-- Page Numbers -->
        <template v-for="(link, index) in filteredLinks" :key="index">
            <span v-if="link.label === '...'" class="px-2 text-gray-400 text-sm">...</span>
            <component
                v-else
                :is="link.url ? Link : 'span'"
                :href="link.url"
                class="flex items-center justify-center min-w-[36px] h-9 px-2 rounded-lg text-sm font-medium transition-all duration-200"
                :class="link.active
                    ? 'bg-[#1565C0] text-white shadow-md shadow-blue-200/50'
                    : link.url
                        ? 'text-gray-600 hover:bg-gray-100'
                        : 'text-gray-300'"
                preserve-scroll
                v-html="link.label"
            />
        </template>

        <!-- Next -->
        <component
            :is="nextLink?.url ? Link : 'span'"
            :href="nextLink?.url"
            class="flex items-center justify-center w-9 h-9 rounded-lg text-sm transition-colors"
            :class="nextLink?.url
                ? 'text-gray-600 hover:bg-gray-100 cursor-pointer'
                : 'text-gray-300 cursor-not-allowed'"
            preserve-scroll
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </component>
    </nav>
</template>
