<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    movements: Object,
    products: Array,
    summary: Object,
})
</script>

<template>
    <Head title="Riwayat Pergerakan Stok" />
    <div>
        <div class="flex items-center gap-4 mb-8">
            <Link href="/admin/gudang/kelola" class="w-10 h-10 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-600 hover:bg-gray-50 hover:text-[#1565C0] transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </Link>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Riwayat Pergerakan Stok</h1>
                <p class="text-gray-500 mt-1">Semua catatan pergerakan barang di gudang.</p>
            </div>
        </div>

        <!-- Summary -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            <div class="bg-emerald-50 rounded-2xl p-5 border border-emerald-100 shadow-sm">
                <p class="text-xs font-semibold text-emerald-800 uppercase tracking-wider">Total Masuk</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ summary.total_masuk }}</p>
            </div>
            <div class="bg-blue-50 rounded-2xl p-5 border border-blue-100 shadow-sm">
                <p class="text-xs font-semibold text-blue-800 uppercase tracking-wider">Total Keluar</p>
                <p class="text-2xl font-bold text-blue-600 mt-1">{{ summary.total_keluar }}</p>
            </div>
            <div class="bg-amber-50 rounded-2xl p-5 border border-amber-100 shadow-sm">
                <p class="text-xs font-semibold text-amber-800 uppercase tracking-wider">Total Penyesuaian</p>
                <p class="text-2xl font-bold text-amber-600 mt-1">{{ summary.total_adjustment }}</p>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Produk</th>
                            <th class="px-6 py-4">Tipe</th>
                            <th class="px-6 py-4 text-center">Jumlah</th>
                            <th class="px-6 py-4">Keterangan</th>
                            <th class="px-6 py-4">Oleh</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="m in movements.data" :key="m.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-gray-500 whitespace-nowrap">{{ new Date(m.created_at).toLocaleString('id-ID') }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ m.product?.name }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-md inline-block"
                                      :class="{
                                          'bg-emerald-50 text-emerald-600': m.type === 'in',
                                          'bg-blue-50 text-blue-600': m.type === 'out',
                                          'bg-amber-50 text-amber-600': m.type === 'adjustment',
                                      }">
                                    {{ m.type === 'in' ? 'Masuk' : (m.type === 'out' ? 'Keluar' : 'Koreksi') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center font-bold"
                                :class="m.type === 'in' ? 'text-emerald-600' : (m.type === 'out' ? 'text-blue-600' : 'text-amber-600')">
                                {{ m.type === 'in' ? '+' : (m.type === 'out' ? '-' : '') }}{{ m.quantity }}
                            </td>
                            <td class="px-6 py-4 text-gray-500 max-w-xs truncate">{{ m.keterangan || '-' }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ m.user?.name || 'Sistem' }}</td>
                        </tr>
                        <tr v-if="!movements.data?.length">
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">Belum ada riwayat pergerakan stok.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-50">
                <Pagination :links="movements.links" />
            </div>
        </div>
    </div>
</template>
