<script setup>
import { ref, onMounted } from "vue";
import { Head, Link } from "@inertiajs/vue3";

const isDark = ref(true);

// Initialiser le thème au chargement
onMounted(() => {
    // Vérifier si un thème est déjà stocké
    const theme = localStorage.getItem("theme");
    if (theme) {
        isDark.value = theme === "dark";
        document.documentElement.classList.toggle("dark", isDark.value);
    }
});

const toggleTheme = () => {
    isDark.value = !isDark.value;
    // Sauvegarder le thème dans localStorage
    localStorage.setItem("theme", isDark.value ? "dark" : "light");
    document.documentElement.classList.toggle("dark");
};

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

function handleImageError() {
    document.getElementById("screenshot-container")?.classList.add("!hidden");
    document.getElementById("docs-card")?.classList.add("!row-span-1");
    document.getElementById("docs-card-content")?.classList.add("!flex-row");
    document.getElementById("background")?.classList.add("!hidden");
}
</script>

<template>
    <Head title="Bienvenue" />

    <div class="min-h-screen bg-zinc-900 text-white">
        <!-- Navbar -->
        <div class="relative sm:flex sm:justify-center sm:items-center">
            <div class="p-6 flex justify-end items-center gap-4">
                <template v-if="canLogin">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="font-semibold text-white hover:text-gray-300"
                    >
                        Dashboard
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="font-semibold text-white hover:text-gray-300"
                        >
                            Connexion
                        </Link>
                        <Link
                            v-if="canRegister"
                            :href="route('register')"
                            class="ml-4 font-semibold text-white hover:text-gray-300"
                        >
                            Inscription
                        </Link>
                    </template>
                </template>

                <!-- Toggle Theme Button -->
                <button
                    @click="toggleTheme"
                    class="p-2 rounded-full hover:bg-zinc-700"
                >
                    <svg
                        v-if="isDark"
                        class="w-6 h-6"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                        />
                    </svg>
                    <svg
                        v-else
                        class="w-6 h-6"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"
                        />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Hero Section -->
        <div class="max-w-7xl mx-auto px-6 py-16">
            <h1 class="text-4xl font-bold mb-8 text-center">
                Bienvenue sur l'Intranet Technicien
            </h1>

            <div class="text-center max-w-2xl mx-auto">
                <p class="text-xl text-gray-400 mb-8">
                    Plateforme de gestion des interventions techniques.
                </p>
                <p class="text-lg text-gray-500">
                    Veuillez vous connecter pour accéder à votre espace de
                    travail.
                </p>
            </div>
        </div>
    </div>
</template>
