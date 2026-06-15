<script setup>
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineOptions({ layout: AdminLayout })
</script>

<template>
    <Head title="Notifikasi" />
    <div>
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Notifikasi</h1>
            <p class="text-gray-500 mt-1">Pemberitahuan terbaru untuk aktivitas gudang dan pesanan.</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl">🔔</div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Belum Ada Notifikasi</h3>
            <p class="text-gray-500 text-sm max-w-md mx-auto">Semua notifikasi terkait pesanan baru, stok rendah, dan aktivitas gudang akan tampil di sini.</p>
        </div>
    </div>
</template>
