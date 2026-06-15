<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({ product: Object })

const form = useForm({
    quantity: '',
    keterangan: '',
})

function submit() {
    form.post(`/admin/gudang/${props.product.id}/stok-keluar`)
}
</script>

<template>
    <Head :title="`Stok Keluar — ${product.name}`" />
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center gap-4 mb-8">
            <Link href="/admin/gudang/kelola" class="w-10 h-10 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-600 hover:bg-gray-50 hover:text-blue-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </Link>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Stok Keluar</h1>
                <p class="text-gray-500 mt-1">Kurangi stok untuk <span class="font-semibold text-gray-700">{{ product.name }}</span></p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
            <div class="flex items-center gap-4 p-4 bg-blue-50 rounded-xl">
                <div class="w-14 h-14 rounded-xl bg-white overflow-hidden shrink-0 shadow-sm">
                    <img v-if="product.image" :src="`/${product.image}`" class="w-full h-full object-cover" />
                </div>
                <div>
                    <p class="font-semibold text-gray-900">{{ product.name }}</p>
                    <p class="text-sm text-gray-500">Stok saat ini: <span class="font-bold text-blue-600">{{ product.stock }}</span></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8">
            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah Stok Keluar *</label>
                    <input v-model="form.quantity" type="number" min="1" :max="product.stock" required
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/40 focus:border-blue-400 focus:bg-white transition-all"
                           placeholder="Masukkan jumlah" />
                    <p class="mt-1 text-xs text-gray-400">Maksimal: {{ product.stock }}</p>
                    <p v-if="form.errors.quantity" class="mt-1 text-xs text-red-500">{{ form.errors.quantity }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Keterangan</label>
                    <textarea v-model="form.keterangan" rows="3"
                              class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500/40 focus:border-blue-400 focus:bg-white transition-all"
                              placeholder="Catatan / alasan pengurangan stok"></textarea>
                    <p v-if="form.errors.keterangan" class="mt-1 text-xs text-red-500">{{ form.errors.keterangan }}</p>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <Link href="/admin/gudang/kelola" class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">Batal</Link>
                    <button type="submit" :disabled="form.processing"
                            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl shadow-md transition-all disabled:opacity-60">
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Stok Keluar' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
