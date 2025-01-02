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
        <!-- Navbar avec bouton connexion -->
        <div class="fixed top-2 right-24 md:top-4 md:right-28 z-50">
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

        <!-- ThemeSwitch -->
        <div class="fixed top-2 right-2 md:top-4 md:right-4 z-50">
            <ThemeSwitch />
        </div>

        <div class="flex-grow flex flex-col justify-center items-center px-6 py-16">
            <h1 class="text-4xl font-bold mb-8 text-center text-gray-900 dark:text-white">
                Bienvenue sur l'Intranet Technicien
            </h1>
            <div class="text-center max-w-2xl mx-auto">
                <p class="text-xl text-gray-600 dark:text-gray-300 mb-8">
                    Plateforme de gestion des interventions techniques.
                </p>
                <p class="text-lg text-gray-500 dark:text-gray-400 mb-8">
                    Veuillez vous connecter pour accéder à votre espace de travail.
                </p>
            </div>
        </div>

        <!-- Contenu principal -->
        <div class="flex justify-center max-w-xs mx-auto">
            <img
                src="/images/accueil.jpg"
                alt="Accueil"
                class="rounded-lg shadow-lg max-w-full h-auto"
            />
        </div>
    </div>
</template>
