<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    barangKeluar: Object,
    products: Array,
    totalJumlah: Number,
    totalHariIni: Number,
})
</script>

<template>
    <Head title="Barang Keluar" />
    <div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Barang Keluar</h1>
                <p class="text-gray-500 mt-1">Catatan semua pengeluaran barang.</p>
            </div>
            <Link href="/admin/barang-keluar/create" class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-md gap-2 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Catat Barang Keluar
            </Link>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
            <div class="bg-blue-50 rounded-2xl p-5 border border-blue-100 shadow-sm">
                <p class="text-xs font-semibold text-blue-800 uppercase">Total Barang Keluar</p>
                <p class="text-2xl font-bold text-blue-600 mt-1">{{ totalJumlah }} unit</p>
            </div>
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <p class="text-xs font-semibold text-gray-500 uppercase">Hari Ini</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ totalHariIni }} unit</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Produk</th>
                            <th class="px-6 py-4 text-center">Jumlah</th>
                            <th class="px-6 py-4">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="item in barangKeluar.data" :key="item.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 text-gray-500 whitespace-nowrap">{{ new Date(item.created_at).toLocaleDateString('id-ID') }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ item.product?.name || item.produk?.nama }}</td>
                            <td class="px-6 py-4 text-center font-bold text-blue-600">-{{ item.quantity || item.jumlah }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ item.keterangan || '-' }}</td>
                        </tr>
                        <tr v-if="!barangKeluar.data?.length">
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">Belum ada data barang keluar.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-50">
                <Pagination :links="barangKeluar.links" />
            </div>
        </div>
    </div>
</template>
