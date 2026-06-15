<script setup>
import { computed } from 'vue'
import UserLayout from '@/Layouts/UserLayout.vue'

defineOptions({ layout: UserLayout })

const props = defineProps({ order: Object })

const steps = computed(() => {
    const s = [
        { key: 'pending', label: 'Pesanan Dibuat', icon: '📝' },
        { key: 'processing', label: 'Diproses', icon: '⚙️' },
        { key: 'shipped', label: 'Dikirim', icon: '🚚' },
        { key: 'delivered', label: 'Diterima', icon: '✅' },
    ]
    const statusOrder = ['pending', 'processing', 'shipped', 'delivered']
    const currentIdx = statusOrder.indexOf(props.order.status)
    return s.map((step, i) => ({
        ...step,
        completed: i <= currentIdx,
        current: i === currentIdx,
    }))
})
</script>

<template>
    <Head :title="`Lacak Pesanan ${order.order_code}`" />

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <Link href="/orders" class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-[#1565C0] mb-6 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali
        </Link>

        <h1 class="text-2xl font-bold text-gray-900 mb-2">Lacak Pesanan</h1>
        <p class="text-sm text-gray-500 font-mono mb-8">{{ order.order_code }}</p>

        <!-- Timeline -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6 md:p-8 mb-8">
            <div class="relative">
                <div v-for="(step, index) in steps" :key="step.key"
                     class="flex items-start gap-4 pb-8 last:pb-0 relative">
                    <!-- Line -->
                    <div v-if="index < steps.length - 1"
                         class="absolute left-5 top-10 w-0.5 h-[calc(100%-24px)]"
                         :class="step.completed && steps[index + 1]?.completed ? 'bg-[#1565C0]' : 'bg-gray-200'">
                    </div>

                    <!-- Circle -->
                    <div class="relative z-10 shrink-0 w-10 h-10 rounded-full flex items-center justify-center text-lg transition-all duration-300"
                         :class="step.completed
                             ? step.current
                                 ? 'bg-[#1565C0] shadow-lg shadow-blue-200/50 ring-4 ring-blue-100'
                                 : 'bg-[#1976D2]'
                             : 'bg-gray-100'">
                        <span v-if="step.completed">{{ step.icon }}</span>
                        <span v-else class="text-gray-400 text-sm">{{ index + 1 }}</span>
                    </div>

                    <!-- Content -->
                    <div class="pt-1.5">
                        <p class="font-semibold text-sm"
                           :class="step.completed ? 'text-gray-900' : 'text-gray-400'">
                            {{ step.label }}
                        </p>
                        <p v-if="step.current" class="text-xs text-[#1565C0] mt-0.5 font-medium">Status saat ini</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Info -->
        <div class="bg-white rounded-2xl border border-gray-100 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Detail Pesanan</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Alamat</span>
                    <span class="text-gray-900 text-right max-w-[60%]">{{ order.alamat }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Telepon</span>
                    <span class="text-gray-900">{{ order.nomor_telepon }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Total</span>
                    <span class="font-bold text-[#1565C0]">Rp {{ Number(order.total_price).toLocaleString('id-ID') }}</span>
                </div>
            </div>

            <!-- Items -->
            <div class="border-t border-gray-100 mt-4 pt-4 space-y-3">
                <div v-for="item in order.order_items" :key="item.id" class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-100 shrink-0">
                        <img v-if="item.product?.image" :src="`/${item.product.image}`" class="w-full h-full object-cover" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ item.product?.name }}</p>
                        <p class="text-xs text-gray-500">{{ item.quantity }}x @ Rp {{ Number(item.price).toLocaleString('id-ID') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
