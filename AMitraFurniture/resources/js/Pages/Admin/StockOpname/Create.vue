<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref, computed } from 'vue'

defineOptions({ layout: AdminLayout })
const props = defineProps({ products: Array, kode: String })

const items = ref([{ product_id: '', stok_fisik: '' }])

function addItem() {
    items.value.push({ product_id: '', stok_fisik: '' })
}

function removeItem(index) {
    if (items.value.length > 1) items.value.splice(index, 1)
}

const form = useForm({
    kode: props.kode,
    tanggal: new Date().toISOString().split('T')[0],
    catatan: '',
    items: [],
})

function submit() {
    form.items = items.value.filter(i => i.product_id && i.stok_fisik !== '')
    form.post('/admin/stock-opname')
}

function getProduct(id) {
    return props.products.find(p => p.id == id)
}
</script>

<template>
    <Head title="Buat Stock Opname" />
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center gap-4 mb-8">
            <Link href="/admin/stock-opname" class="w-10 h-10 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-600 hover:bg-gray-50 hover:text-[#1565C0] transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </Link>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Buat Stock Opname</h1>
                <p class="text-gray-500 mt-1">Kode: <span class="font-mono font-bold text-gray-700">{{ kode }}</span></p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8">
            <form @submit.prevent="submit" class="space-y-8">
                <!-- Header -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Kode Opname</label>
                        <input v-model="form.kode" type="text" readonly
                               class="w-full px-4 py-3 bg-gray-100 border border-gray-200 rounded-xl text-sm text-gray-500 cursor-not-allowed" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal *</label>
                        <input v-model="form.tanggal" type="date" required
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#1565C0]/40 focus:border-[#1565C0] focus:bg-white transition-all" />
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Catatan</label>
                        <textarea v-model="form.catatan" rows="2"
                                  class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#1565C0]/40 focus:border-[#1565C0] focus:bg-white transition-all"
                                  placeholder="Catatan opname (opsional)"></textarea>
                    </div>
                </div>

                <!-- Items -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-gray-900">Daftar Item Opname</h3>
                        <button type="button" @click="addItem" class="text-sm text-[#1565C0] font-semibold hover:text-[#0D47A1] flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Tambah Item
                        </button>
                    </div>

                    <div class="space-y-3">
                        <div v-for="(item, index) in items" :key="index"
                             class="flex items-end gap-3 p-4 bg-gray-50 rounded-xl">
                            <div class="flex-1">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Produk</label>
                                <select v-model="item.product_id" required
                                        class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#1565C0]/40 focus:border-[#1565C0] transition-all">
                                    <option value="" disabled>-- Pilih --</option>
                                    <option v-for="p in products" :key="p.id" :value="p.id">{{ p.name }} (Sistem: {{ p.stock }})</option>
                                </select>
                            </div>
                            <div class="w-32">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Stok Sistem</label>
                                <input type="text" readonly :value="getProduct(item.product_id)?.stock ?? '-'"
                                       class="w-full px-3 py-2.5 bg-gray-100 border border-gray-200 rounded-lg text-sm text-gray-500 cursor-not-allowed" />
                            </div>
                            <div class="w-32">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Stok Fisik *</label>
                                <input v-model="item.stok_fisik" type="number" min="0" required
                                       class="w-full px-3 py-2.5 bg-white border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#1565C0]/40 focus:border-[#1565C0] transition-all" />
                            </div>
                            <div class="w-24 text-center">
                                <label class="block text-xs font-medium text-gray-500 mb-1">Selisih</label>
                                <p class="py-2.5 text-sm font-bold"
                                   :class="(item.stok_fisik - (getProduct(item.product_id)?.stock || 0)) >= 0 ? 'text-emerald-600' : 'text-red-600'">
                                    {{ item.product_id && item.stok_fisik !== '' ? (item.stok_fisik - (getProduct(item.product_id)?.stock || 0)) : '-' }}
                                </p>
                            </div>
                            <button type="button" @click="removeItem(index)" v-if="items.length > 1"
                                    class="w-9 h-9 flex items-center justify-center rounded-lg bg-red-50 text-red-500 hover:bg-red-100 transition-colors shrink-0 mb-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <Link href="/admin/stock-opname" class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">Batal</Link>
                    <button type="submit" :disabled="form.processing"
                             class="px-6 py-2.5 bg-[#1565C0] hover:bg-[#0D47A1] text-white text-sm font-semibold rounded-xl shadow-md transition-all disabled:opacity-60">
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Opname' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
