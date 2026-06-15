<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    totalRevenue: Number,
    newOrdersToday: Number,
    totalOrders: Number,
    totalProducts: Number,
    totalCustomers: Number,
    pendingOrders: Number,
    lowStockProducts: Number,
    recentOrders: Array,
    topProducts: Array,
    monthlyStats: Array,
})
</script>

<template>
    <Head title="Dashboard Admin" />

    <div>
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-500 mt-1">Ringkasan bisnis Anda hari ini.</p>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Revenue -->
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-xl">💰</div>
                    <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded-md">Total Pendapatan</span>
                </div>
                <h3 class="text-gray-500 text-sm font-medium">Pendapatan Bersih</h3>
                <p class="text-2xl font-bold text-gray-900 mt-1">Rp {{ Number(totalRevenue).toLocaleString('id-ID') }}</p>
            </div>

            <!-- Orders -->
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-xl">📦</div>
                    <span class="text-xs font-medium text-blue-600 bg-blue-50 px-2 py-1 rounded-md">Hari Ini: +{{ newOrdersToday }}</span>
                </div>
                <h3 class="text-gray-500 text-sm font-medium">Total Pesanan</h3>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ totalOrders }}</p>
            </div>

            <!-- Products -->
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-xl">🏷️</div>
                    <span v-if="lowStockProducts > 0" class="text-xs font-medium text-red-600 bg-red-50 px-2 py-1 rounded-md">{{ lowStockProducts }} Stok Rendah</span>
                </div>
                <h3 class="text-gray-500 text-sm font-medium">Total Produk</h3>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ totalProducts }}</p>
            </div>

            <!-- Customers -->
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center text-xl">👥</div>
                </div>
                <h3 class="text-gray-500 text-sm font-medium">Total Pelanggan</h3>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ totalCustomers }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Recent Orders -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-bold text-gray-900">Pesanan Terbaru</h2>
                    <Link href="/admin/pesanan" class="text-sm font-medium text-[#1565C0] hover:text-[#0D47A1]">Lihat Semua</Link>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 rounded-l-lg">ID Pesanan</th>
                                <th class="px-4 py-3">Pelanggan</th>
                                <th class="px-4 py-3">Total</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 rounded-r-lg">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="order in recentOrders" :key="order.id" class="border-b border-gray-50 last:border-0 hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-3 font-mono text-gray-600">{{ order.order_code }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ order.user?.name || 'Guest' }}</td>
                                <td class="px-4 py-3 font-semibold text-gray-900">Rp {{ Number(order.total_price).toLocaleString('id-ID') }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-[10px] font-semibold rounded-full"
                                          :class="{
                                              'badge-pending': order.status === 'pending',
                                              'badge-processing': order.status === 'processing',
                                              'badge-shipped': order.status === 'shipped',
                                              'bg-emerald-100 text-emerald-800': order.status === 'delivered',
                                              'bg-red-100 text-red-800': order.status === 'cancelled',
                                          }">
                                        {{ order.status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-500">{{ new Date(order.created_at).toLocaleDateString('id-ID') }}</td>
                            </tr>
                            <tr v-if="!recentOrders?.length">
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">Belum ada pesanan</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Top Products -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-6">Produk Terlaris</h2>
                <div class="space-y-4">
                    <div v-for="(product, index) in topProducts" :key="product.id" class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-gray-100 overflow-hidden shrink-0 relative">
                            <img v-if="product.image" :src="`/${product.image}`" class="w-full h-full object-cover" />
                            <div class="absolute top-0 left-0 w-5 h-5 bg-[#1565C0] text-[#FFD600] text-[10px] font-bold flex items-center justify-center rounded-br-lg">{{ index + 1 }}</div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ product.name }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ product.orders_count }} Terjual</p>
                        </div>
                        <div class="text-sm font-bold text-emerald-600">
                            Rp {{ Number(product.price).toLocaleString('id-ID') }}
                        </div>
                    </div>
                    <div v-if="!topProducts?.length" class="text-center py-8 text-gray-500 text-sm">
                        Belum ada data penjualan
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
