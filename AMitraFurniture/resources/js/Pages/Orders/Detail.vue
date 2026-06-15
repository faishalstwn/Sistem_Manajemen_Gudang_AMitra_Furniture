<script setup>
import { router } from '@inertiajs/vue3'
import UserLayout from '@/Layouts/UserLayout.vue'

defineOptions({ layout: UserLayout })

const props = defineProps({
    order: Object,
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

function confirmPayment() {
    if (confirm('Konfirmasi bahwa Anda sudah melakukan pembayaran?')) {
        router.post(`/orders/${props.order.id}/confirm-payment`)
    }
}
</script>

<template>
    <Head :title="`Pesanan ${order.order_code}`" />

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Back -->
        <Link href="/orders" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-[#1565C0] mb-6 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </Link>

        <!-- Order Header -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-mono text-gray-400">{{ order.order_code }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ new Date(order.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' }) }}</p>
                </div>
                <div class="flex flex-col gap-1.5 items-end">
                    <span class="text-xs font-semibold px-3 py-1 rounded-full"
                          :class="{
                              'badge-pending': order.payment_status === 'pending',
                              'badge-paid': order.payment_status === 'paid',
                              'badge-failed': order.payment_status === 'failed',
                          }">
                        {{ paymentLabel[order.payment_status] || order.payment_status }}
                    </span>
                    <span class="text-xs font-semibold px-3 py-1 rounded-full"
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

            <!-- Payment Method -->
            <div class="mt-4 flex items-center gap-4 text-sm text-gray-600">
                <span>Metode: <strong class="text-gray-900 uppercase">{{ order.payment_method }}</strong></span>
            </div>

            <!-- Actions -->
            <div class="mt-4 flex gap-3">
                <Link v-if="order.payment_status === 'paid'" :href="`/orders/${order.id}/track`"
                      class="px-4 py-2 text-sm font-medium text-[#1565C0] bg-blue-50 hover:bg-blue-100 rounded-xl transition-colors">
                    Lacak Pesanan
                </Link>
                <button v-if="order.payment_status === 'pending' && order.payment_method !== 'midtrans'"
                        @click="confirmPayment"
                        class="px-4 py-2 text-sm font-medium text-white bg-emerald-500 hover:bg-emerald-600 rounded-xl transition-colors">
                    Konfirmasi Pembayaran
                </button>
                <Link v-if="order.payment_status === 'pending' && order.snap_token"
                      :href="`/payment/page/${order.id}`"
                      class="px-4 py-2 text-sm font-medium text-white bg-blue-500 hover:bg-blue-600 rounded-xl transition-colors">
                    Bayar Sekarang
                </Link>
            </div>
        </div>

        <!-- Shipping Info -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6 mb-6">
            <h3 class="font-semibold text-gray-900 mb-3">Informasi Pengiriman</h3>
            <div class="space-y-2 text-sm">
                <p><span class="text-gray-500">Alamat:</span> <span class="text-gray-900">{{ order.alamat }}</span></p>
                <p><span class="text-gray-500">Telepon:</span> <span class="text-gray-900">{{ order.nomor_telepon }}</span></p>
            </div>
        </div>

        <!-- Order Items -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Detail Produk</h3>
            <div class="space-y-4">
                <div v-for="item in order.order_items" :key="item.id"
                     class="flex items-center gap-4 pb-4 border-b border-gray-50 last:border-0 last:pb-0">
                    <div class="w-16 h-16 rounded-xl overflow-hidden bg-gray-100 shrink-0">
                        <img v-if="item.product?.image" :src="`/${item.product.image}`" class="w-full h-full object-cover" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ item.product?.name }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ item.quantity }}x @ Rp {{ Number(item.price).toLocaleString('id-ID') }}</p>
                    </div>
                    <p class="text-sm font-bold text-gray-900 shrink-0">
                        Rp {{ (item.price * item.quantity).toLocaleString('id-ID') }}
                    </p>
                </div>
            </div>

            <!-- Total -->
            <div class="border-t border-gray-100 mt-4 pt-4 flex items-center justify-between">
                <span class="font-semibold text-gray-900">Total</span>
                <span class="text-xl font-extrabold text-[#1565C0]">Rp {{ Number(order.total_price).toLocaleString('id-ID') }}</span>
            </div>
        </div>
    </div>
</template>
