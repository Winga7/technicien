<script setup>
import { ref, onMounted } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import ThemeSwitch from "@/Components/ThemeSwitch.vue";

const isDark = ref(localStorage.getItem("theme") === "dark");

onMounted(() => {
    window.addEventListener("theme-changed", (e) => {
        isDark.value = e.detail;
    });
});

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head title="Bienvenue" />

    <div class="min-h-screen bg-gray-100 dark:bg-zinc-900">
        <div class="fixed top-2 right-2 md:top-4 md:right-4 z-50">
            <ThemeSwitch />
        </div>

        <!-- Navbar -->
        <div class="relative px-4 sm:px-6 lg:px-8">
            <div class="flex justify-end items-center py-4">
                <template v-if="canLogin">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="font-semibold text-gray-800 dark:text-white hover:text-gray-600 dark:hover:text-gray-300"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="font-semibold text-gray-800 dark:text-white hover:text-gray-600 dark:hover:text-gray-300"
                        >
                            Connexion
                        </Link>
                    </template>
                </template>
            </div>
        </div>

        <!-- Contenu principal -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center">
                <h1 class="text-4xl sm:text-5xl md:text-6xl font-bold text-gray-900 dark:text-white mb-8">
                    Bienvenue sur <span class="text-indigo-600 dark:text-indigo-400">Help Desk</span>
                </h1>
                <p class="text-lg sm:text-xl text-gray-600 dark:text-gray-300 mb-12">
                    Gérez vos tickets et suivez leur progression en temps réel
                </p>
            </div>
        </div>
    </div>
</template>
