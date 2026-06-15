<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    locations: Array,
    grid: Object,
    maxBaris: Number,
    maxKolom: Number,
    zonaList: Array,
    stats: Object,
})

function getCellColor(cell) {
    if (!cell) return 'bg-gray-100 border-gray-200'
    const persen = cell.kapasitas > 0 ? (cell.total_terisi / cell.kapasitas * 100) : 0
    if (persen >= 90) return 'bg-red-100 border-red-300 text-red-800'
    if (persen >= 50) return 'bg-amber-100 border-amber-300 text-amber-800'
    if (persen > 0) return 'bg-emerald-100 border-emerald-300 text-emerald-800'
    return 'bg-gray-50 border-gray-200 text-gray-500'
}
</script>

<template>
    <Head title="Peta Gudang" />
    <div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Peta Lokasi Gudang</h1>
                <p class="text-gray-500 mt-1">Visualisasi layout dan kapasitas rak gudang.</p>
            </div>
            <div class="flex gap-3">
                <Link href="/admin/lokasi-gudang" class="px-4 py-2.5 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors text-sm">📋 Daftar Lokasi</Link>
                <Link href="/admin/lokasi-gudang/create" class="px-4 py-2.5 bg-[#1565C0] text-white font-medium rounded-xl hover:bg-[#0D47A1] transition-colors text-sm">+ Tambah Lokasi</Link>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <p class="text-xs font-semibold text-gray-500 uppercase">Total Lokasi</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.total_lokasi }}</p>
            </div>
            <div class="bg-emerald-50 rounded-2xl p-5 border border-emerald-100 shadow-sm">
                <p class="text-xs font-semibold text-emerald-800 uppercase">Terisi</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ stats.total_terisi }}</p>
            </div>
            <div class="bg-gray-50 rounded-2xl p-5 border border-gray-200 shadow-sm">
                <p class="text-xs font-semibold text-gray-500 uppercase">Kosong</p>
                <p class="text-2xl font-bold text-gray-600 mt-1">{{ stats.total_kosong }}</p>
            </div>
            <div class="bg-red-50 rounded-2xl p-5 border border-red-100 shadow-sm">
                <p class="text-xs font-semibold text-red-800 uppercase">Penuh</p>
                <p class="text-2xl font-bold text-red-600 mt-1">{{ stats.total_penuh }}</p>
            </div>
        </div>

        <!-- Legend -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6 flex flex-wrap items-center gap-4 text-xs">
            <span class="font-semibold text-gray-600">Legenda:</span>
            <span class="flex items-center gap-1.5"><span class="w-4 h-4 rounded bg-gray-50 border border-gray-200"></span> Kosong</span>
            <span class="flex items-center gap-1.5"><span class="w-4 h-4 rounded bg-emerald-100 border border-emerald-300"></span> Terisi (< 50%)</span>
            <span class="flex items-center gap-1.5"><span class="w-4 h-4 rounded bg-amber-100 border border-amber-300"></span> Cukup Penuh (≥ 50%)</span>
            <span class="flex items-center gap-1.5"><span class="w-4 h-4 rounded bg-red-100 border border-red-300"></span> Hampir Penuh (≥ 90%)</span>
        </div>

        <!-- Grid Map -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 overflow-x-auto">
            <div class="inline-grid gap-2" :style="{ gridTemplateColumns: `repeat(${maxKolom || 6}, minmax(80px, 1fr))` }">
                <template v-for="baris in maxBaris" :key="baris">
                    <template v-for="kolom in maxKolom" :key="`${baris}-${kolom}`">
                        <div v-if="grid?.[baris]?.[kolom]"
                             class="border-2 rounded-xl p-3 text-center cursor-pointer hover:shadow-md transition-all min-h-[80px] flex flex-col justify-center"
                             :class="getCellColor(grid[baris][kolom])">
                            <Link :href="`/admin/lokasi-gudang/${grid[baris][kolom].id}`" class="block">
                                <p class="font-bold text-xs">{{ grid[baris][kolom].kode }}</p>
                                <p class="text-[10px] mt-1 opacity-75">{{ grid[baris][kolom].zona }}</p>
                            </Link>
                        </div>
                        <div v-else class="border border-dashed border-gray-200 rounded-xl min-h-[80px] flex items-center justify-center">
                            <span class="text-gray-300 text-xs">—</span>
                        </div>
                    </template>
                </template>
            </div>
        </div>
    </div>
</template>
