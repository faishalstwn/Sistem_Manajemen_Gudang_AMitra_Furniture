<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    lowStockProducts: Array,
    outOfStockProducts: Array,
    allProducts: Array,
    pendingShipmentOrders: Array,
    chartLabels: Array,
    chartMasuk: Array,
    chartKeluar: Array,
    chartTopStokNames: Array,
    chartTopStokData: Array,
})
</script>

<template>
    <Head title="Monitor Gudang" />

    <div>
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Monitor Gudang</h1>
            <p class="text-gray-500 mt-1">Status dan pergerakan stok secara realtime.</p>
        </div>

        <!-- Alerts -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-red-50 border border-red-100 rounded-2xl p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-red-100 text-red-600 rounded-xl flex items-center justify-center text-lg">⚠️</div>
                    <div>
                        <h3 class="font-bold text-red-900">Stok Habis</h3>
                        <p class="text-sm text-red-700">{{ outOfStockProducts.length }} produk memerlukan restock segera</p>
                    </div>
                </div>
                <div class="space-y-2 max-h-40 overflow-y-auto pr-2">
                    <div v-for="p in outOfStockProducts" :key="p.id" class="flex justify-between items-center text-sm bg-white/60 p-2 rounded-lg">
                        <span class="font-medium text-red-900 truncate pr-4">{{ p.name }}</span>
                        <span class="text-red-700 font-bold bg-red-100 px-2 py-0.5 rounded">0</span>
                    </div>
                    <div v-if="!outOfStockProducts.length" class="text-sm text-red-600">Tidak ada produk habis.</div>
                </div>
            </div>

            <div class="bg-amber-50 border border-amber-100 rounded-2xl p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center text-lg">🔔</div>
                    <div>
                        <h3 class="font-bold text-amber-900">Stok Menipis</h3>
                        <p class="text-sm text-amber-700">{{ lowStockProducts.length }} produk di bawah batas aman (< 10)</p>
                    </div>
                </div>
                <div class="space-y-2 max-h-40 overflow-y-auto pr-2">
                    <div v-for="p in lowStockProducts" :key="p.id" class="flex justify-between items-center text-sm bg-white/60 p-2 rounded-lg">
                        <span class="font-medium text-amber-900 truncate pr-4">{{ p.name }}</span>
                        <span class="text-amber-700 font-bold bg-amber-100 px-2 py-0.5 rounded">{{ p.stock }}</span>
                    </div>
                    <div v-if="!lowStockProducts.length" class="text-sm text-amber-600">Tidak ada produk stok menipis.</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Pending Shipments -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-bold text-gray-900">Antrean Pengiriman (Siap Pack)</h2>
                    <span class="px-2.5 py-1 bg-blue-50 text-blue-700 text-xs font-bold rounded-md">{{ pendingShipmentOrders.length }} Pesanan</span>
                </div>
                <div class="space-y-4 max-h-96 overflow-y-auto pr-2">
                    <div v-for="order in pendingShipmentOrders" :key="order.id" class="border border-gray-100 rounded-xl p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <p class="font-mono text-xs text-gray-500">{{ order.order_code }}</p>
                                <p class="font-semibold text-gray-900 mt-0.5">{{ order.user?.name || 'Guest' }}</p>
                            </div>
                            <Link :href="`/admin/pesanan/${order.id}/edit`" class="text-xs bg-gray-900 text-white px-3 py-1.5 rounded-lg hover:bg-gray-800 transition-colors">
                                Proses
                            </Link>
                        </div>
                        <div class="flex gap-2 mt-3 overflow-x-auto pb-1">
                            <div v-for="item in order.order_items" :key="item.id" class="flex items-center gap-2 bg-white border border-gray-200 px-2 py-1.5 rounded-lg shrink-0">
                                <img v-if="item.product?.image" :src="`/${item.product.image}`" class="w-6 h-6 rounded object-cover" />
                                <span class="text-xs font-medium">{{ item.quantity }}x</span>
                                <span class="text-xs text-gray-600 truncate max-w-[100px]" :title="item.product?.name">{{ item.product?.name }}</span>
                            </div>
                        </div>
                    </div>
                    <div v-if="!pendingShipmentOrders.length" class="text-center text-gray-500 py-8 text-sm">
                        Tidak ada antrean pengiriman saat ini.
                    </div>
                </div>
            </div>

            <!-- Stats (Placeholder for charts) -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col justify-center items-center text-center">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4 text-3xl">📊</div>
                <h3 class="font-bold text-gray-900">Statistik Pergerakan Stok</h3>
                <p class="text-gray-500 text-sm mt-2 max-w-sm mb-6">Visualisasi pergerakan barang masuk dan keluar per bulan dapat diintegrasikan menggunakan library seperti Chart.js.</p>
                <div class="grid grid-cols-2 gap-4 w-full max-w-sm">
                    <div class="bg-emerald-50 rounded-xl p-4">
                        <p class="text-xs font-semibold text-emerald-800 mb-1">Total Masuk</p>
                        <p class="text-xl font-bold text-emerald-600">{{ chartMasuk.reduce((a, b) => a + Number(b), 0) }}</p>
                    </div>
                    <div class="bg-blue-50 rounded-xl p-4">
                        <p class="text-xs font-semibold text-blue-800 mb-1">Total Keluar</p>
                        <p class="text-xl font-bold text-blue-600">{{ chartKeluar.reduce((a, b) => a + Number(b), 0) }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
