<script setup>
import { ref, computed } from 'vue'
import { usePage, Link, router } from '@inertiajs/vue3'
import FlashMessage from '@/Components/FlashMessage.vue'

const page = usePage()
const user = computed(() => page.props.auth?.user)
const sidebarOpen = ref(false)

const menuItems = [
    { label: 'Dashboard', href: '/admin/dashboard', icon: 'chart', match: '/admin/dashboard' },
    { label: 'Pesanan', href: '/admin/pesanan', icon: 'orders', match: '/admin/pesanan' },
    { label: 'Produk', href: '/admin/produk', icon: 'product', match: '/admin/produk' },
    { label: 'Pengiriman', href: '/admin/pengiriman', icon: 'truck', match: '/admin/pengiriman' },
    { label: 'Laporan Penjualan', href: '/admin/laporan', icon: 'report', match: '/admin/laporan' },
    { divider: true, label: 'Gudang' },
    { label: 'Monitor Gudang', href: '/admin/gudang', icon: 'warehouse', match: '/admin/gudang' },
    { label: 'Kelola Stok', href: '/admin/gudang/kelola', icon: 'stock', match: '/admin/gudang/kelola' },
    { label: 'Barang Masuk', href: '/admin/barang-masuk', icon: 'inbox', match: '/admin/barang-masuk' },
    { label: 'Barang Keluar', href: '/admin/barang-keluar', icon: 'outbox', match: '/admin/barang-keluar' },
    { label: 'Lokasi Gudang', href: '/admin/lokasi-gudang', icon: 'map', match: '/admin/lokasi-gudang' },
    { label: 'Stock Opname', href: '/admin/stock-opname', icon: 'clipboard', match: '/admin/stock-opname' },
    { label: 'Laporan Gudang', href: '/admin/laporan-gudang', icon: 'report', match: '/admin/laporan-gudang' },
]

function isActive(match) {
    const url = page.url.split('?')[0]
    const matches = url === match || url.startsWith(match + '/')
    if (!matches) return false
    // Check if there's a more specific menu item that also matches
    for (const item of menuItems) {
        if (item.divider || !item.match || item.match === match) continue
        if (item.match.length > match.length) {
            const otherMatches = url === item.match || url.startsWith(item.match + '/')
            if (otherMatches) return false
        }
    }
    return true
}

function logout() {
    router.post('/logout')
}
</script>

<template>
    <div class="min-h-screen bg-slate-50 flex">
        <!-- Sidebar Overlay (Mobile) -->
        <Transition
            enter-active-class="transition-opacity ease-out duration-300"
            enter-from-class="opacity-0" enter-to-class="opacity-100"
            leave-active-class="transition-opacity ease-in duration-200"
            leave-from-class="opacity-100" leave-to-class="opacity-0"
        >
            <div v-if="sidebarOpen" class="fixed inset-0 bg-black/40 z-40 lg:hidden" @click="sidebarOpen = false"></div>
        </Transition>

        <!-- Sidebar -->
        <aside
            class="fixed lg:sticky top-0 left-0 z-50 lg:z-auto w-64 h-screen bg-[#0D47A1] flex flex-col transition-transform duration-300 lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <!-- Logo -->
            <div class="p-5 border-b border-blue-900">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl overflow-hidden shadow-md flex items-center justify-center bg-white">
                        <img src="/assets/images/logo-baru.jpg" alt="Logo" class="w-full h-full object-cover" />
                    </div>
                    <div>
                        <span class="text-sm font-bold text-white">Admin Panel</span>
                        <p class="text-[10px] text-blue-300">A Mitra Furniture</p>
                    </div>
                </div>
            </div>

            <!-- Menu -->
            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-0.5">
                <template v-for="item in menuItems" :key="item.label">
                    <!-- Divider -->
                    <div v-if="item.divider" class="pt-4 pb-2 px-3">
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-blue-300">{{ item.label }}</p>
                    </div>
                    <!-- Menu Item -->
                    <Link v-else :href="item.href"
                          class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-all duration-200"
                          :class="isActive(item.match)
                              ? 'bg-[#FFD600] text-[#0D47A1] font-bold shadow-sm'
                              : 'text-blue-100 hover:bg-blue-800 hover:text-white'"
                          @click="sidebarOpen = false">
                        <span class="w-5 h-5 flex items-center justify-center text-xs">
                            <template v-if="item.icon === 'chart'">📊</template>
                            <template v-else-if="item.icon === 'orders'">📦</template>
                            <template v-else-if="item.icon === 'product'">🏷️</template>
                            <template v-else-if="item.icon === 'truck'">🚚</template>
                            <template v-else-if="item.icon === 'warehouse'">🏭</template>
                            <template v-else-if="item.icon === 'stock'">📋</template>
                            <template v-else-if="item.icon === 'inbox'">📥</template>
                            <template v-else-if="item.icon === 'outbox'">📤</template>
                            <template v-else-if="item.icon === 'map'">🗺️</template>
                            <template v-else-if="item.icon === 'clipboard'">📝</template>
                            <template v-else-if="item.icon === 'report'">📈</template>
                            <template v-else>📄</template>
                        </span>
                        {{ item.label }}
                    </Link>
                </template>
            </nav>

            <!-- User -->
            <div class="p-3 border-t border-blue-900">
                <div class="flex items-center gap-3 px-3 py-2">
                    <div class="w-8 h-8 bg-[#FFD600] rounded-lg flex items-center justify-center text-[#0D47A1] text-xs font-bold">
                        {{ user?.name?.charAt(0)?.toUpperCase() || 'A' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ user?.name }}</p>
                        <p class="text-[10px] text-blue-300 truncate">Admin</p>
                    </div>
                    <button @click="logout" class="p-1.5 text-blue-300 hover:text-[#FFD600] rounded-lg hover:bg-blue-800 transition-colors" title="Logout">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-h-screen">
            <!-- Top Bar -->
            <header class="sticky top-0 z-30 bg-white/95 backdrop-blur-xl border-b border-blue-100">
                <div class="flex items-center justify-between h-14 px-4 lg:px-8">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div class="hidden lg:block"></div>
                    <div class="text-sm text-gray-500">
                        {{ new Date().toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) }}
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-4 lg:p-8">
                <FlashMessage />
                <slot />
            </main>
        </div>
    </div>
</template>
