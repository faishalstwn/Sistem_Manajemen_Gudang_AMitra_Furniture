<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })
const props = defineProps({ movement: Object, product: Object })
</script>

<template>
    <Head :title="`Detail Barang Keluar #${movement.id}`" />
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center gap-4 mb-8">
            <Link href="/admin/barang-keluar" class="w-10 h-10 bg-white border border-gray-200 rounded-xl flex items-center justify-center text-gray-600 hover:bg-gray-50 hover:text-blue-600 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </Link>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Detail Barang Keluar</h1>
                <p class="text-gray-500 mt-1">ID: #{{ movement.id }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 md:p-8 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-sm">
                <div><p class="text-gray-500 mb-1">Produk</p><p class="font-semibold text-gray-900">{{ product?.name || movement.product?.name }}</p></div>
                <div><p class="text-gray-500 mb-1">Jumlah Keluar</p><p class="font-bold text-blue-600 text-lg">-{{ movement.quantity }}</p></div>
                <div><p class="text-gray-500 mb-1">Tanggal</p><p class="font-medium text-gray-900">{{ new Date(movement.created_at).toLocaleString('id-ID') }}</p></div>
                <div><p class="text-gray-500 mb-1">Dicatat Oleh</p><p class="font-medium text-gray-900">{{ movement.user?.name || 'Sistem' }}</p></div>
                <div class="sm:col-span-2"><p class="text-gray-500 mb-1">Keterangan</p><p class="font-medium text-gray-900">{{ movement.keterangan || '-' }}</p></div>
            </div>
        </div>
    </div>
</template>
