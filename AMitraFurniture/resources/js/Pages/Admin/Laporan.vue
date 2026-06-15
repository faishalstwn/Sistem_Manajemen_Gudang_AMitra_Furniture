<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { ref } from 'vue'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    orders: Array,
    totalRevenue: Number,
    totalOrders: Number,
    ordersByStatus: Object,
    bulan: Number,
    monthName: String,
    year: Number,
})

const selectedMonth = ref(props.bulan)
const months = [
    { value: 1, label: 'Januari' }, { value: 2, label: 'Februari' }, { value: 3, label: 'Maret' },
    { value: 4, label: 'April' }, { value: 5, label: 'Mei' }, { value: 6, label: 'Juni' },
    { value: 7, label: 'Juli' }, { value: 8, label: 'Agustus' }, { value: 9, label: 'September' },
    { value: 10, label: 'Oktober' }, { value: 11, label: 'November' }, { value: 12, label: 'Desember' },
]

function changeMonth() {
    router.get(`/admin/laporan/${Number(selectedMonth.value)}`)
}
</script>

<template>
    <Head :title="`Laporan ${monthName} ${year}`" />

    <div>
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Laporan Penjualan</h1>
                <p class="text-gray-500 mt-1">Periode: {{ monthName }} {{ year }}</p>
            </div>
            <div class="flex items-center gap-3">
                <select v-model="selectedMonth" @change="changeMonth"
                        class="px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm shadow-sm">
                    <option v-for="m in months" :key="m.value" :value="m.value">{{ m.label }}</option>
                </select>
            </div>
        </div>

        <!-- KPI -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <p class="text-sm font-medium text-gray-500">Pendapatan</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">Rp {{ Number(totalRevenue).toLocaleString('id-ID') }}</p>
            </div>
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
                <p class="text-sm font-medium text-gray-500">Total Pesanan</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ totalOrders }}</p>
            </div>
            <div class="bg-emerald-50 rounded-2xl p-6 border border-emerald-100 shadow-sm">
                <p class="text-sm font-medium text-emerald-800">Sudah Bayar</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">{{ ordersByStatus.paid }}</p>
            </div>
            <div class="bg-amber-50 rounded-2xl p-6 border border-amber-100 shadow-sm">
                <p class="text-sm font-medium text-amber-800">Menunggu Bayar</p>
                <p class="text-2xl font-bold text-amber-600 mt-1">{{ ordersByStatus.pending }}</p>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-900">Daftar Pesanan — {{ monthName }} {{ year }}</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-500 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-4">Order Code</th>
                            <th class="px-6 py-4">Pelanggan</th>
                            <th class="px-6 py-4">Total</th>
                            <th class="px-6 py-4">Pembayaran</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <tr v-for="order in orders" :key="order.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-mono text-gray-600">{{ order.order_code }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ order.user?.name || 'Guest' }}</td>
                            <td class="px-6 py-4 font-semibold text-gray-900">Rp {{ Number(order.total_price).toLocaleString('id-ID') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-md"
                                      :class="{
                                          'bg-amber-50 text-amber-600': order.payment_status === 'pending',
                                          'bg-emerald-50 text-emerald-600': order.payment_status === 'paid',
                                          'bg-red-50 text-red-600': order.payment_status === 'failed' || order.payment_status === 'expired',
                                      }">
                                    {{ order.payment_status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-md"
                                      :class="{
                                          'bg-amber-50 text-amber-600': order.status === 'pending',
                                          'bg-blue-50 text-blue-600': order.status === 'processing',
                                          'bg-indigo-50 text-indigo-600': order.status === 'shipped',
                                          'bg-emerald-50 text-emerald-600': order.status === 'delivered',
                                          'bg-red-50 text-red-600': order.status === 'cancelled',
                                      }">
                                    {{ order.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-500">{{ new Date(order.created_at).toLocaleDateString('id-ID') }}</td>
                        </tr>
                        <tr v-if="!orders?.length">
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">Tidak ada pesanan di bulan ini.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
