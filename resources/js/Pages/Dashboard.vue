<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Welcome from "@/Components/Welcome.vue";
import { ref, onMounted } from "vue";

// Définir les compteurs
const ticketsEnAttente = ref(0);
const ticketsEnCours = ref(0);
const ticketsTermines = ref(0);

// Récupérer les statistiques depuis le contrôleur
defineProps({
    statistics: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <div>
                <!-- Quick Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div
                        class="bg-gray-100 dark:bg-zinc-900 p-3 rounded-lg border border-gray-200 dark:border-zinc-700"
                    >
                        <h3
                            class="text-lg font-semibold mb-1 text-gray-800 dark:text-gray-200"
                        >
                            Tickets en attente
                        </h3>
                        <p class="text-2xl font-bold text-blue-400">
                            {{ statistics.enAttente }}
                        </p>
                    </div>
                    <div
                        class="bg-gray-100 dark:bg-zinc-900 p-3 rounded-lg border border-gray-200 dark:border-zinc-700"
                    >
                        <h3
                            class="text-lg font-semibold mb-1 text-gray-800 dark:text-gray-200"
                        >
                            Tickets en cours
                        </h3>
                        <p class="text-2xl font-bold text-yellow-400">
                            {{ statistics.enCours }}
                        </p>
                    </div>
                    <div
                        class="bg-gray-100 dark:bg-zinc-900 p-3 rounded-lg border border-gray-200 dark:border-zinc-700"
                    >
                        <h3
                            class="text-lg font-semibold mb-1 text-gray-800 dark:text-gray-200"
                        >
                            Tickets terminés
                        </h3>
                        <p class="text-2xl font-bold text-green-400">
                            {{ statistics.termines }}
                        </p>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow-xl sm:rounded-lg">
                    <Welcome />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
