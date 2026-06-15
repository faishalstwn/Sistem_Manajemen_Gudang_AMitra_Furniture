<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'

defineOptions({ layout: AdminLayout })
const props = defineProps({ locations: Object, zonaList: Array })

function deleteLocation(id) {
    if (confirm('Hapus lokasi ini? Tindakan ini tidak dapat dibatalkan.')) {
        router.delete(`/admin/lokasi-gudang/${id}`)
    }
}
</script>

<template>
    <Head title="Daftar Lokasi Gudang" />
    <div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Daftar Lokasi Gudang</h1>
                <p class="text-gray-500 mt-1">Kelola rak, sel, dan area penyimpanan.</p>
            </div>
            <div class="flex gap-3">
                <Link href="/admin/lokasi-gudang/peta" class="px-4 py-2.5 bg-white border border-gray-200 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors text-sm">🗺️ Lihat Peta</Link>
                <Link href="/admin/lokasi-gudang/create" class="px-4 py-2.5 bg-[#1565C0] hover:bg-[#0D47A1] text-white font-medium rounded-xl transition-colors text-sm">+ Tambah Lokasi</Link>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-4">Kode</th>
                            <th class="px-6 py-4">Zona</th>
                            <th class="px-6 py-4">Posisi</th>
                            <th class="px-6 py-4 text-center">Kapasitas</th>
                            <th class="px-6 py-4 text-center">Produk</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="loc in locations.data" :key="loc.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-bold text-gray-900">{{ loc.kode }}</td>
                            <td class="px-6 py-4"><span class="px-2.5 py-1 bg-blue-50 text-blue-600 text-xs font-medium rounded-md">{{ loc.zona }}</span></td>
                            <td class="px-6 py-4 text-gray-500">Baris {{ loc.baris }}, Kolom {{ loc.kolom }}</td>
                            <td class="px-6 py-4 text-center font-medium">{{ loc.kapasitas }}</td>
                            <td class="px-6 py-4 text-center">{{ loc.products?.length || 0 }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <Link :href="`/admin/lokasi-gudang/${loc.id}`" class="w-8 h-8 rounded-lg bg-gray-100 text-gray-600 flex items-center justify-center hover:bg-blue-50 hover:text-blue-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </Link>
                                    <Link :href="`/admin/lokasi-gudang/${loc.id}/edit`" class="w-8 h-8 rounded-lg bg-gray-100 text-gray-600 flex items-center justify-center hover:bg-blue-50 hover:text-[#1565C0] transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </Link>
                                    <button @click="deleteLocation(loc.id)" class="w-8 h-8 rounded-lg bg-gray-100 text-gray-600 flex items-center justify-center hover:bg-red-50 hover:text-red-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!locations.data?.length">
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">Belum ada lokasi gudang.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-50">
                <Pagination :links="locations.links" />
            </div>
        </div>
    </div>
</template>
