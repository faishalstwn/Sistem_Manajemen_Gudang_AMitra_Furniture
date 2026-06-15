<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })
const props = defineProps({ stockOpname: Object })

function approve() {
    if (confirm('Setujui opname ini? Stok sistem akan disesuaikan berdasarkan hasil opname.')) {
        router.post(`/admin/stock-opname/${props.stockOpname.id}/approve`)
    }
}
</script>

<template>
    <Head :title="`Stock Opname ${stockOpname.kode}`" />
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center gap-4 mb-8">
            <Link href="/admin/stock-opname" class="w-10 h-10 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-600 hover:bg-gray-50 hover:text-[#1565C0] transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </Link>
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-gray-900">{{ stockOpname.kode }}</h1>
                <p class="text-gray-500 mt-1">{{ new Date(stockOpname.tanggal).toLocaleDateString('id-ID', { dateStyle: 'long' }) }}</p>
            </div>
            <span class="px-3 py-1.5 text-xs font-bold rounded-lg"
                  :class="stockOpname.status === 'draft' ? 'bg-amber-50 text-amber-600 border border-amber-200' : 'bg-emerald-50 text-emerald-600 border border-emerald-200'">
                {{ stockOpname.status === 'draft' ? '📝 Draft' : '✅ Selesai' }}
            </span>
        </div>

        <!-- Info -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                <div><p class="text-gray-500 mb-1">Petugas</p><p class="font-medium text-gray-900">{{ stockOpname.user?.name || '-' }}</p></div>
                <div><p class="text-gray-500 mb-1">Tanggal</p><p class="font-medium text-gray-900">{{ new Date(stockOpname.tanggal).toLocaleDateString('id-ID') }}</p></div>
                <div><p class="text-gray-500 mb-1">Catatan</p><p class="font-medium text-gray-900">{{ stockOpname.catatan || '-' }}</p></div>
            </div>
        </div>

        <!-- Items -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
            <div class="p-6 border-b border-gray-100">
                <h3 class="font-bold text-gray-900">Hasil Opname — {{ stockOpname.items?.length || 0 }} Item</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-4">Produk</th>
                            <th class="px-6 py-4 text-center">Stok Sistem</th>
                            <th class="px-6 py-4 text-center">Stok Fisik</th>
                            <th class="px-6 py-4 text-center">Selisih</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="item in stockOpname.items" :key="item.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-medium text-gray-900">{{ item.product?.name }}</td>
                            <td class="px-6 py-4 text-center text-gray-600">{{ item.stok_sistem }}</td>
                            <td class="px-6 py-4 text-center font-semibold text-gray-900">{{ item.stok_fisik }}</td>
                            <td class="px-6 py-4 text-center font-bold"
                                :class="(item.stok_fisik - item.stok_sistem) === 0 ? 'text-gray-400' : ((item.stok_fisik - item.stok_sistem) > 0 ? 'text-emerald-600' : 'text-red-600')">
                                {{ (item.stok_fisik - item.stok_sistem) > 0 ? '+' : '' }}{{ item.stok_fisik - item.stok_sistem }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Approve Button -->
        <div v-if="stockOpname.status === 'draft'" class="flex justify-end">
            <button @click="approve"
                    class="px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl shadow-md transition-all">
                ✅ Setujui & Terapkan ke Stok
            </button>
        </div>
    </div>
</template>
