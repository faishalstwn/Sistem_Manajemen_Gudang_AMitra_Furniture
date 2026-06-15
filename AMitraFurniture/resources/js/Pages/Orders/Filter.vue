<script setup>
import { Link } from '@inertiajs/vue3'
import UserLayout from '@/Layouts/UserLayout.vue'
import Pagination from '@/Components/Pagination.vue'

defineOptions({ layout: UserLayout })

const props = defineProps({
    orders: Object,
    status: String,
    statuses: Array,
})

const paymentLabel = { pending: 'Belum Bayar', paid: 'Sudah Bayar', failed: 'Gagal', expired: 'Kadaluarsa' }
</script>

<template>
    <Head title="Filter Pesanan" />

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <Link href="/orders" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-[#1565C0] mb-6 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </Link>

        <h1 class="text-2xl font-bold text-gray-900 mb-6">Filter Pesanan</h1>

        <!-- Filter Tabs -->
        <div class="flex flex-wrap gap-2 mb-8">
            <Link href="/orders/filter"
                  class="px-4 py-2 text-sm font-medium rounded-xl transition-all"
                  :class="!status ? 'bg-[#1565C0] text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                Semua
            </Link>
            <Link v-for="s in statuses" :key="s"
                  :href="`/orders/filter/${s}`"
                  class="px-4 py-2 text-sm font-medium rounded-xl transition-all capitalize"
                  :class="status === s ? 'bg-[#1565C0] text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'">
                {{ paymentLabel[s] || s }}
            </Link>
        </div>

        <div v-if="!orders?.data?.length" class="text-center py-16">
            <p class="text-gray-500">Tidak ada pesanan dengan status ini</p>
        </div>

        <div v-else class="space-y-4">
            <Link v-for="order in orders.data" :key="order.id" :href="`/orders/${order.id}`"
                  class="block bg-white rounded-2xl border border-gray-100 p-5 hover:shadow-md hover:border-[#1565C0]/30 transition-all">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-xs font-mono text-gray-400">{{ order.order_code }}</p>
                    <span class="text-[10px] font-semibold px-2.5 py-1 rounded-full"
                          :class="{ 'badge-pending': order.payment_status === 'pending', 'badge-paid': order.payment_status === 'paid', 'badge-failed': order.payment_status === 'failed' }">
                        {{ paymentLabel[order.payment_status] || order.payment_status }}
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-600">{{ new Date(order.created_at).toLocaleDateString('id-ID') }}</p>
                    <p class="text-lg font-bold text-[#1565C0]">Rp {{ Number(order.total_price).toLocaleString('id-ID') }}</p>
                </div>
            </Link>
            <Pagination :links="orders.links" />
        </div>
    </div>
</template>
