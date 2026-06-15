<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    order: Object,
})

const form = useForm({
    status: props.order.status,
    payment_status: props.order.payment_status,
})

function submit() {
    form.put(`/admin/pesanan/${props.order.id}`)
}

const statusOptions = [
    { value: 'pending', label: 'Menunggu' },
    { value: 'processing', label: 'Diproses' },
    { value: 'shipped', label: 'Dikirim' },
    { value: 'delivered', label: 'Diterima' },
    { value: 'cancelled', label: 'Dibatalkan' },
]

const paymentOptions = [
    { value: 'pending', label: 'Belum Bayar' },
    { value: 'paid', label: 'Sudah Bayar' },
    { value: 'failed', label: 'Gagal' },
    { value: 'expired', label: 'Kadaluarsa' },
]
</script>

<template>
    <Head :title="`Edit Pesanan ${order.order_code}`" />

    <div class="max-w-4xl mx-auto">
        <div class="flex items-center gap-4 mb-8">
            <Link href="/admin/pesanan" class="w-10 h-10 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-600 hover:bg-gray-50 hover:text-[#1565C0] transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </Link>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Kelola Pesanan</h1>
                <p class="text-gray-500 font-mono mt-1">{{ order.order_code }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Order Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Customer Info -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-bold text-gray-900 mb-4">Informasi Pelanggan & Pengiriman</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500 mb-1">Nama Pelanggan</p>
                            <p class="font-medium text-gray-900">{{ order.user?.name || 'Guest' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 mb-1">Email</p>
                            <p class="font-medium text-gray-900">{{ order.user?.email || '-' }}</p>
                        </div>
                        <div class="sm:col-span-2">
                            <p class="text-gray-500 mb-1">Nomor Telepon</p>
                            <p class="font-medium text-gray-900">{{ order.nomor_telepon }}</p>
                        </div>
                        <div class="sm:col-span-2">
                            <p class="text-gray-500 mb-1">Alamat Pengiriman</p>
                            <p class="font-medium text-gray-900">{{ order.alamat }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 mb-1">Tanggal Pesanan</p>
                            <p class="font-medium text-gray-900">{{ new Date(order.created_at).toLocaleString('id-ID') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500 mb-1">Metode Pembayaran</p>
                            <p class="font-medium text-gray-900 uppercase">{{ order.payment_method }}</p>
                        </div>
                    </div>
                </div>

                <!-- Products -->
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-bold text-gray-900 mb-4">Produk Dipesan</h3>
                    <div class="space-y-4">
                        <div v-for="item in order.order_items" :key="item.id" class="flex items-center gap-4 pb-4 border-b border-gray-50 last:border-0 last:pb-0">
                            <div class="w-16 h-16 rounded-xl bg-gray-100 overflow-hidden shrink-0">
                                <img v-if="item.product?.image" :src="`/${item.product.image}`" class="w-full h-full object-cover" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-900 truncate">{{ item.product?.name }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ item.quantity }}x @ Rp {{ Number(item.price).toLocaleString('id-ID') }}</p>
                            </div>
                            <p class="font-bold text-gray-900">Rp {{ (item.quantity * item.price).toLocaleString('id-ID') }}</p>
                        </div>
                        <div class="pt-4 flex items-center justify-between">
                            <span class="font-bold text-gray-900">Total Pembayaran</span>
                            <span class="text-xl font-extrabold text-[#1565C0]">Rp {{ Number(order.total_price).toLocaleString('id-ID') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Update Form -->
            <div class="lg:col-span-1">
                <form @submit.prevent="submit" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sticky top-24">
                    <h3 class="font-bold text-gray-900 mb-4">Update Status</h3>
                    
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status Pembayaran</label>
                            <select v-model="form.payment_status" required
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#1565C0]/40 focus:border-[#1565C0] focus:bg-white transition-all">
                                <option v-for="opt in paymentOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                            </select>
                            <p v-if="form.errors.payment_status" class="mt-1 text-xs text-red-500">{{ form.errors.payment_status }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Status Pesanan</label>
                            <select v-model="form.status" required
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#1565C0]/40 focus:border-[#1565C0] focus:bg-white transition-all">
                                <option v-for="opt in statusOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                            </select>
                            <p v-if="form.errors.status" class="mt-1 text-xs text-red-500">{{ form.errors.status }}</p>
                        </div>

                        <div class="pt-2">
                            <button type="submit" :disabled="form.processing"
                                    class="w-full py-3 bg-[#1565C0] hover:bg-[#0D47A1] text-white font-semibold rounded-xl shadow-md transition-all disabled:opacity-60">
                                {{ form.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
