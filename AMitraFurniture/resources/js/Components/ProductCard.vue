<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    product: {
        type: Object,
        required: true,
    },
})
</script>

<template>
    <Link :href="`/produk/${product.id}/detail`"
          class="product-card group block bg-white rounded-2xl overflow-hidden border border-gray-100 hover:border-[#1565C0]/30">
        <!-- Image -->
        <div class="relative aspect-square overflow-hidden bg-gray-100">
            <img
                v-if="product.image"
                :src="`/${product.image}`"
                :alt="product.name"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                loading="lazy"
            />
            <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>

            <!-- Category Badge -->
            <span class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm text-xs font-medium text-gray-600 px-2.5 py-1 rounded-full">
                {{ product.category }}
            </span>

            <!-- Stock indicator -->
            <div v-if="product.stock <= 0"
                 class="absolute inset-0 bg-black/40 flex items-center justify-center">
                <span class="bg-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-full">Habis</span>
            </div>
            <div v-else-if="product.stock < 5"
                 class="absolute top-3 right-3">
                <span class="bg-[#FFD600] text-[#0D47A1] text-[10px] font-bold px-2 py-1 rounded-full">Sisa {{ product.stock }}</span>
            </div>
        </div>

        <!-- Info -->
        <div class="p-4">
            <h3 class="font-semibold text-gray-900 text-sm leading-snug line-clamp-2 group-hover:text-[#1565C0] transition-colors">
                {{ product.name }}
            </h3>
            <p class="mt-2 text-lg font-bold text-[#1565C0]">
                Rp {{ Number(product.price).toLocaleString('id-ID') }}
            </p>
        </div>
    </Link>
</template>
