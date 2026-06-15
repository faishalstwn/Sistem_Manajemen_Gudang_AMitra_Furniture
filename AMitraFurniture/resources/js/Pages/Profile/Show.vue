<script setup>
import { useForm } from '@inertiajs/vue3'
import UserLayout from '@/Layouts/UserLayout.vue'

defineOptions({ layout: UserLayout })

const props = defineProps({
    user: Object,
    ordersYesterday: Array,
    ordersPaid: Array,
    ordersUnpaid: Array,
    ordersProcessing: Array,
    ordersShipping: Array,
})

const profileForm = useForm({
    name: props.user.name || '',
    email: props.user.email || '',
    phone: props.user.phone || '',
    address: props.user.address || '',
})

const passwordForm = useForm({
    current_password: '',
    new_password: '',
    new_password_confirmation: '',
})

function updateProfile() {
    profileForm.patch('/profil', { preserveScroll: true })
}

function updatePassword() {
    passwordForm.patch('/profil/password', {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
    })
}

const stats = [
    { label: 'Belum Bayar', value: props.ordersUnpaid?.length || 0, color: 'text-[#1565C0]', bg: 'bg-blue-50', icon: '⏳' },
    { label: 'Diproses', value: props.ordersProcessing?.length || 0, color: 'text-blue-600', bg: 'bg-blue-50', icon: '⚙️' },
    { label: 'Dikirim', value: props.ordersShipping?.length || 0, color: 'text-indigo-600', bg: 'bg-indigo-50', icon: '🚚' },
    { label: 'Selesai', value: props.ordersPaid?.length || 0, color: 'text-emerald-600', bg: 'bg-emerald-50', icon: '✅' },
]
</script>

<template>
    <Head title="Profil" />

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Profile Header -->
        <div class="bg-gradient-to-r from-[#1565C0] to-[#0D47A1] rounded-2xl p-6 md:p-8 text-white mb-8 relative overflow-hidden">
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full"></div>
            <div class="absolute -bottom-6 -left-6 w-24 h-24 bg-white/10 rounded-full"></div>
            <div class="relative flex items-center gap-4">
                <div class="w-16 h-16 bg-[#FFD600] rounded-2xl flex items-center justify-center text-2xl font-bold text-[#0D47A1]">
                    {{ user.name?.charAt(0)?.toUpperCase() || '?' }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold">{{ user.name }}</h1>
                    <p class="text-blue-200 text-sm">{{ user.email }}</p>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-8">
            <div v-for="stat in stats" :key="stat.label"
                 :class="stat.bg"
                 class="rounded-xl p-4 text-center">
                <span class="text-2xl">{{ stat.icon }}</span>
                <p class="text-2xl font-bold mt-1" :class="stat.color">{{ stat.value }}</p>
                <p class="text-xs text-gray-600 mt-0.5">{{ stat.label }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Profile Form -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-5">Informasi Profil</h2>
                <form @submit.prevent="updateProfile" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama</label>
                        <input v-model="profileForm.name" type="text" required
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#1565C0]/40 focus:border-[#1565C0] focus:bg-white transition-all" />
                        <p v-if="profileForm.errors.name" class="mt-1 text-xs text-red-500">{{ profileForm.errors.name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                        <input v-model="profileForm.email" type="email" required
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#1565C0]/40 focus:border-[#1565C0] focus:bg-white transition-all" />
                        <p v-if="profileForm.errors.email" class="mt-1 text-xs text-red-500">{{ profileForm.errors.email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Telepon</label>
                        <input v-model="profileForm.phone" type="text"
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#1565C0]/40 focus:border-[#1565C0] focus:bg-white transition-all"
                               placeholder="08xxxxxxxxxx" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat</label>
                        <textarea v-model="profileForm.address" rows="3"
                                  class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#1565C0]/40 focus:border-[#1565C0] focus:bg-white transition-all"
                                  placeholder="Alamat lengkap"></textarea>
                    </div>
                    <button type="submit" :disabled="profileForm.processing"
                            class="w-full py-3 bg-[#1565C0] hover:bg-[#0D47A1] text-white font-semibold rounded-xl shadow-md transition-all disabled:opacity-60">
                        {{ profileForm.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}
                    </button>
                </form>
            </div>

            <!-- Password Form -->
            <div class="bg-white rounded-2xl border border-gray-100 p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-5">Ubah Kata Sandi</h2>
                <form @submit.prevent="updatePassword" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Password Lama</label>
                        <input v-model="passwordForm.current_password" type="password" required
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#1565C0]/40 focus:border-[#1565C0] focus:bg-white transition-all" />
                        <p v-if="passwordForm.errors.current_password" class="mt-1 text-xs text-red-500">{{ passwordForm.errors.current_password }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Password Baru</label>
                        <input v-model="passwordForm.new_password" type="password" required
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#1565C0]/40 focus:border-[#1565C0] focus:bg-white transition-all"
                               placeholder="Minimal 8 karakter" />
                        <p v-if="passwordForm.errors.new_password" class="mt-1 text-xs text-red-500">{{ passwordForm.errors.new_password }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Password Baru</label>
                        <input v-model="passwordForm.new_password_confirmation" type="password" required
                               class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-[#1565C0]/40 focus:border-[#1565C0] focus:bg-white transition-all" />
                    </div>
                    <button type="submit" :disabled="passwordForm.processing"
                            class="w-full py-3 bg-gray-900 hover:bg-gray-800 text-white font-semibold rounded-xl shadow-md transition-all disabled:opacity-60">
                        {{ passwordForm.processing ? 'Menyimpan...' : 'Ubah Password' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</template>
