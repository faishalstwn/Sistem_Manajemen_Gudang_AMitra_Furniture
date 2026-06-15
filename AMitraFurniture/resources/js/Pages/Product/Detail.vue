<script setup>
import { ref, computed } from 'vue'
import { router, useForm, usePage } from '@inertiajs/vue3'
import UserLayout from '@/Layouts/UserLayout.vue'
import ProductCard from '@/Components/ProductCard.vue'

defineOptions({ layout: UserLayout })

const props = defineProps({
    product: Object,
    relatedProducts: Array,
    reviews: Array,
    averageRating: Number,
    totalReviews: Number,
})

const page = usePage()
const user = computed(() => page.props.auth?.user)
const quantity = ref(1)
const activeTab = ref('description')

function addToCart() {
    if (!user.value) {
        router.visit('/login')
        return
    }
    router.post(`/cart/add/${props.product.id}`, { quantity: quantity.value }, {
        preserveScroll: true,
    })
}

function buyNow() {
    if (!user.value) {
        router.visit('/login')
        return
    }
    router.visit(`/checkout/${props.product.id}`)
}

function increment() {
    if (quantity.value < props.product.stock) quantity.value++
}

function decrement() {
    if (quantity.value > 1) quantity.value--
}

const filledStars = (rating) => Math.floor(rating || 0)
const hasHalfStar = (rating) => (rating || 0) % 1 >= 0.5
</script>

<template>
    <Head :title="product.name" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-sm text-gray-500 mb-6">
            <Link href="/" class="hover:text-[#1565C0] transition-colors">Home</Link>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <Link v-if="product.category" :href="`/kategori/${product.category}`" class="hover:text-[#1565C0] transition-colors">{{ product.category }}</Link>
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="text-gray-900 font-medium truncate max-w-[200px]">{{ product.name }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
            <!-- Product Image -->
            <div class="space-y-4">
                <div class="aspect-square bg-gray-100 rounded-2xl overflow-hidden border border-gray-200">
                    <img
                        v-if="product.image"
                        :src="`/${product.image}`"
                        :alt="product.name"
                        class="w-full h-full object-cover"
                    />
                    <div v-else class="w-full h-full flex items-center justify-center text-gray-300">
                        <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Product Info -->
            <div class="space-y-6">
                <div>
                    <span class="inline-block bg-blue-100 text-[#1565C0] text-xs font-semibold px-3 py-1 rounded-full mb-3">
                        {{ product.category }}
                    </span>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">{{ product.name }}</h1>

                    <!-- Rating -->
                    <div v-if="totalReviews > 0" class="flex items-center gap-2 mt-3">
                        <div class="flex items-center">
                            <template v-for="i in 5" :key="i">
                                <svg class="w-4 h-4" :class="i <= filledStars(averageRating) ? 'text-[#FFD600]' : 'text-gray-200'" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </template>
                        </div>
                        <span class="text-sm text-gray-600">{{ Number(averageRating).toFixed(1) }} ({{ totalReviews }} ulasan)</span>
                    </div>
                </div>

                <!-- Price -->
                <div class="bg-gradient-to-r from-blue-50 to-slate-50 rounded-xl p-5">
                    <p class="text-3xl font-extrabold text-[#1565C0]">
                        Rp {{ Number(product.price).toLocaleString('id-ID') }}
                    </p>
                </div>

                <!-- Stock Info -->
                <div class="flex items-center gap-3">
                    <span v-if="product.stock > 10" class="flex items-center gap-1.5 text-sm text-emerald-600">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                        Stok tersedia
                    </span>
                    <span v-else-if="product.stock > 0" class="flex items-center gap-1.5 text-sm text-[#1565C0]">
                        <span class="w-2 h-2 bg-[#1565C0] rounded-full"></span>
                        Sisa {{ product.stock }} unit
                    </span>
                    <span v-else class="flex items-center gap-1.5 text-sm text-red-600">
                        <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                        Stok habis
                    </span>
                </div>

                <!-- Quantity -->
                <div v-if="product.stock > 0">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah</label>
                    <div class="flex items-center gap-3">
                        <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden">
                            <button @click="decrement" class="px-4 py-2.5 text-gray-600 hover:bg-gray-50 transition-colors">−</button>
                            <span class="px-5 py-2.5 font-semibold text-gray-900 min-w-[48px] text-center border-x border-gray-200">{{ quantity }}</span>
                            <button @click="increment" class="px-4 py-2.5 text-gray-600 hover:bg-gray-50 transition-colors">+</button>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div v-if="product.stock > 0" class="flex gap-3">
                    <button
                        @click="addToCart"
                        class="flex-1 py-3.5 border-2 border-[#1565C0] text-[#1565C0] font-semibold rounded-xl hover:bg-blue-50 transition-all duration-200 flex items-center justify-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />
                        </svg>
                        Keranjang
                    </button>
                    <button
                        @click="buyNow"
                        class="flex-1 py-3.5 bg-[#1565C0] hover:bg-[#0D47A1] text-white font-semibold rounded-xl shadow-lg shadow-blue-200/50 transition-all duration-200 hover:shadow-xl"
                    >
                        Beli Sekarang
                    </button>
                </div>

                <!-- Description -->
                <div class="border-t border-gray-100 pt-6">
                    <h3 class="font-semibold text-gray-900 mb-3">Deskripsi Produk</h3>
                    <p class="text-gray-600 text-sm leading-relaxed whitespace-pre-line">{{ product.description || 'Belum ada deskripsi untuk produk ini.' }}</p>
                </div>
            </div>
        </div>

        <!-- Reviews Section -->
        <section v-if="reviews && reviews.length > 0" class="mt-16">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Ulasan Pelanggan ({{ totalReviews }})</h2>
            <div class="space-y-4">
                <div v-for="review in reviews" :key="review.id"
                     class="bg-white rounded-xl border border-gray-100 p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-8 h-8 bg-[#1565C0] rounded-full flex items-center justify-center text-[#FFD600] text-xs font-bold">
                            {{ review.user?.name?.charAt(0)?.toUpperCase() || '?' }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ review.user?.name || 'Anonim' }}</p>
                            <div class="flex items-center gap-1">
                                <template v-for="i in 5" :key="i">
                                    <svg class="w-3 h-3" :class="i <= review.rating ? 'text-[#FFD600]' : 'text-gray-200'" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </template>
                            </div>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600">{{ review.comment }}</p>
                </div>
            </div>
        </section>

        <!-- Related Products -->
        <section v-if="relatedProducts && relatedProducts.length > 0" class="mt-16 pb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Produk Serupa</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <ProductCard
                    v-for="rp in relatedProducts"
                    :key="rp.id"
                    :product="rp"
                />
            </div>
        </section>
    </div>
</template>
