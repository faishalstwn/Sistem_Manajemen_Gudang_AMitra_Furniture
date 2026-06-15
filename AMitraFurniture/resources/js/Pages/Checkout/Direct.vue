<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import UserLayout from '@/Layouts/UserLayout.vue'
import axios from 'axios'

defineOptions({ layout: UserLayout })

const props = defineProps({ product: Object })

const quantity = ref(1)

const form = useForm({
    product_id: props.product.id,
    quantity: 1,
    alamat: '',
    nomor_telepon: '',
    payment_method: 'midtrans',
    voucher_code: '',
})

function increment() {
    if (quantity.value < props.product.stock) {
        quantity.value++
        form.quantity = quantity.value
    }
}
function decrement() {
    if (quantity.value > 1) {
        quantity.value--
        form.quantity = quantity.value
    }
}

// Voucher state
const voucherInput = ref('')
const voucherLoading = ref(false)
const voucherError = ref('')
const voucherSuccess = ref('')
const appliedVoucher = ref(null)
const discountAmount = ref(0)

const subtotal = computed(() => props.product.price * quantity.value)

const totalAfterDiscount = computed(() => {
    return Math.max(0, subtotal.value - discountAmount.value)
})

async function applyVoucher() {
    if (!voucherInput.value.trim()) {
        voucherError.value = 'Masukkan kode voucher terlebih dahulu.'
        return
    }

    voucherLoading.value = true
    voucherError.value = ''
    voucherSuccess.value = ''

    try {
        const response = await axios.post('/voucher/apply', {
            code: voucherInput.value.trim(),
            subtotal: subtotal.value,
        })

        if (response.data.valid) {
            appliedVoucher.value = response.data.voucher
            discountAmount.value = response.data.discount
            voucherSuccess.value = response.data.message
            form.voucher_code = voucherInput.value.trim()
        }
    } catch (error) {
        const msg = error.response?.data?.message || 'Gagal memvalidasi voucher.'
        voucherError.value = msg
        appliedVoucher.value = null
        discountAmount.value = 0
        form.voucher_code = ''
    } finally {
        voucherLoading.value = false
    }
}

function removeVoucher() {
    appliedVoucher.value = null
    discountAmount.value = 0
    voucherInput.value = ''
    voucherError.value = ''
    voucherSuccess.value = ''
    form.voucher_code = ''
}

function submit() {
    form.quantity = quantity.value
    form.post('/checkout/direct')
}
</script>

<template>
    <Head :title="`Checkout - ${product.name}`" />

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <Link href="/" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-[#1565C0] mb-6 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </Link>

        <h1 class="text-2xl font-bold text-gray-900 mb-8">Beli Langsung</h1>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
            <div class="lg:col-span-3">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Product Preview -->
                    <div class="bg-white rounded-2xl border border-gray-100 p-6">
                        <div class="flex items-center gap-4">
                            <div class="w-20 h-20 rounded-xl overflow-hidden bg-gray-100 shrink-0">
                                <img v-if="product.image" :src="`/${product.image}`" class="w-full h-full object-cover" />
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900">{{ product.name }}</p>
                                <p class="text-[#1565C0] font-bold mt-1">Rp {{ Number(product.price).toLocaleString('id-ID') }}</p>
                            </div>
                        </div>
                        <div class="mt-4 flex items-center gap-3">
                            <span class="text-sm text-gray-600">Jumlah:</span>
                            <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden">
                                <button type="button" @click="decrement" class="px-3 py-1.5 text-gray-600 hover:bg-gray-50 text-sm">−</button>
                                <span class="px-4 py-1.5 text-sm font-semibold border-x border-gray-200">{{ quantity }}</span>
                                <button type="button" @click="increment" class="px-3 py-1.5 text-gray-600 hover:bg-gray-50 text-sm">+</button>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping -->
                    <div class="bg-white rounded-2xl border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-900 mb-4">Informasi Pengiriman</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Lengkap</label>
                                <textarea v-model="form.alamat" rows="3" required
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#1565C0]/40 focus:border-[#1565C0] focus:bg-white transition-all"
                                    placeholder="Masukkan alamat lengkap"></textarea>
                                <p v-if="form.errors.alamat" class="mt-1 text-xs text-red-500">{{ form.errors.alamat }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Telepon</label>
                                <input v-model="form.nomor_telepon" type="text" required
                                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#1565C0]/40 focus:border-[#1565C0] focus:bg-white transition-all"
                                    placeholder="08xxxxxxxxxx" />
                                <p v-if="form.errors.nomor_telepon" class="mt-1 text-xs text-red-500">{{ form.errors.nomor_telepon }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Voucher Section -->
                    <div class="bg-white rounded-2xl border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-900 mb-4">
                            <span class="inline-flex items-center gap-2">
                                🎟️ Kode Voucher
                            </span>
                        </h3>

                        <!-- Applied voucher -->
                        <div v-if="appliedVoucher" class="mb-4">
                            <div class="flex items-center justify-between p-3 bg-emerald-50 border border-emerald-200 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-emerald-800">{{ appliedVoucher.code }}</p>
                                        <p class="text-xs text-emerald-600">{{ appliedVoucher.name }} — Hemat Rp {{ Number(discountAmount).toLocaleString('id-ID') }}</p>
                                    </div>
                                </div>
                                <button type="button" @click="removeVoucher"
                                    class="text-xs text-red-500 hover:text-red-700 font-medium px-2 py-1 rounded hover:bg-red-50 transition-colors">
                                    Hapus
                                </button>
                            </div>
                        </div>

                        <!-- Voucher input -->
                        <div v-else>
                            <div class="flex gap-2">
                                <input v-model="voucherInput" type="text"
                                    class="flex-1 px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#1565C0]/40 focus:border-[#1565C0] focus:bg-white transition-all uppercase"
                                    placeholder="Masukkan kode voucher"
                                    @keyup.enter.prevent="applyVoucher" />
                                <button type="button" @click="applyVoucher" :disabled="voucherLoading"
                                    class="px-5 py-3 bg-[#1565C0] hover:bg-[#0D47A1] text-white text-sm font-semibold rounded-xl transition-all duration-200 disabled:opacity-60 whitespace-nowrap">
                                    <span v-if="voucherLoading" class="inline-flex items-center gap-1.5">
                                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                        </svg>
                                        Cek...
                                    </span>
                                    <span v-else>Terapkan</span>
                                </button>
                            </div>
                            <p v-if="voucherError" class="mt-2 text-xs text-red-500 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                {{ voucherError }}
                            </p>
                            <p v-if="voucherSuccess" class="mt-2 text-xs text-emerald-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                {{ voucherSuccess }}
                            </p>
                        </div>
                    </div>

                    <!-- Payment -->
                    <div class="bg-white rounded-2xl border border-gray-100 p-6">
                        <h3 class="font-semibold text-gray-900 mb-4">Metode Pembayaran</h3>
                        <div class="space-y-3">
                            <label v-for="method in [
                                { value: 'midtrans', label: 'Midtrans', icon: '💳' },
                                { value: 'transfer_bca', label: 'Transfer BCA', icon: '🏦' },
                                { value: 'transfer_bri', label: 'Transfer BRI', icon: '🏦' },
                                { value: 'transfer_mandiri', label: 'Transfer Mandiri', icon: '🏦' },
                                { value: 'cod', label: 'COD', icon: '💵' },
                            ]" :key="method.value"
                                   class="flex items-center gap-3 p-4 border rounded-xl cursor-pointer transition-all"
                                   :class="form.payment_method === method.value ? 'border-[#1565C0] bg-blue-50 ring-1 ring-[#1565C0]' : 'border-gray-200 hover:border-gray-300'">
                                <input type="radio" v-model="form.payment_method" :value="method.value" class="text-[#1565C0] focus:ring-[#1565C0]" />
                                <span class="text-lg">{{ method.icon }}</span>
                                <span class="text-sm font-medium text-gray-900">{{ method.label }}</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" :disabled="form.processing"
                            class="w-full py-4 bg-gradient-to-r from-[#1565C0] to-[#1976D2] hover:from-[#0D47A1] hover:to-[#1565C0] text-white font-semibold rounded-xl shadow-lg shadow-blue-200/50 transition-all disabled:opacity-60">
                        <span v-if="form.processing">Memproses...</span>
                        <span v-else>Bayar Rp {{ Number(totalAfterDiscount).toLocaleString('id-ID') }}</span>
                    </button>
                </form>
            </div>

            <!-- Summary -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl border border-gray-100 p-6 sticky top-24">
                    <h3 class="font-semibold text-gray-900 mb-4">Ringkasan</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span>Rp {{ Number(subtotal).toLocaleString('id-ID') }}</span></div>
                        <div v-if="discountAmount > 0" class="flex justify-between">
                            <span class="text-emerald-600 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 2a2 2 0 00-2 2v14l3.5-2 3.5 2 3.5-2 3.5 2V4a2 2 0 00-2-2H5zm4.707 3.707a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L8.414 10H13a1 1 0 100-2H8.414l1.293-1.293z" clip-rule="evenodd"/></svg>
                                Diskon ({{ appliedVoucher?.code }})
                            </span>
                            <span class="text-emerald-600 font-medium">- Rp {{ Number(discountAmount).toLocaleString('id-ID') }}</span>
                        </div>
                        <div class="flex justify-between"><span class="text-gray-500">Ongkir</span><span class="text-emerald-600">Gratis</span></div>
                    </div>
                    <div class="border-t border-gray-100 mt-4 pt-4 flex items-center justify-between">
                        <span class="font-semibold">Total</span>
                        <span class="text-xl font-extrabold text-[#1565C0]">Rp {{ Number(totalAfterDiscount).toLocaleString('id-ID') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
