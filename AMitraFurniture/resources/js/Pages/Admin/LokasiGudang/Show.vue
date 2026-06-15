<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref } from 'vue'

defineOptions({ layout: AdminLayout })
const props = defineProps({ location: Object, availableProducts: Array })

const assignForm = useForm({ product_id: '', jumlah: 1 })

function assignProduct() {
    assignForm.post(`/admin/lokasi-gudang/${props.location.id}/assign`, {
        onSuccess: () => assignForm.reset(),
    })
}

function removeProduct(productId) {
    if (confirm('Hapus produk dari lokasi ini?')) {
        router.delete(`/admin/lokasi-gudang/${props.location.id}/remove/${productId}`)
    }
}
</script>

<template>
    <Head :title="`Lokasi ${location.kode}`" />
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center gap-4 mb-8">
            <Link href="/admin/lokasi-gudang" class="w-10 h-10 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-600 hover:bg-gray-50 hover:text-[#1565C0] transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </Link>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Lokasi: {{ location.kode }}</h1>
                <p class="text-gray-500 mt-1">Zona {{ location.zona }} — Baris {{ location.baris }}, Kolom {{ location.kolom }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Info & Products -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-bold text-gray-900 mb-4">Informasi Lokasi</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div><p class="text-gray-500 mb-1">Kode</p><p class="font-bold text-gray-900">{{ location.kode }}</p></div>
                        <div><p class="text-gray-500 mb-1">Zona</p><p class="font-medium text-gray-900">{{ location.zona }}</p></div>
                        <div><p class="text-gray-500 mb-1">Kapasitas</p><p class="font-medium text-gray-900">{{ location.kapasitas }} unit</p></div>
                        <div><p class="text-gray-500 mb-1">Deskripsi</p><p class="font-medium text-gray-900">{{ location.deskripsi || '-' }}</p></div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-bold text-gray-900 mb-4">Produk di Lokasi Ini</h3>
                    <div class="space-y-3">
                        <div v-for="product in location.products" :key="product.id"
                             class="flex items-center gap-4 p-3 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 rounded-lg bg-gray-200 overflow-hidden shrink-0">
                                <img v-if="product.image" :src="`/${product.image}`" class="w-full h-full object-cover" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 truncate">{{ product.name }}</p>
                                <p class="text-xs text-gray-500">Qty: {{ product.pivot?.jumlah || 0 }}</p>
                            </div>
                            <button @click="removeProduct(product.id)" class="text-red-500 hover:text-red-700 text-xs font-medium">Hapus</button>
                        </div>
                        <div v-if="!location.products?.length" class="text-sm text-gray-500 py-4 text-center">Belum ada produk di lokasi ini.</div>
                    </div>
                </div>
            </div>

            <!-- Assign Product -->
            <div>
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sticky top-24">
                    <h3 class="font-bold text-gray-900 mb-4">Tempatkan Produk</h3>
                    <form @submit.prevent="assignProduct" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Pilih Produk</label>
                            <select v-model="assignForm.product_id" required
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#1565C0]/40 focus:border-[#1565C0] focus:bg-white transition-all">
                                <option value="" disabled>-- Pilih --</option>
                                <option v-for="p in availableProducts" :key="p.id" :value="p.id">{{ p.name }}</option>
                            </select>
                            <div v-if="assignForm.errors.product_id" class="text-red-500 text-xs mt-1">{{ assignForm.errors.product_id }}</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah</label>
                            <input v-model="assignForm.jumlah" type="number" min="1" required
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#1565C0]/40 focus:border-[#1565C0] focus:bg-white transition-all" />
                            <div v-if="assignForm.errors.jumlah" class="text-red-500 text-xs mt-1">{{ assignForm.errors.jumlah }}</div>
                        </div>
                        <button type="submit" :disabled="assignForm.processing"
                                class="w-full py-3 bg-[#1565C0] hover:bg-[#0D47A1] text-white font-semibold rounded-xl shadow-md transition-all disabled:opacity-60 text-sm">
                            {{ assignForm.processing ? 'Menyimpan...' : 'Tempatkan' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
