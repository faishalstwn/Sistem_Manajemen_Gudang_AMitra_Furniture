<script setup>
import { onMounted } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import axios from 'axios'
import UserLayout from '@/Layouts/UserLayout.vue'

defineOptions({ layout: UserLayout })

const props = defineProps({ order: Object })

function updateStatusAndRedirect(orderId, redirectPath) {
    // Kirim request ke server untuk cek & update status dari Midtrans API
    // Menggunakan axios yang sudah otomatis handle XSRF-TOKEN cookie
    axios.post(`/payment/update-status/${orderId}`)
        .then(() => router.visit(redirectPath))
        .catch(() => router.visit(redirectPath))
}

onMounted(() => {
    if (props.order.snap_token) {
        // Load Midtrans Snap.js
        const script = document.createElement('script')
        script.src = 'https://app.sandbox.midtrans.com/snap/snap.js'
        script.setAttribute('data-client-key', usePage().props.midtrans_client_key || 'SB-Mid-client-PLACEHOLDER')
        script.onload = () => {
            window.snap.pay(props.order.snap_token, {
                onSuccess: (result) => {
                    console.log('Payment success:', result)
                    updateStatusAndRedirect(props.order.id, `/payment/finish/${props.order.id}`)
                },
                onPending: (result) => {
                    console.log('Payment pending:', result)
                    updateStatusAndRedirect(props.order.id, `/orders/${props.order.id}`)
                },
                onError: (result) => {
                    console.log('Payment error:', result)
                    updateStatusAndRedirect(props.order.id, `/orders/${props.order.id}`)
                },
                onClose: () => {},
            })
        }
        document.head.appendChild(script)
    }
})
</script>

<template>
    <Head title="Pembayaran" />

    <div class="max-w-lg mx-auto px-4 py-16 text-center">
        <div class="bg-white rounded-2xl border border-gray-100 p-8 shadow-lg">
            <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8 text-blue-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
            </div>
            <h1 class="text-xl font-bold text-gray-900 mb-2">Memproses Pembayaran</h1>
            <p class="text-gray-500 text-sm mb-6">
                Jendela pembayaran Midtrans akan segera muncul. Jangan tutup halaman ini.
            </p>

            <div class="bg-gray-50 rounded-xl p-4 text-left text-sm space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-500">Kode Pesanan</span>
                    <span class="font-mono text-gray-900">{{ order.order_code }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Total</span>
                    <span class="font-bold text-[#1565C0]">Rp {{ Number(order.total_price).toLocaleString('id-ID') }}</span>
                </div>
            </div>

            <Link :href="`/orders/${order.id}`" class="inline-block mt-6 text-sm text-gray-500 hover:text-[#1565C0] transition-colors">
                Kembali ke detail pesanan
            </Link>
        </div>
    </div>
</template>
