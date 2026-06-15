<script setup>
import { Link } from '@inertiajs/vue3'
import UserLayout from '@/Layouts/UserLayout.vue'
import ProductCard from '@/Components/ProductCard.vue'
import Pagination from '@/Components/Pagination.vue'

defineOptions({ layout: UserLayout })

const props = defineProps({
    products: Object,
    category: String,
})

const categoryMeta = {
    'Kursi': { emoji: '🪑', color: 'from-[#FFD600] to-[#F9A825]', description: 'Kursi berkualitas tinggi untuk kenyamanan sehari-hari' },
    'Sofa': { emoji: '🛋️', color: 'from-blue-500 to-indigo-500', description: 'Sofa mewah dengan desain modern dan nyaman' },
    'Meja': { emoji: '🪵', color: 'from-emerald-500 to-teal-500', description: 'Meja elegan untuk ruang kerja dan ruang makan' },
    'Lemari': { emoji: '🚪', color: 'from-purple-500 to-pink-500', description: 'Lemari praktis dengan kapasitas penyimpanan optimal' },
}

const meta = categoryMeta[props.category] || categoryMeta['Kursi']
</script>

<template>
    <Head :title="`Kategori ${category}`" />

    <div class="min-h-screen">
        <!-- Category Header -->
        <section :class="`bg-gradient-to-r ${meta.color} relative overflow-hidden`">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-white">
                <Link href="/" class="inline-flex items-center gap-1.5 text-white/80 hover:text-white text-sm mb-4 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Kembali ke Home
                </Link>
                <div class="flex items-center gap-4">
                    <span class="text-5xl">{{ meta.emoji }}</span>
                    <div>
                        <h1 class="text-3xl md:text-4xl font-bold">{{ category }}</h1>
                        <p class="text-white/80 mt-1">{{ meta.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Products -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <p class="text-sm text-gray-500 mb-6" v-if="products?.total">
                {{ products.total }} produk dalam kategori {{ category }}
            </p>

            <div v-if="!products?.data?.length" class="text-center py-20">
                <p class="text-gray-500">Belum ada produk di kategori ini</p>
                <Link href="/" class="inline-block mt-4 text-sm text-[#1565C0] hover:text-[#0D47A1] font-medium">
                    ← Lihat semua produk
                </Link>
            </div>

            <div v-else class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                <ProductCard
                    v-for="product in products.data"
                    :key="product.id"
                    :product="product"
                    class="animate-fade-in-up opacity-0"
                    :class="`stagger-${(products.data.indexOf(product) % 6) + 1}`"
                />
            </div>

            <Pagination :links="products.links" />
        </section>
    </div>
</template>
