<script setup>
import UserLayout from '@/Layouts/UserLayout.vue'

defineOptions({ layout: UserLayout })

const props = defineProps({ order: Object })
</script>

<template>
    <Head title="Pembayaran Berhasil" />

    <div class="max-w-lg mx-auto px-4 py-16 text-center">
        <div class="bg-white rounded-2xl border border-gray-100 p-8 shadow-lg animate-fade-in-up">
            <!-- Success Icon -->
            <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 mb-2">Pembayaran Berhasil! 🎉</h1>
            <p class="text-gray-500 text-sm mb-8">
                Terima kasih atas pesanan Anda. Pesanan sedang diproses.
            </p>

            <!-- Order Summary -->
            <div class="bg-gray-50 rounded-xl p-5 text-left text-sm space-y-3 mb-8">
                <div class="flex justify-between">
                    <span class="text-gray-500">Kode Pesanan</span>
                    <span class="font-mono font-semibold text-gray-900">{{ order.order_code }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Total Pembayaran</span>
                    <span class="font-bold text-emerald-600">Rp {{ Number(order.total_price).toLocaleString('id-ID') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Metode</span>
                    <span class="font-medium text-gray-900 uppercase">{{ order.payment_method }}</span>
                </div>
            </div>

            <!-- Items -->
            <div v-if="order.order_items?.length" class="text-left mb-8">
                <h3 class="text-sm font-semibold text-gray-700 mb-3">Produk Dipesan</h3>
                <div class="space-y-2">
                    <div v-for="item in order.order_items" :key="item.id" class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg overflow-hidden bg-gray-100 shrink-0">
                            <img v-if="item.product?.image" :src="`/${item.product.image}`" class="w-full h-full object-cover" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900 truncate">{{ item.product?.name }}</p>
                            <p class="text-xs text-gray-500">{{ item.quantity }}x</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col gap-3">
                <Link :href="`/orders/${order.id}`"
                      class="w-full py-3 bg-[#1565C0] hover:bg-[#0D47A1] text-white font-semibold rounded-xl shadow-lg shadow-blue-200/50 transition-all text-center">
                    Lihat Detail Pesanan
                </Link>
                <Link href="/"
                      class="w-full py-3 border border-gray-200 text-gray-700 hover:bg-gray-50 font-medium rounded-xl transition-colors text-center">
                    Lanjut Belanja
                </Link>
            </div>
        </div>
    </div>
</template>
