<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })
const props = defineProps({ zonaList: Array })

const form = useForm({
    kode: '',
    zona: '',
    baris: '',
    kolom: '',
    kapasitas: '',
    deskripsi: '',
})

function submit() {
    form.post('/admin/lokasi-gudang')
}
</script>

<template>
    <Head title="Tambah Lokasi Gudang" />
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center gap-4 mb-8">
            <Link href="/admin/lokasi-gudang" class="w-10 h-10 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-600 hover:bg-gray-50 hover:text-[#1565C0] transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </Link>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tambah Lokasi</h1>
                <p class="text-gray-500 mt-1">Buat titik penyimpanan baru di gudang.</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8">
            <form @submit.prevent="submit" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Kode Lokasi *</label>
                        <input v-model="form.kode" type="text" required placeholder="A-01-01"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#1565C0]/40 focus:border-[#1565C0] focus:bg-white transition-all" />
                        <p v-if="form.errors.kode" class="mt-1 text-xs text-red-500">{{ form.errors.kode }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Zona *</label>
                        <input v-model="form.zona" type="text" required placeholder="Zona A"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#1565C0]/40 focus:border-[#1565C0] focus:bg-white transition-all" list="zona-list" />
                        <datalist id="zona-list">
                            <option v-for="z in zonaList" :key="z" :value="z" />
                        </datalist>
                        <p v-if="form.errors.zona" class="mt-1 text-xs text-red-500">{{ form.errors.zona }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Baris *</label>
                        <input v-model="form.baris" type="number" min="1" required
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#1565C0]/40 focus:border-[#1565C0] focus:bg-white transition-all" />
                        <p v-if="form.errors.baris" class="mt-1 text-xs text-red-500">{{ form.errors.baris }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Kolom *</label>
                        <input v-model="form.kolom" type="number" min="1" required
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#1565C0]/40 focus:border-[#1565C0] focus:bg-white transition-all" />
                        <p v-if="form.errors.kolom" class="mt-1 text-xs text-red-500">{{ form.errors.kolom }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Kapasitas (unit) *</label>
                        <input v-model="form.kapasitas" type="number" min="1" required
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#1565C0]/40 focus:border-[#1565C0] focus:bg-white transition-all" />
                        <p v-if="form.errors.kapasitas" class="mt-1 text-xs text-red-500">{{ form.errors.kapasitas }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi</label>
                        <textarea v-model="form.deskripsi" rows="3"
                                  class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#1565C0]/40 focus:border-[#1565C0] focus:bg-white transition-all"
                                  placeholder="Deskripsi lokasi (opsional)"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                    <Link href="/admin/lokasi-gudang" class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors">Batal</Link>
                    <button type="submit" :disabled="form.processing"
                            class="px-6 py-2.5 bg-[#1565C0] hover:bg-[#0D47A1] text-white text-sm font-semibold rounded-xl shadow-md transition-all disabled:opacity-60">
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Lokasi' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
