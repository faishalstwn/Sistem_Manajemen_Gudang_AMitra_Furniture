<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { usePage, Link, router } from '@inertiajs/vue3'
import FlashMessage from '@/Components/FlashMessage.vue'

const page = usePage()
const user = computed(() => page.props.auth?.user)
const cartCount = computed(() => page.props.cartCount || 0)

// Mobile menu
const mobileMenuOpen = ref(false)
const scrolled = ref(false)

// User dropdown
const userDropdownOpen = ref(false)

function toggleMobileMenu() {
    mobileMenuOpen.value = !mobileMenuOpen.value
}

function handleScroll() {
    scrolled.value = window.scrollY > 20
}

function logout() {
    router.post('/logout')
}

function closeDropdown(e) {
    if (!e.target.closest('.user-dropdown')) {
        userDropdownOpen.value = false
    }
}

onMounted(() => {
    window.addEventListener('scroll', handleScroll)
    document.addEventListener('click', closeDropdown)
})

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll)
    document.removeEventListener('click', closeDropdown)
})
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Navbar -->
        <nav
            class="fixed top-0 left-0 right-0 z-50 transition-all duration-300"
            :class="scrolled
                ? 'bg-white/90 backdrop-blur-xl shadow-sm border-b border-gray-100'
                : 'bg-white shadow-sm'"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Logo -->
                    <Link href="/" class="flex items-center gap-3 shrink-0">
                        <div class="w-10 h-10 rounded-xl overflow-hidden shadow-md shadow-yellow-200/50 flex items-center justify-center bg-white">
                            <img src="/assets/images/logo-baru.jpg" alt="Logo" class="w-full h-full object-cover" />
                        </div>
                        <span class="text-lg font-bold text-[#1565C0] hidden sm:block">A Mitra Furniture</span>
                    </Link>

                    <!-- Search Bar (Desktop) -->
                    <div class="hidden md:flex flex-1 max-w-lg mx-8">
                        <form @submit.prevent="router.get('/', { search: $event.target.search.value })" class="w-full">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input
                                    type="text"
                                    name="search"
                                    placeholder="Cari furniture impianmu..."
                                    class="w-full pl-10 pr-4 py-2.5 bg-gray-100 border-0 rounded-xl text-sm focus:ring-2 focus:ring-[#1565C0]/40 focus:bg-white transition-all duration-200"
                                />
                            </div>
                        </form>
                    </div>

                    <!-- Desktop Nav Items -->
                    <div class="hidden md:flex items-center gap-2">
                        <template v-if="user">
                            <!-- Cart -->
                            <Link href="/cart" class="relative p-2.5 text-gray-600 hover:text-[#1565C0] hover:bg-blue-50 rounded-xl transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />
                                </svg>
                                <span v-if="cartCount > 0"
                                      class="absolute -top-0.5 -right-0.5 bg-[#CC1F1A] text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center shadow-sm">
                                    {{ cartCount > 99 ? '99+' : cartCount }}
                                </span>
                            </Link>

                            <!-- Orders -->
                            <Link href="/orders" class="p-2.5 text-gray-600 hover:text-[#1565C0] hover:bg-blue-50 rounded-xl transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </Link>

                            <!-- User Dropdown -->
                            <div class="relative user-dropdown ml-1">
                                <button
                                    @click="userDropdownOpen = !userDropdownOpen"
                                    class="flex items-center gap-2 pl-3 pr-2 py-1.5 rounded-xl hover:bg-gray-100 transition-colors"
                                >
                                    <div class="w-7 h-7 bg-[#1565C0] rounded-lg flex items-center justify-center text-[#FFD600] text-xs font-bold">
                                        {{ user.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <span class="text-sm font-medium text-gray-700 max-w-[100px] truncate">{{ user.name }}</span>
                                    <svg class="w-4 h-4 text-gray-400 transition-transform" :class="{ 'rotate-180': userDropdownOpen }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <Transition
                                    enter-active-class="transition ease-out duration-150"
                                    enter-from-class="opacity-0 scale-95 -translate-y-1"
                                    enter-to-class="opacity-100 scale-100 translate-y-0"
                                    leave-active-class="transition ease-in duration-100"
                                    leave-from-class="opacity-100 scale-100 translate-y-0"
                                    leave-to-class="opacity-0 scale-95 -translate-y-1"
                                >
                                    <div v-if="userDropdownOpen"
                                         class="absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-xl border border-gray-100 py-1.5 z-50">
                                        <Link href="/profil" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            Profil Saya
                                        </Link>
                                        <Link href="/orders" class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                            </svg>
                                            Riwayat Pesanan
                                        </Link>
                                        <div class="border-t border-gray-100 my-1"></div>
                                        <button @click="logout"
                                                class="flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 w-full transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            Keluar
                                        </button>
                                    </div>
                                </Transition>
                            </div>
                        </template>

                        <template v-else>
                            <Link href="/login"
                                  class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-[#1565C0] transition-colors">
                                Masuk
                            </Link>
                            <Link href="/register"
                                  class="px-5 py-2.5 text-sm font-semibold text-[#0D47A1] bg-[#FFD600] hover:bg-[#F9A825] rounded-xl shadow-md shadow-yellow-200/50 transition-all duration-200 hover:shadow-lg">
                                Daftar
                            </Link>
                        </template>
                    </div>

                    <!-- Mobile menu button -->
                    <button @click="toggleMobileMenu" class="md:hidden p-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                        <svg v-if="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <Transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0 -translate-y-2"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-2"
            >
                <div v-if="mobileMenuOpen" class="md:hidden bg-white border-t border-gray-100 shadow-lg">
                    <!-- Mobile Search -->
                    <div class="px-4 py-3">
                        <form @submit.prevent="router.get('/', { search: $event.target.search.value }); mobileMenuOpen = false">
                            <input
                                type="text"
                                name="search"
                                placeholder="Cari furniture..."
                                class="w-full px-4 py-2.5 bg-gray-100 border-0 rounded-xl text-sm focus:ring-2 focus:ring-[#1565C0]/40"
                            />
                        </form>
                    </div>
                    <div class="px-4 pb-4 space-y-1">
                        <template v-if="user">
                            <Link href="/profil" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-lg" @click="mobileMenuOpen = false">Profil</Link>
                            <Link href="/cart" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-lg" @click="mobileMenuOpen = false">
                                Keranjang
                                <span v-if="cartCount > 0" class="ml-2 bg-[#CC1F1A] text-white px-2 py-0.5 rounded-full text-xs font-medium">{{ cartCount }}</span>
                            </Link>
                            <Link href="/orders" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-lg" @click="mobileMenuOpen = false">Pesanan</Link>
                            <button @click="logout" class="block w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 rounded-lg">Keluar</button>
                        </template>
                        <template v-else>
                            <Link href="/login" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 rounded-lg" @click="mobileMenuOpen = false">Masuk</Link>
                            <Link href="/register" class="block px-4 py-2.5 text-sm font-medium text-[#1565C0] hover:bg-blue-50 rounded-lg" @click="mobileMenuOpen = false">Daftar</Link>
                        </template>
                    </div>
                </div>
            </Transition>
        </nav>

        <!-- Main Content -->
        <main class="pt-16">
            <FlashMessage />
            <slot />
        </main>

        <!-- Bottom Navigation (Mobile) -->
        <nav class="md:hidden fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-lg border-t border-gray-200 z-40 safe-area-bottom">
            <div class="flex items-center justify-around py-2">
                <Link href="/" class="flex flex-col items-center gap-0.5 px-3 py-1.5 text-gray-500 hover:text-[#1565C0] transition-colors"
                      :class="{ 'text-[#1565C0]': $page.url === '/' }">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1" />
                    </svg>
                    <span class="text-[10px] font-medium">Home</span>
                </Link>

                <Link :href="user ? '/cart' : '/login'" class="relative flex flex-col items-center gap-0.5 px-3 py-1.5 text-gray-500 hover:text-[#1565C0] transition-colors"
                      :class="{ 'text-[#1565C0]': $page.url.startsWith('/cart') }">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />
                    </svg>
                    <span v-if="cartCount > 0"
                          class="absolute -top-0.5 right-1 bg-[#CC1F1A] text-white text-[8px] font-bold w-4 h-4 rounded-full flex items-center justify-center">
                        {{ cartCount > 9 ? '9+' : cartCount }}
                    </span>
                    <span class="text-[10px] font-medium">Cart</span>
                </Link>

                <Link :href="user ? '/orders' : '/login'" class="flex flex-col items-center gap-0.5 px-3 py-1.5 text-gray-500 hover:text-[#1565C0] transition-colors"
                      :class="{ 'text-[#1565C0]': $page.url.startsWith('/orders') }">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span class="text-[10px] font-medium">Pesanan</span>
                </Link>

                <Link :href="user ? '/profil' : '/login'" class="flex flex-col items-center gap-0.5 px-3 py-1.5 text-gray-500 hover:text-[#1565C0] transition-colors"
                      :class="{ 'text-[#1565C0]': $page.url.startsWith('/profil') }">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="text-[10px] font-medium">Profil</span>
                </Link>
            </div>
        </nav>

        <!-- Bottom padding for mobile nav -->
        <div class="md:hidden h-16"></div>
    </div>
</template>
