<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({ productsWithStock: { type: [Array, Object], default: () => [] } })

const form = useForm({
    product_id: '',
    quantity: '',
    type: 'out',
    notes: '',
})

function submit() {
    form.post('/admin/barang-keluar')
}

const productList = Array.isArray(props.productsWithStock) ? props.productsWithStock : Object.values(props.productsWithStock)
</script>

<template>
    <Head title="Catat Barang Keluar" />
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center gap-4 mb-8">
            <Link href="/admin/barang-keluar" class="w-10 h-10 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-600 hover:bg-gray-50 hover:text-blue-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </Link>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Catat Barang Keluar</h1>
                <p class="text-gray-500 mt-1">Pilih produk dan masukkan jumlah yang dikeluarkan.</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8">
            <form @submit.prevent="submit" class="space-y-6">
                <div v-if="form.errors.error" class="p-4 mb-4 text-sm text-red-800 rounded-xl bg-red-50 border border-red-200">
                    {{ form.errors.error }}
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Pilih Produk *</label>
                    <select v-model="form.product_id" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/40 focus:border-blue-400 focus:bg-white transition-all">
                        <option value="" disabled>-- Pilih Produk --</option>
                        <option v-for="p in productList" :key="p.id" :value="p.id">{{ p.name }} (Stok: {{ p.stock }})</option>
                    </select>
                    <p v-if="form.errors.product_id" class="mt-1 text-xs text-red-500">{{ form.errors.product_id }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah *</label>
                    <input v-model="form.quantity" type="number" min="1" required
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/40 focus:border-blue-400 focus:bg-white transition-all"
                           placeholder="Masukkan jumlah" />
                    <p v-if="form.errors.quantity" class="mt-1 text-xs text-red-500">{{ form.errors.quantity }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Keterangan</label>
                    <textarea v-model="form.notes" rows="3"
                              class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/40 focus:border-blue-400 focus:bg-white transition-all"
                              placeholder="Catatan pengeluaran barang"></textarea>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <Link href="/admin/barang-keluar" class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">Batal</Link>
                    <button type="submit" :disabled="form.processing"
                            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-md transition-all disabled:opacity-60">
                        {{ form.processing ? 'Menyimpan...' : 'Simpan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
