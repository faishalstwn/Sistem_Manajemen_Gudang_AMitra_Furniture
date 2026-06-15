<script setup>
import { computed, ref, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const show = ref(false)
const message = ref('')
const type = ref('success')
let timeout = null

const flash = computed(() => page.props.flash)

watch(flash, (val) => {
    if (val?.success) {
        message.value = val.success
        type.value = 'success'
        show.value = true
        startTimer()
    } else if (val?.error) {
        message.value = val.error
        type.value = 'error'
        show.value = true
        startTimer()
    }
}, { immediate: true, deep: true })

function startTimer() {
    clearTimeout(timeout)
    timeout = setTimeout(() => {
        show.value = false
    }, 5000)
}

function close() {
    show.value = false
    clearTimeout(timeout)
}
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 translate-y-[-100%]"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 translate-y-[-100%]"
    >
        <div v-if="show" class="fixed top-20 left-1/2 -translate-x-1/2 z-[100] w-full max-w-md px-4">
            <div
                class="rounded-xl shadow-xl px-5 py-4 flex items-start gap-3"
                :class="type === 'success'
                    ? 'bg-emerald-50 border border-emerald-200 text-emerald-800'
                    : 'bg-red-50 border border-red-200 text-red-800'"
            >
                <!-- Icon -->
                <div class="shrink-0 mt-0.5">
                    <svg v-if="type === 'success'" class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <svg v-else class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <!-- Message -->
                <p class="text-sm font-medium flex-1">{{ message }}</p>
                <!-- Close -->
                <button @click="close" class="shrink-0 text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </Transition>
</template>
