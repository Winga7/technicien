<script setup>
import { ref, onMounted } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import ThemeToggle from '@/Components/ThemeToggle.vue';

const isDark = ref(localStorage.getItem('theme') === 'dark');

onMounted(() => {
    window.addEventListener('theme-changed', (e) => {
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

    <div class="min-h-screen bg-white dark:bg-zinc-900 text-gray-800 dark:text-white flex flex-col justify-between">
        <!-- Navbar -->
        <div class="relative sm:flex sm:justify-center sm:items-center">
            <div class="p-6 flex justify-end items-center gap-4">
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
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="ml-4 font-semibold text-gray-800 dark:text-white hover:text-gray-600 dark:hover:text-gray-300"
                        >
                            Inscription
                        </Link>
                    </template>
                </template>

                <ThemeToggle />
            </div>
        </div>

        <!-- Hero Section -->
        <div
            class="flex-grow flex flex-col justify-center items-center px-6 py-16"
        >
            <h1 class="text-4xl font-bold mb-8 text-center">
                Bienvenue sur l'Intranet Technicien
            </h1>

            <div class="text-center max-w-2xl mx-auto">
                <p class="text-xl text-gray-400 mb-8">
                    Plateforme de gestion des interventions techniques.
                </p>
                <p class="text-lg text-gray-500 mb-8">
                    Veuillez vous connecter pour accéder à votre espace de
                    travail.
                </p>
                <div class="flex justify-center max-w-xs mx-auto">
                    <img
                        src="/images/accueil.jpg"
                        alt="Accueil"
                        class="rounded-lg shadow-lg max-w-full h-auto"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
