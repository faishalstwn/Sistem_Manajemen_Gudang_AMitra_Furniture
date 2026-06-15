<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    totalProduk: Number,
    totalStok: Number,
    nilaiInventori: Number,
    totalMasuk30: Number,
    totalKeluar30: Number,
    months: Array,
    dataMasuk: Array,
    dataKeluar: Array,
    dataSelisih: Array,
    topKeluar: Array,
    slowMovers: Array,
    stokPerKategori: Array,
    movementSummary: Object,
    avgMasukBulanan: Number,
    avgKeluarBulanan: Number,
    lastOpname: Object,
    periode: Number,
})
</script>

<template>
    <Head title="Laporan Gudang" />
    <div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Laporan Gudang</h1>
                <p class="text-gray-500 mt-1">Analitik dan ringkasan pergerakan inventori {{ periode }} bulan terakhir.</p>
            </div>
            <div class="flex gap-3">
                <a href="/admin/export/pdf/stok" target="_blank" class="px-4 py-2.5 bg-red-50 text-red-700 font-medium rounded-xl hover:bg-red-100 transition-colors text-sm">📄 Export PDF Stok</a>
                <a href="/admin/export/excel/stok" target="_blank" class="px-4 py-2.5 bg-emerald-50 text-emerald-700 font-medium rounded-xl hover:bg-emerald-100 transition-colors text-sm">📊 Export Excel</a>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <p class="text-xs font-semibold text-gray-500 uppercase">Total Produk</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ totalProduk }}</p>
            </div>
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <p class="text-xs font-semibold text-gray-500 uppercase">Total Unit Stok</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ Number(totalStok).toLocaleString('id-ID') }}</p>
            </div>
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <p class="text-xs font-semibold text-gray-500 uppercase">Nilai Inventori</p>
                <p class="text-xl font-bold text-emerald-600 mt-1">Rp {{ Number(nilaiInventori).toLocaleString('id-ID') }}</p>
            </div>
            <div class="bg-emerald-50 rounded-2xl p-5 border border-emerald-100 shadow-sm">
                <p class="text-xs font-semibold text-emerald-800 uppercase">Masuk (30 hari)</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">+{{ totalMasuk30 }}</p>
            </div>
            <div class="bg-blue-50 rounded-2xl p-5 border border-blue-100 shadow-sm">
                <p class="text-xs font-semibold text-blue-800 uppercase">Keluar (30 hari)</p>
                <p class="text-2xl font-bold text-blue-600 mt-1">-{{ totalKeluar30 }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Movement Summary -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-bold text-gray-900 mb-4">Ringkasan Pergerakan</h3>
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="text-center p-4 bg-emerald-50 rounded-xl">
                        <p class="text-xs font-semibold text-emerald-800">Masuk</p>
                        <p class="text-xl font-bold text-emerald-600 mt-1">{{ movementSummary.masuk }}</p>
                    </div>
                    <div class="text-center p-4 bg-blue-50 rounded-xl">
                        <p class="text-xs font-semibold text-blue-800">Keluar</p>
                        <p class="text-xl font-bold text-blue-600 mt-1">{{ movementSummary.keluar }}</p>
                    </div>
                    <div class="text-center p-4 bg-amber-50 rounded-xl">
                        <p class="text-xs font-semibold text-amber-800">Koreksi</p>
                        <p class="text-xl font-bold text-amber-600 mt-1">{{ movementSummary.koreksi }}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-gray-500">Rata-rata Masuk/Bulan</p>
                        <p class="font-bold text-gray-900">{{ avgMasukBulanan }} unit</p>
                    </div>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <p class="text-gray-500">Rata-rata Keluar/Bulan</p>
                        <p class="font-bold text-gray-900">{{ avgKeluarBulanan }} unit</p>
                    </div>
                </div>
            </div>

            <!-- Stok per Kategori -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-bold text-gray-900 mb-4">Distribusi Stok per Kategori</h3>
                <div class="space-y-3">
                    <div v-for="cat in stokPerKategori" :key="cat.category" class="flex items-center gap-3">
                        <div class="flex-1">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium text-gray-700">{{ cat.category || 'Tanpa Kategori' }}</span>
                                <span class="text-gray-500">{{ cat.jumlah_produk }} produk • {{ cat.total_stok }} unit</span>
                            </div>
                            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-[#1565C0] rounded-full transition-all"
                                     :style="{ width: `${totalStok > 0 ? (cat.total_stok / totalStok * 100) : 0}%` }"></div>
                            </div>
                        </div>
                    </div>
                    <div v-if="!stokPerKategori?.length" class="text-center text-gray-500 text-sm py-4">Tidak ada data.</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Top Keluar -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-bold text-gray-900 mb-4">🔥 Top 10 Produk Paling Banyak Keluar</h3>
                <div class="space-y-3">
                    <div v-for="(item, i) in topKeluar" :key="item.product_id" class="flex items-center gap-3 p-2 bg-gray-50 rounded-lg">
                        <span class="w-7 h-7 flex items-center justify-center bg-[#1565C0] text-white text-xs font-bold rounded-lg shrink-0">{{ i + 1 }}</span>
                        <span class="font-medium text-gray-900 flex-1 truncate">{{ item.product?.name || `Produk #${item.product_id}` }}</span>
                        <span class="font-bold text-blue-600">{{ item.total_keluar }}</span>
                    </div>
                    <div v-if="!topKeluar?.length" class="text-center text-gray-500 text-sm py-4">Tidak ada data.</div>
                </div>
            </div>

            <!-- Slow Movers -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-bold text-gray-900 mb-4">🐌 Slow Movers (Stagnant Stock)</h3>
                <p class="text-xs text-gray-500 mb-4">Produk yang tidak keluar dalam 3 bulan terakhir namun masih memiliki stok.</p>
                <div class="space-y-3">
                    <div v-for="product in slowMovers" :key="product.id" class="flex items-center gap-3 p-2 bg-gray-50 rounded-lg">
                        <div class="w-8 h-8 rounded-lg bg-gray-200 overflow-hidden shrink-0">
                            <img v-if="product.image" :src="`/${product.image}`" class="w-full h-full object-cover" />
                        </div>
                        <span class="font-medium text-gray-900 flex-1 truncate">{{ product.name }}</span>
                        <span class="font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded text-sm">{{ product.stock }} unit</span>
                    </div>
                    <div v-if="!slowMovers?.length" class="text-center text-gray-500 text-sm py-4">Tidak ada slow movers.</div>
                </div>
            </div>
        </div>

        <!-- Last Opname -->
        <div v-if="lastOpname" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-bold text-gray-900 mb-4">📋 Stock Opname Terakhir</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                <div><p class="text-gray-500 mb-1">Kode</p><p class="font-mono font-bold text-gray-900">{{ lastOpname.kode }}</p></div>
                <div><p class="text-gray-500 mb-1">Tanggal</p><p class="font-medium text-gray-900">{{ new Date(lastOpname.tanggal).toLocaleDateString('id-ID') }}</p></div>
                <div><p class="text-gray-500 mb-1">Petugas</p><p class="font-medium text-gray-900">{{ lastOpname.user?.name || '-' }}</p></div>
            </div>
        </div>

        <!-- Monthly Trend Table -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mt-8">
            <div class="p-6 border-b border-gray-100">
                <h3 class="font-bold text-gray-900">Trend Pergerakan Stok per Bulan</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-3">Bulan</th>
                            <th class="px-6 py-3 text-center">Masuk</th>
                            <th class="px-6 py-3 text-center">Keluar</th>
                            <th class="px-6 py-3 text-center">Selisih</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="(month, i) in months" :key="month" class="hover:bg-gray-50">
                            <td class="px-6 py-3 font-medium text-gray-900">{{ month }}</td>
                            <td class="px-6 py-3 text-center text-emerald-600 font-semibold">+{{ dataMasuk[i] }}</td>
                            <td class="px-6 py-3 text-center text-blue-600 font-semibold">-{{ dataKeluar[i] }}</td>
                            <td class="px-6 py-3 text-center font-bold" :class="dataSelisih[i] >= 0 ? 'text-emerald-600' : 'text-red-600'">
                                {{ dataSelisih[i] >= 0 ? '+' : '' }}{{ dataSelisih[i] }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
