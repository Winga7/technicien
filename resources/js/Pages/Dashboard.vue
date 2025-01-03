<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Welcome from "@/Components/Welcome.vue";

defineProps({
    statistics: {
        type: Object,
        required: true,
    },
    clients: {
        type: Array,
        required: true,
    },
});
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <div class="space-y-4">
                <!-- Toutes les statistiques -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Section Tickets -->
                    <div
                        class="bg-white dark:bg-zinc-800 rounded-lg shadow p-4"
                    >
                        <h2
                            class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3 flex items-center"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 mr-2"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"
                                />
                            </svg>
                            Tickets
                        </h2>
                        <div class="grid grid-cols-2 gap-2">
                            <div
                                class="text-center p-2 bg-gray-50 dark:bg-zinc-900 rounded"
                            >
                                <p
                                    class="text-sm text-gray-600 dark:text-gray-400"
                                >
                                    En attente
                                </p>
                                <p class="text-xl font-bold text-yellow-500">
                                    {{ statistics.tickets.enAttente }}
                                </p>
                            </div>
                            <div
                                class="text-center p-2 bg-gray-50 dark:bg-zinc-900 rounded"
                            >
                                <p
                                    class="text-sm text-gray-600 dark:text-gray-400"
                                >
                                    En cours
                                </p>
                                <p class="text-xl font-bold text-blue-500">
                                    {{ statistics.tickets.enCours }}
                                </p>
                            </div>
                            <div
                                class="text-center p-2 bg-gray-50 dark:bg-zinc-900 rounded"
                            >
                                <p
                                    class="text-sm text-gray-600 dark:text-gray-400"
                                >
                                    Terminés
                                </p>
                                <p class="text-xl font-bold text-green-500">
                                    {{ statistics.tickets.termines }}
                                </p>
                            </div>
                            <div
                                class="text-center p-2 bg-gray-50 dark:bg-zinc-900 rounded"
                            >
                                <p
                                    class="text-sm text-gray-600 dark:text-gray-400"
                                >
                                    Actifs
                                </p>
                                <p class="text-xl font-bold text-cyan-500">
                                    {{ statistics.tickets.actifs }}
                                </p>
                            </div>
                            <div
                                class="text-center p-2 bg-gray-50 dark:bg-zinc-900 rounded"
                            >
                                <p
                                    class="text-sm text-gray-600 dark:text-gray-400"
                                >
                                    Nouveaux ce mois
                                </p>
                                <p class="text-xl font-bold text-emerald-500">
                                    {{ statistics.tickets.nouveauxCeMois }}
                                </p>
                            </div>
                            <div
                                class="text-center p-2 bg-gray-50 dark:bg-zinc-900 rounded"
                            >
                                <p
                                    class="text-sm text-gray-600 dark:text-gray-400"
                                >
                                    Total
                                </p>
                                <p class="text-xl font-bold text-indigo-500">
                                    {{ statistics.tickets.total }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Section Équipe -->
                    <div
                        class="bg-white dark:bg-zinc-800 rounded-lg shadow p-4"
                    >
                        <h2
                            class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3 flex items-center"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 mr-2"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                                />
                            </svg>
                            Équipe
                        </h2>
                        <div class="grid grid-cols-1 gap-2">
                            <div
                                class="text-center p-2 bg-gray-50 dark:bg-zinc-900 rounded"
                            >
                                <p
                                    class="text-sm text-gray-600 dark:text-gray-400"
                                >
                                    Techniciens
                                </p>
                                <p class="text-xl font-bold text-purple-500">
                                    {{ statistics.users.techniciens }}
                                </p>
                            </div>
                            <div
                                class="text-center p-2 bg-gray-50 dark:bg-zinc-900 rounded"
                            >
                                <p
                                    class="text-sm text-gray-600 dark:text-gray-400"
                                >
                                    Administrateurs
                                </p>
                                <p class="text-xl font-bold text-orange-500">
                                    {{ statistics.users.admins }}
                                </p>
                            </div>
                            <div
                                class="text-center p-2 bg-gray-50 dark:bg-zinc-900 rounded"
                            >
                                <p
                                    class="text-sm text-gray-600 dark:text-gray-400"
                                >
                                    Total
                                </p>
                                <p class="text-xl font-bold text-indigo-500">
                                    {{ statistics.users.total }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Section Clients -->
                    <div
                        class="bg-white dark:bg-zinc-800 rounded-lg shadow p-4"
                    >
                        <h2
                            class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-3 flex items-center"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 mr-2"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                                />
                            </svg>
                            Clients
                        </h2>
                        <div class="grid grid-cols-1 gap-2">
                            <div
                                class="text-center p-2 bg-gray-50 dark:bg-zinc-900 rounded"
                            >
                                <p
                                    class="text-sm text-gray-600 dark:text-gray-400"
                                >
                                    Nouveaux ce mois
                                </p>
                                <p class="text-xl font-bold text-emerald-500">
                                    {{ statistics.clients.nouveauxCeMois }}
                                </p>
                            </div>
                            <div
                                class="text-center p-2 bg-gray-50 dark:bg-zinc-900 rounded"
                            >
                                <p
                                    class="text-sm text-gray-600 dark:text-gray-400"
                                >
                                    Actifs
                                </p>
                                <p class="text-xl font-bold text-cyan-500">
                                    {{ statistics.clients.actifs }}
                                </p>
                            </div>
                            <div
                                class="text-center p-2 bg-gray-50 dark:bg-zinc-900 rounded"
                            >
                                <p
                                    class="text-sm text-gray-600 dark:text-gray-400"
                                >
                                    Total
                                </p>
                                <p class="text-xl font-bold text-indigo-500">
                                    {{ statistics.clients.total }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow-xl sm:rounded-lg">
                    <Welcome :clients="clients" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>
