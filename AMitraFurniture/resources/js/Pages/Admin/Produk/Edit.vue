<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref } from 'vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    product: Object,
    categories: Array,
})

const form = useForm({
    _method: 'PUT',
    name: props.product.name,
    description: props.product.description || '',
    price: props.product.price,
    stock: props.product.stock,
    category: props.product.category,
    image: null,
})

const imagePreview = ref(props.product.image ? `/${props.product.image}` : null)

function handleImageChange(e) {
    const file = e.target.files[0]
    if (file) {
        form.image = file
        imagePreview.value = URL.createObjectURL(file)
    } else {
        form.image = null
        imagePreview.value = props.product.image ? `/${props.product.image}` : null
    }
}

function submit() {
    form.post(`/admin/produk/${props.product.id}`)
}
</script>

<template>
    <Head title="Edit Produk" />

    <div class="max-w-3xl mx-auto">
        <div class="flex items-center gap-4 mb-8">
            <Link href="/admin/produk" class="w-10 h-10 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-600 hover:bg-gray-50 hover:text-[#1565C0] transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </Link>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Produk</h1>
                <p class="text-gray-500 mt-1">Perbarui informasi produk {{ product.name }}.</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8">
            <form @submit.prevent="submit" class="space-y-6">
                <!-- Info Utama -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Produk *</label>
                        <input v-model="form.name" type="text" required
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#1565C0]/40 focus:border-[#1565C0] focus:bg-white transition-all" />
                        <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Harga (Rp) *</label>
                        <input v-model="form.price" type="number" required min="0" step="1"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#1565C0]/40 focus:border-[#1565C0] focus:bg-white transition-all" />
                        <p v-if="form.errors.price" class="mt-1 text-xs text-red-500">{{ form.errors.price }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Stok *</label>
                        <input v-model="form.stock" type="number" required min="0" step="1"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#1565C0]/40 focus:border-[#1565C0] focus:bg-white transition-all" />
                        <p v-if="form.errors.stock" class="mt-1 text-xs text-red-500">{{ form.errors.stock }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Kategori *</label>
                        <select v-model="form.category" required
                                class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#1565C0]/40 focus:border-[#1565C0] focus:bg-white transition-all">
                            <option value="" disabled>Pilih Kategori</option>
                            <option v-for="cat in categories" :key="cat" :value="cat">{{ cat }}</option>
                            <option value="Sofa">Sofa</option>
                            <option value="Meja">Meja</option>
                            <option value="Kursi">Kursi</option>
                            <option value="Lemari">Lemari</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                        <p v-if="form.errors.category" class="mt-1 text-xs text-red-500">{{ form.errors.category }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi</label>
                        <textarea v-model="form.description" rows="4"
                                  class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#1565C0]/40 focus:border-[#1565C0] focus:bg-white transition-all"></textarea>
                        <p v-if="form.errors.description" class="mt-1 text-xs text-red-500">{{ form.errors.description }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Foto Produk (Biarkan kosong jika tidak diubah)</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-[#1565C0] transition-colors bg-gray-50">
                            <div class="space-y-1 text-center">
                                <div v-if="imagePreview" class="mb-4">
                                    <img :src="imagePreview" class="mx-auto h-32 w-auto rounded-lg shadow-sm object-cover" />
                                </div>
                                <div class="flex text-sm text-gray-600 justify-center mt-2">
                                    <label class="relative cursor-pointer bg-white rounded-md font-medium text-[#1565C0] hover:text-[#0D47A1] focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-[#1565C0] px-2 py-0.5">
                                        <span>Ganti foto</span>
                                        <input type="file" @change="handleImageChange" accept="image/*" class="sr-only" />
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF up to 2MB</p>
                            </div>
                        </div>
                        <p v-if="form.errors.image" class="mt-1 text-xs text-red-500">{{ form.errors.image }}</p>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end gap-3">
                    <Link href="/admin/produk" class="px-6 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">Batal</Link>
                    <button type="submit" :disabled="form.processing"
                            class="px-6 py-2.5 bg-[#1565C0] hover:bg-[#0D47A1] text-white text-sm font-semibold rounded-xl shadow-md transition-all disabled:opacity-60">
                        {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
