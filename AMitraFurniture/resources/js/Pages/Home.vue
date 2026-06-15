<script setup>
import { ref, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import UserLayout from '@/Layouts/UserLayout.vue'
import ProductCard from '@/Components/ProductCard.vue'
import Pagination from '@/Components/Pagination.vue'

defineOptions({ layout: UserLayout })

const props = defineProps({
    products: Object,
    filters: {
        type: Object,
        default: () => ({}),
    },
})

const page = usePage()
const user = computed(() => page.props.auth?.user)

const categories = [
    { name: 'Kursi', icon: 'chair', emoji: '🪑' },
    { name: 'Sofa', icon: 'couch', emoji: '🛋️' },
    { name: 'Meja', icon: 'table', emoji: '🪑' },
    { name: 'Lemari', icon: 'cabinet', emoji: '🚪' },
]

const searchQuery = ref(props.filters?.search || '')

function search() {
    router.get('/', { search: searchQuery.value }, { preserveState: true })
}

function addToCart(product) {
    if (!user.value) {
        router.visit('/login')
        return
    }
    router.post(`/cart/add/${product.id}`, { quantity: 1 }, {
        preserveScroll: true,
        preserveState: true,
    })
}
</script>

<template>
    <Head title="Home" />

    <div class="min-h-screen">
        <!-- Hero Section -->
        <section class="relative overflow-hidden bg-gradient-to-br from-blue-50 via-blue-100 to-slate-100">
            <div class="absolute inset-0 opacity-30">
                <div class="absolute top-10 left-10 w-72 h-72 bg-blue-200 rounded-full mix-blend-multiply filter blur-3xl animate-pulse"></div>
                <div class="absolute bottom-10 right-10 w-72 h-72 bg-yellow-200 rounded-full mix-blend-multiply filter blur-3xl animate-pulse" style="animation-delay: 1s"></div>
            </div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
                <div class="text-center max-w-3xl mx-auto">
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-gray-900 leading-tight tracking-tight">
                        Furniture
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#1565C0] to-[#1976D2]">Premium</span>
                        <br />untuk Rumah Impian
                    </h1>
                    <p class="mt-6 text-lg text-gray-600 max-w-xl mx-auto leading-relaxed">
                        Temukan koleksi furniture terbaik dengan kualitas tinggi dan desain modern untuk melengkapi ruangan Anda.
                    </p>

                    <!-- Search Bar -->
                    <form @submit.prevent="search" class="mt-8 max-w-lg mx-auto">
                        <div class="relative flex items-center">
                            <svg class="absolute left-4 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Cari furniture impianmu..."
                                class="w-full pl-12 pr-28 py-4 bg-white border-0 rounded-2xl shadow-lg shadow-blue-100/50 text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-[#1565C0]/40 focus:shadow-xl transition-all duration-300"
                            />
                            <button
                                type="submit"
                                class="absolute right-2 px-6 py-2.5 bg-[#1565C0] hover:bg-[#0D47A1] text-white font-semibold text-sm rounded-xl shadow-md shadow-blue-200/50 transition-all duration-200 hover:shadow-lg"
                            >
                                Cari
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <!-- Promo Banners -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-8 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gradient-to-r from-[#1565C0] to-[#1976D2] rounded-2xl p-6 text-white shadow-xl shadow-blue-200/30 flex items-center justify-between overflow-hidden relative group">
                    <div class="relative z-10">
                        <span class="text-xs font-semibold uppercase tracking-wider text-blue-100">Promo Spesial</span>
                        <h3 class="text-2xl font-bold mt-1">BIG SALE</h3>
                        <p class="text-blue-100 text-sm mt-1">Diskon hingga <span class="text-[#FFD600] font-bold">50%</span></p>
                    </div>
                    <div class="text-6xl opacity-20 group-hover:opacity-30 group-hover:scale-110 transition-all duration-500">🪑</div>
                    <div class="absolute -bottom-4 -right-4 w-32 h-32 bg-white/10 rounded-full"></div>
                </div>

                <div class="bg-gradient-to-r from-emerald-600 to-emerald-500 rounded-2xl p-6 text-white shadow-xl shadow-emerald-200/30 flex items-center justify-between overflow-hidden relative group">
                    <div class="relative z-10">
                        <span class="text-xs font-semibold uppercase tracking-wider text-emerald-100">Koleksi Baru</span>
                        <h3 class="text-2xl font-bold mt-1">NEW ARRIVAL</h3>
                        <p class="text-emerald-100 text-sm mt-1">Diskon <span class="text-white font-bold">20%</span> transaksi pertama</p>
                    </div>
                    <div class="text-6xl opacity-20 group-hover:opacity-30 group-hover:scale-110 transition-all duration-500">🛋️</div>
                    <div class="absolute -bottom-4 -right-4 w-32 h-32 bg-white/10 rounded-full"></div>
                </div>
            </div>
        </section>

        <!-- Categories -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h2 class="text-sm font-semibold uppercase tracking-wider text-gray-400 mb-6">Kategori</h2>
            <div class="grid grid-cols-4 gap-3 md:gap-4">
                <Link
                    v-for="cat in categories"
                    :key="cat.name"
                    :href="`/kategori/${cat.name}`"
                    class="group flex flex-col items-center gap-3 p-4 md:p-6 bg-white rounded-2xl border border-gray-100 hover:border-[#1565C0]/30 hover:shadow-lg hover:shadow-blue-50 transition-all duration-300"
                >
                    <span class="text-3xl md:text-4xl group-hover:scale-110 transition-transform duration-300">{{ cat.emoji }}</span>
                    <span class="text-xs md:text-sm font-medium text-gray-700 group-hover:text-[#1565C0] transition-colors">{{ cat.name }}</span>
                </Link>
            </div>
        </section>

        <!-- Products Grid -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl md:text-2xl font-bold text-gray-900">
                        <template v-if="filters?.search">
                            Hasil Pencarian: "{{ filters.search }}"
                        </template>
                        <template v-else-if="filters?.category">
                            Produk {{ filters.category }}
                        </template>
                        <template v-else>
                            Semua Produk
                        </template>
                    </h2>
                    <p class="text-sm text-gray-500 mt-1" v-if="products?.total">
                        {{ products.total }} produk ditemukan
                    </p>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="!products?.data?.length" class="text-center py-20">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <p class="text-gray-500 font-medium">Produk tidak ditemukan</p>
                <p class="text-sm text-gray-400 mt-1">Coba kata kunci lain atau lihat semua produk</p>
                <Link href="/" class="inline-block mt-4 px-5 py-2.5 text-sm font-medium text-[#1565C0] bg-blue-50 hover:bg-blue-100 rounded-xl transition-colors">
                    Lihat Semua Produk
                </Link>
            </div>

            <!-- Products -->
            <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                <ProductCard
                    v-for="product in products.data"
                    :key="product.id"
                    :product="product"
                    class="animate-fade-in-up opacity-0"
                    :class="`stagger-${(products.data.indexOf(product) % 6) + 1}`"
                />
            </div>

            <!-- Pagination -->
            <Pagination :links="products.links" />
        </section>
    </div>
</template>
