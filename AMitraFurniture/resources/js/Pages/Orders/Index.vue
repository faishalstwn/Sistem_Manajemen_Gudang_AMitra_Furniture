<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import UserLayout from '@/Layouts/UserLayout.vue'
import Pagination from '@/Components/Pagination.vue'

defineOptions({ layout: UserLayout })

const props = defineProps({
    orders: Object,
})

const statusLabel = {
    pending: 'Menunggu',
    processing: 'Diproses',
    shipped: 'Dikirim',
    delivered: 'Diterima',
    cancelled: 'Dibatalkan',
}

const paymentLabel = {
    pending: 'Belum Bayar',
    paid: 'Sudah Bayar',
    failed: 'Gagal',
    expired: 'Kadaluarsa',
}
</script>

<template>
    <Head title="Riwayat Pesanan" />

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-8">Riwayat Pesanan</h1>

        <!-- Empty -->
        <div v-if="!orders?.data?.length" class="text-center py-20">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <h2 class="text-lg font-semibold text-gray-900 mb-2">Belum ada pesanan</h2>
            <p class="text-gray-500 text-sm mb-6">Mulai belanja untuk melihat riwayat pesanan Anda</p>
            <Link href="/" class="inline-block px-6 py-3 bg-[#1565C0] hover:bg-[#0D47A1] text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition-all">
                Belanja Sekarang
            </Link>
        </div>

        <!-- Order List -->
        <div v-else class="space-y-4">
            <Link v-for="order in orders.data" :key="order.id"
                  :href="`/orders/${order.id}`"
                  class="block bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-md hover:border-[#1565C0]/30 transition-all duration-200">
                <!-- Header -->
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <p class="text-xs font-mono text-gray-400">{{ order.order_code }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ new Date(order.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}</p>
                    </div>
                    <div class="flex gap-2">
                        <span class="text-[10px] font-semibold px-2.5 py-1 rounded-full"
                              :class="{
                                  'badge-pending': order.payment_status === 'pending',
                                  'badge-paid': order.payment_status === 'paid',
                                  'badge-failed': order.payment_status === 'failed',
                              }">
                            {{ paymentLabel[order.payment_status] || order.payment_status }}
                        </span>
                        <span class="text-[10px] font-semibold px-2.5 py-1 rounded-full"
                              :class="{
                                  'badge-pending': order.status === 'pending',
                                  'badge-processing': order.status === 'processing',
                                  'badge-shipped': order.status === 'shipped',
                                  'badge-delivered': order.status === 'delivered',
                                  'badge-cancelled': order.status === 'cancelled',
                              }">
                            {{ statusLabel[order.status] || order.status }}
                        </span>
                    </div>
                </div>

                <!-- Items Preview -->
                <div class="flex items-center gap-3">
                    <div class="flex -space-x-2">
                        <template v-for="(item, i) in order.order_items?.slice(0, 3)" :key="item.id">
                            <div class="w-10 h-10 rounded-lg border-2 border-white overflow-hidden bg-gray-100">
                                <img v-if="item.product?.image" :src="`/${item.product.image}`" class="w-full h-full object-cover" />
                            </div>
                        </template>
                        <div v-if="order.order_items?.length > 3"
                             class="w-10 h-10 rounded-lg border-2 border-white bg-gray-100 flex items-center justify-center text-xs font-medium text-gray-500">
                            +{{ order.order_items.length - 3 }}
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-gray-600 truncate">
                            {{ order.order_items?.map(i => i.product?.name).filter(Boolean).join(', ') || 'Produk' }}
                        </p>
                    </div>
                    <p class="text-lg font-bold text-[#1565C0] shrink-0">
                        Rp {{ Number(order.total_price).toLocaleString('id-ID') }}
                    </p>
                </div>
            </Link>

            <Pagination :links="orders.links" />
        </div>
    </div>
</template>
