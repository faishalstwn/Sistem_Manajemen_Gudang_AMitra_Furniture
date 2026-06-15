<script setup>
import { ref, watch } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import Pagination from '@/Components/Pagination.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    products: Object,
    stats: Object,
    categories: Array,
    lowStockProducts: Array,
    filters: Object,
})

const cari = ref(props.filters?.cari || '')
const kategori = ref(props.filters?.kategori || '')
const status = ref(props.filters?.status || '')

function updateFilters() {
    router.get('/admin/produk', {
        cari: cari.value,
        kategori: kategori.value,
        status: status.value,
    }, { preserveState: true, preserveScroll: true, replace: true })
}

watch([cari, kategori, status], () => {
    updateFilters()
})

function deleteProduct(id) {
    if (confirm('Anda yakin ingin menghapus produk ini? Tindakan ini tidak dapat dibatalkan.')) {
        router.delete(`/admin/produk/${id}`, { preserveScroll: true })
    }
}
</script>

<template>
    <Head title="Manajemen Produk" />

    <div>
        <!-- Header & Action -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Manajemen Produk</h1>
                <p class="text-gray-500 mt-1">Kelola katalog produk, harga, dan informasi dasar.</p>
            </div>
            <Link href="/admin/produk/create" class="inline-flex items-center justify-center px-4 py-2.5 bg-[#1565C0] hover:bg-[#0D47A1] text-white font-semibold rounded-xl shadow-md transition-all gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Produk
            </Link>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Produk</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ stats.total }}</p>
            </div>
            <div class="bg-emerald-50 rounded-2xl p-5 border border-emerald-100 shadow-sm">
                <p class="text-xs font-semibold text-emerald-800 uppercase tracking-wider">Stok Aman</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ stats.aman }}</p>
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

        <!-- Filters -->
        <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-sm mb-6 flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <input v-model="cari" type="text" placeholder="Cari nama produk..."
                       class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#1565C0]/40 focus:border-[#1565C0] focus:bg-white transition-all" />
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <select v-model="kategori" class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#1565C0]/40 focus:border-[#1565C0] focus:bg-white min-w-[150px]">
                <option value="">Semua Kategori</option>
                <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
            </select>
            <select v-model="status" class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#1565C0]/40 focus:border-[#1565C0] focus:bg-white min-w-[150px]">
                <option value="">Semua Status</option>
                <option value="tersedia">Stok Tersedia</option>
                <option value="rendah">Stok Rendah (<10)</option>
                <option value="habis">Stok Habis</option>
            </select>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-4">Produk</th>
                            <th class="px-6 py-4">Kategori</th>
                            <th class="px-6 py-4">Harga</th>
                            <th class="px-6 py-4 text-center">Stok</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="product in products.data" :key="product.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg overflow-hidden bg-gray-100 shrink-0">
                                        <img v-if="product.image" :src="`/${product.image}`" class="w-full h-full object-cover" />
                                    </div>
                                    <p class="font-medium text-gray-900 truncate max-w-[200px]">{{ product.name }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4"><span class="px-2.5 py-1 bg-gray-100 text-gray-600 rounded-md text-xs font-medium">{{ product.category }}</span></td>
                            <td class="px-6 py-4 font-semibold text-emerald-600">Rp {{ Number(product.price).toLocaleString('id-ID') }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 text-xs font-bold rounded-md inline-block min-w-[40px]"
                                      :class="product.stock > 10 ? 'bg-emerald-50 text-emerald-600' : (product.stock > 0 ? 'bg-amber-50 text-amber-600' : 'bg-red-50 text-red-600')">
                                    {{ product.stock }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <Link :href="`/admin/produk/${product.id}/edit`" class="w-8 h-8 rounded-lg bg-gray-100 text-gray-600 flex items-center justify-center hover:bg-blue-50 hover:text-[#1565C0] transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </Link>
                                    <button @click="deleteProduct(product.id)" class="w-8 h-8 rounded-lg bg-gray-100 text-gray-600 flex items-center justify-center hover:bg-red-50 hover:text-red-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="!products?.data?.length">
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">Tidak ada produk ditemukan.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-50">
                <Pagination :links="products.links" />
            </div>
        </div>
    </div>
</template>
