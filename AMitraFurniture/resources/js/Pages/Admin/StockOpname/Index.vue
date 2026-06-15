<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'

defineOptions({ layout: AdminLayout })
const props = defineProps({ opnames: Object, stats: Object })
</script>

<template>
    <Head title="Stock Opname" />
    <div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Stock Opname</h1>
                <p class="text-gray-500 mt-1">Audit stok fisik gudang secara berkala.</p>
            </div>
            <Link href="/admin/stock-opname/create" class="inline-flex items-center justify-center px-4 py-2.5 bg-[#1565C0] hover:bg-[#0D47A1] text-white font-semibold rounded-xl shadow-md gap-2 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Buat Opname Baru
            </Link>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <p class="text-xs font-semibold text-gray-500 uppercase">Total Opname</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.total }}</p>
            </div>
            <div class="bg-amber-50 rounded-2xl p-5 border border-amber-100 shadow-sm">
                <p class="text-xs font-semibold text-amber-800 uppercase">Draft</p>
                <p class="text-2xl font-bold text-amber-600 mt-1">{{ stats.draft }}</p>
            </div>
            <div class="bg-emerald-50 rounded-2xl p-5 border border-emerald-100 shadow-sm">
                <p class="text-xs font-semibold text-emerald-800 uppercase">Selesai</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ stats.selesai }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-4">Kode</th>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Petugas</th>
                            <th class="px-6 py-4 text-center">Jumlah Item</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="opname in opnames.data" :key="opname.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-mono font-bold text-gray-900">{{ opname.kode }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ new Date(opname.tanggal).toLocaleDateString('id-ID') }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ opname.user?.name || '-' }}</td>
                            <td class="px-6 py-4 text-center">{{ opname.items_count || opname.items?.length || 0 }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-md"
                                      :class="opname.status === 'draft' ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-emerald-600'">
                                    {{ opname.status === 'draft' ? 'Draft' : 'Selesai' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <Link :href="`/admin/stock-opname/${opname.id}`"
                                      class="text-[#1565C0] font-medium hover:text-[#0D47A1] text-sm">Detail</Link>
                            </td>
                        </tr>
                        <tr v-if="!opnames.data?.length">
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">Belum ada data stock opname.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-50">
                <Pagination :links="opnames.links" />
            </div>
        </div>
    </div>
</template>
