<script setup>
import { ref, watch } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    products: Object,
    categories: Array,
    stats: Object,
    recentMovements: Array,
    filters: Object,
})

const cari = ref(props.filters?.cari || '')
const kategori = ref(props.filters?.kategori || '')
const status = ref(props.filters?.status || '')

function updateFilters() {
    router.get('/admin/gudang/kelola', {
        cari: cari.value,
        kategori: kategori.value,
        status: status.value,
    }, { preserveState: true, preserveScroll: true, replace: true })
}

watch([cari, kategori, status], () => updateFilters())
</script>

<template>
    <Head title="Kelola Stok Gudang" />

    <div>
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Kelola Stok Barang</h1>
            <p class="text-gray-500 mt-1">Lakukan penyesuaian stok, catat barang masuk, dan barang keluar.</p>
        </div>

        <!-- KPI -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Item Produk</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.total }}</p>
            </div>
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Stok Aman</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.aman }}</p>
            </div>
            <div class="bg-amber-50 rounded-2xl p-5 border border-amber-100 shadow-sm">
                <p class="text-xs font-semibold text-amber-800 uppercase tracking-wider">Stok Rendah</p>
                <p class="text-2xl font-bold text-amber-600 mt-1">{{ stats.rendah }}</p>
            </div>
            <div class="bg-red-50 rounded-2xl p-5 border border-red-100 shadow-sm">
                <p class="text-xs font-semibold text-red-800 uppercase tracking-wider">Stok Habis</p>
                <p class="text-2xl font-bold text-red-600 mt-1">{{ stats.habis }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Product List -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Filters -->
                <div class="flex flex-col md:flex-row gap-3">
                    <div class="flex-1 relative">
                        <input v-model="cari" type="text" placeholder="Cari nama barang..."
                               class="w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#1565C0]/40 focus:border-[#1565C0] transition-all shadow-sm" />
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <select v-model="status" class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm shadow-sm min-w-[140px]">
                        <option value="">Semua Status</option>
                        <option value="aman">Stok Aman</option>
                        <option value="rendah">Stok Rendah</option>
                        <option value="habis">Stok Habis</option>
                    </select>
                </div>

                <!-- Products Table -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                            <tr>
                                <th class="px-5 py-3">Barang</th>
                                <th class="px-5 py-3 text-center">Stok</th>
                                <th class="px-5 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            <tr v-for="product in products.data" :key="product.id" class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-gray-100 overflow-hidden shrink-0">
                                            <img v-if="product.image" :src="`/${product.image}`" class="w-full h-full object-cover" />
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900 line-clamp-1">{{ product.name }}</p>
                                            <p class="text-[10px] text-gray-400 uppercase tracking-wider">{{ product.category }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <span class="px-2.5 py-1 text-xs font-bold rounded-md inline-block min-w-[40px]"
                                          :class="product.stock > 10 ? 'bg-emerald-50 text-emerald-600' : (product.stock > 0 ? 'bg-amber-50 text-amber-600' : 'bg-red-50 text-red-600')">
                                        {{ product.stock }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <Link :href="`/admin/gudang/stok-masuk/${product.id}`" title="Stok Masuk" class="w-8 h-8 flex items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        </Link>
                                        <Link :href="`/admin/gudang/stok-keluar/${product.id}`" title="Stok Keluar" class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                        </Link>
                                        <Link :href="`/admin/gudang/adjustment/${product.id}`" title="Penyesuaian (Opname)" class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!products.data.length">
                                <td colspan="3" class="px-5 py-8 text-center text-gray-500">Tidak ada data.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="products.links" />
            </div>

            <!-- Recent Movements -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sticky top-24 self-start">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-bold text-gray-900">Aktivitas Terakhir</h2>
                    <Link href="/admin/gudang/riwayat" class="text-sm font-medium text-[#1565C0] hover:text-[#0D47A1]">Lihat Semua</Link>
                </div>
                <div class="space-y-4">
                    <div v-for="m in recentMovements" :key="m.id" class="flex gap-3">
                        <div class="mt-1 w-8 h-8 shrink-0 rounded-full flex items-center justify-center text-xs"
                             :class="{
                                 'bg-emerald-100 text-emerald-600': m.type === 'in',
                                 'bg-blue-100 text-blue-600': m.type === 'out',
                                 'bg-amber-100 text-amber-600': m.type === 'adjustment'
                             }">
                            {{ m.type === 'in' ? '📥' : (m.type === 'out' ? '📤' : '⚖️') }}
                        </div>
                        <div>
                            <p class="text-sm text-gray-900">
                                <span class="font-semibold">{{ m.type === 'in' ? '+' : (m.type === 'out' ? '-' : '') }}{{ m.quantity }}</span> 
                                {{ m.product?.name }}
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5">Oleh: {{ m.user?.name || 'Sistem' }} • {{ new Date(m.created_at).toLocaleDateString('id-ID', { hour: '2-digit', minute: '2-digit' }) }}</p>
                        </div>
                    </div>
                    <div v-if="!recentMovements.length" class="text-sm text-gray-500 text-center py-4">Belum ada aktivitas tercatat.</div>
                </div>
            </div>
        </div>
    </div>
</template>
