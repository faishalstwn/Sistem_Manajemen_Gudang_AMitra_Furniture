<script setup>
import { computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import UserLayout from '@/Layouts/UserLayout.vue'

defineOptions({ layout: UserLayout })

const props = defineProps({
    cartItems: Array,
    total: Number,
})

function updateQuantity(cartId, quantity) {
    router.patch(`/cart/${cartId}`, { quantity }, {
        preserveScroll: true,
        preserveState: true,
    })
}

function removeItem(cartId) {
    if (confirm('Hapus produk ini dari keranjang?')) {
        router.delete(`/cart/${cartId}`, {
            preserveScroll: true,
        })
    }
}

const isEmpty = computed(() => !props.cartItems?.length)
</script>

<template>
    <Head title="Keranjang" />

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-8">Keranjang Belanja</h1>

        <!-- Empty Cart -->
        <div v-if="isEmpty" class="text-center py-20">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />
                </svg>
            </div>
            <h2 class="text-lg font-semibold text-gray-900 mb-2">Keranjang kosong</h2>
            <p class="text-gray-500 text-sm mb-6">Yuk, mulai belanja furniture impianmu!</p>
            <Link href="/" class="inline-block px-6 py-3 bg-gradient-to-r from-[#1565C0] to-[#1976D2] hover:from-[#0D47A1] hover:to-[#1565C0] text-white font-semibold rounded-xl shadow-md shadow-blue-200/50 hover:shadow-lg transition-all">
                Mulai Belanja
            </Link>
        </div>

        <!-- Cart Items -->
        <div v-else class="space-y-4">
            <div v-for="item in cartItems" :key="item.id"
                 class="bg-white rounded-2xl border border-gray-100 p-4 md:p-5 flex gap-4 items-start hover:shadow-md transition-shadow">
                <!-- Product Image -->
                <Link :href="`/produk/${item.product.id}/detail`" class="shrink-0">
                    <div class="w-20 h-20 md:w-24 md:h-24 rounded-xl overflow-hidden bg-gray-100">
                        <img v-if="item.product.image" :src="`/${item.product.image}`" :alt="item.product.name"
                             class="w-full h-full object-cover" />
                        <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </Link>

                <!-- Info -->
                <div class="flex-1 min-w-0">
                    <Link :href="`/produk/${item.product.id}/detail`" class="text-sm md:text-base font-semibold text-gray-900 hover:text-[#1565C0] transition-colors line-clamp-2">
                        {{ item.product.name }}
                    </Link>
                    <p class="text-lg font-bold text-[#1565C0] mt-1">
                        Rp {{ Number(item.product.price).toLocaleString('id-ID') }}
                    </p>

                    <!-- Quantity Controls -->
                    <div class="flex items-center justify-between mt-3">
                        <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden">
                            <button @click="updateQuantity(item.id, Math.max(1, item.quantity - 1))"
                                    class="px-3 py-1.5 text-gray-600 hover:bg-gray-50 text-sm transition-colors">−</button>
                            <span class="px-4 py-1.5 text-sm font-semibold border-x border-gray-200 min-w-[40px] text-center">{{ item.quantity }}</span>
                            <button @click="updateQuantity(item.id, item.quantity + 1)"
                                    class="px-3 py-1.5 text-gray-600 hover:bg-gray-50 text-sm transition-colors">+</button>
                        </div>
                        <button @click="removeItem(item.id)"
                                class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>
                    </div>

                    <!-- Subtotal -->
                    <p class="text-xs text-gray-500 mt-2">
                        Subtotal: <span class="font-semibold text-gray-700">Rp {{ (item.product.price * item.quantity).toLocaleString('id-ID') }}</span>
                    </p>
                </div>
            </div>

            <!-- Total & Checkout -->
            <div class="sticky bottom-16 md:bottom-0 bg-white rounded-2xl border border-gray-100 shadow-xl p-5 mt-6">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-gray-600 font-medium">Total ({{ cartItems.length }} item)</span>
                    <span class="text-2xl font-extrabold text-[#1565C0]">Rp {{ Number(total).toLocaleString('id-ID') }}</span>
                </div>
                <Link href="/checkout"
                      class="block w-full py-3.5 bg-gradient-to-r from-[#1565C0] to-[#1976D2] hover:from-[#0D47A1] hover:to-[#1565C0] text-white text-center font-semibold rounded-xl shadow-lg shadow-blue-200/50 transition-all duration-200 hover:shadow-xl">
                    Checkout
                </Link>
            </div>
        </div>
    </div>
</template>
