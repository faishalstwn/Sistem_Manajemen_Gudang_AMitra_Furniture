<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    waitingShipment: Number,
    shipping: Number,
    delivered: Number,
    recentOrders: Array,
})
</script>

<template>
    <Head title="Manajemen Pengiriman" />

    <div>
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Pengiriman</h1>
            <p class="text-gray-500 mt-1">Pantau status pengiriman pesanan ke pelanggan.</p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-2xl">📦</div>
                <div>
                    <p class="text-sm font-semibold text-gray-500">Perlu Dikirim</p>
                    <p class="text-2xl font-bold text-gray-900">{{ waitingShipment }}</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-14 h-14 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-2xl">🚚</div>
                <div>
                    <p class="text-sm font-semibold text-gray-500">Sedang Dikirim</p>
                    <p class="text-2xl font-bold text-gray-900">{{ shipping }}</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center gap-4">
                <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-2xl">✅</div>
                <div>
                    <p class="text-sm font-semibold text-gray-500">Telah Diterima</p>
                    <p class="text-2xl font-bold text-gray-900">{{ delivered }}</p>
                </div>
            </div>
        </div>

        <!-- Recent Shipments -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Daftar Pengiriman Aktif</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-4">ID Pesanan</th>
                            <th class="px-6 py-4">Penerima</th>
                            <th class="px-6 py-4">Alamat</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="order in recentOrders" :key="order.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-mono text-gray-600">{{ order.order_code }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900">
                                {{ order.user?.name || 'Guest' }}
                                <p class="text-xs text-gray-500 font-normal mt-0.5">{{ order.nomor_telepon }}</p>
                            </td>
                            <td class="px-6 py-4 text-gray-600 max-w-xs truncate" :title="order.alamat">{{ order.alamat }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 text-xs font-bold rounded-full inline-block"
                                      :class="{
                                          'bg-blue-50 text-blue-600': order.status === 'processing',
                                          'bg-amber-50 text-amber-600': order.status === 'shipped',
                                          'bg-emerald-50 text-emerald-600': order.status === 'delivered',
                                      }">
                                    {{ order.status === 'processing' ? 'Perlu Dikirim' : (order.status === 'shipped' ? 'Dikirim' : 'Diterima') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <Link :href="`/admin/pesanan/${order.id}/edit`" class="text-[#1565C0] font-medium hover:text-[#0D47A1]">Update Status</Link>
                            </td>
                        </tr>
                        <tr v-if="!recentOrders?.length">
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">Tidak ada data pengiriman aktif.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
