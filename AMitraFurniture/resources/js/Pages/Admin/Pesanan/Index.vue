<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({ orders: Object })

const statusLabel = { pending: 'Menunggu', processing: 'Diproses', shipped: 'Dikirim', delivered: 'Diterima', cancelled: 'Dibatalkan' }
const paymentLabel = { pending: 'Belum Bayar', paid: 'Sudah Bayar', failed: 'Gagal', expired: 'Kadaluarsa' }
</script>

<template>
    <Head title="Kelola Pesanan" />

    <div>
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Pesanan</h1>
                <p class="text-gray-500 mt-1">Kelola dan pantau semua pesanan pelanggan.</p>
            </div>
            <Link href="/admin/pengiriman" class="px-4 py-2 bg-blue-50 text-blue-700 font-medium rounded-xl hover:bg-blue-100 transition-colors flex items-center gap-2">
                <span>🚚</span> Status Pengiriman
            </Link>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50/80">
                        <tr>
                            <th class="px-6 py-4">Order ID</th>
                            <th class="px-6 py-4">Pelanggan</th>
                            <th class="px-6 py-4">Total & Items</th>
                            <th class="px-6 py-4">Pembayaran</th>
                            <th class="px-6 py-4">Status Order</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="order in orders.data" :key="order.id" class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-mono text-gray-600">{{ order.order_code }}</span>
                                <p class="text-[10px] text-gray-400 mt-1">{{ new Date(order.created_at).toLocaleDateString('id-ID') }}</p>
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ order.user?.name || 'Guest' }}</td>
                            <td class="px-6 py-4">
                                <p class="font-bold text-[#1565C0]">Rp {{ Number(order.total_price).toLocaleString('id-ID') }}</p>
                                <p class="text-[10px] text-gray-500 mt-0.5">{{ order.order_items?.length || 0 }} macam produk</p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-md"
                                      :class="{
                                          'bg-amber-50 text-amber-600': order.payment_status === 'pending',
                                          'bg-emerald-50 text-emerald-600': order.payment_status === 'paid',
                                          'bg-red-50 text-red-600': order.payment_status === 'failed',
                                      }">
                                    {{ paymentLabel[order.payment_status] || order.payment_status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-md"
                                      :class="{
                                          'bg-amber-50 text-amber-600': order.status === 'pending',
                                          'bg-blue-50 text-blue-600': order.status === 'processing',
                                          'bg-indigo-50 text-indigo-600': order.status === 'shipped',
                                          'bg-emerald-50 text-emerald-600': order.status === 'delivered',
                                          'bg-red-50 text-red-600': order.status === 'cancelled',
                                      }">
                                    {{ statusLabel[order.status] || order.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <Link :href="`/admin/pesanan/${order.id}/edit`"
                                      class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 text-gray-600 hover:bg-blue-50 hover:text-[#1565C0] transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </Link>
                            </td>
                        </tr>
                        <tr v-if="!orders?.data?.length">
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">Belum ada pesanan terdaftar.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="p-4 border-t border-gray-50">
                <Pagination :links="orders.links" />
            </div>
        </div>
    </div>
</template>
