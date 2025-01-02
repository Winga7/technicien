<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";

const props = defineProps({
    tickets: {
        type: Array,
        required: true,
    },
    auth: {
        type: Object,
        required: true,
    },
});

const search = ref('');

const deleteTicket = (id) => {
    if (confirm("Êtes-vous sûr de vouloir supprimer ce ticket ?")) {
        router.delete(route("tickets.destroy", id), {
            preserveScroll: true,
            onSuccess: () => {
                console.log("Ticket supprimé avec succès");
            },
            onError: () => {
                console.error("Erreur lors de la suppression du ticket");
            },
        });
    }
};

const filteredTickets = computed(() => {
    if (!search.value) return props.tickets;
    const searchLower = search.value.toLowerCase();
    const searchPhone = search.value.replace(/\s/g, '');
    return props.tickets.filter(ticket =>
        ticket.titre.toLowerCase().includes(searchLower) ||
        ticket.client?.name.toLowerCase().includes(searchLower) ||
        ticket.client?.telephone?.replace(/\s/g, '').includes(searchPhone) ||
        ticket.statut.toLowerCase().includes(searchLower)
    );
});

const getStatusColor = (statut) => {
    switch (statut.toLowerCase()) {
        case "en attente":
            return "text-yellow-600 dark:text-yellow-400";
        case "en cours":
            return "text-blue-600 dark:text-blue-400";
        case "terminé":
            return "text-green-600 dark:text-green-400";
        default:
            return "text-gray-600 dark:text-gray-400";
    }
};

const userRole = computed(() => {
    return props.auth?.user?.role;
});

const canManageTickets = computed(() => {
    return userRole.value === 'admin' || userRole.value === 'technicien';
});

const canDeleteTickets = computed(() => {
    return userRole.value === 'admin';
});
</script>

<template>
    <AppLayout title="Tickets">
        <template #header>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                <div class="flex items-center space-x-3">
                    <span class="text-2xl" role="img" aria-label="tickets">🎫</span>
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        Gestion des Tickets
                    </h2>
                </div>
                <Link
                    v-if="canManageTickets"
                    :href="route('tickets.create')"
                    class="w-full sm:w-auto px-4 py-2 bg-green-600 dark:bg-green-500 text-white rounded-md hover:bg-green-700 dark:hover:bg-green-600 transition text-center"
                >
                    <span class="flex items-center justify-center space-x-2">
                        <span class="text-lg" role="img" aria-label="nouveau">🎫</span>
                        <span>Nouveau Ticket</span>
                    </span>
                </Link>
            </div>
        </template>

        <div class="py-6 sm:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center">
                            <span class="text-gray-500 dark:text-gray-400" role="img" aria-label="recherche">🔍</span>
                        </span>
                        <input
                            type="text"
                            v-model="search"
                            placeholder="Rechercher un ticket..."
                            class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400"
                        >
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow-xl sm:rounded-lg p-4 sm:p-6">
                    <!-- Table responsive -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                            <thead>
                                <tr class="text-left">
                                    <th class="p-3 sm:p-4 text-xs sm:text-sm text-gray-700 dark:text-gray-300">Titre</th>
                                    <th class="hidden sm:table-cell p-3 sm:p-4 text-xs sm:text-sm text-gray-700 dark:text-gray-300">Client</th>
                                    <th class="hidden md:table-cell p-3 sm:p-4 text-xs sm:text-sm text-gray-700 dark:text-gray-300">Statut</th>
                                    <th class="p-3 sm:p-4 text-xs sm:text-sm text-gray-700 dark:text-gray-300">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                                <tr v-for="ticket in filteredTickets" :key="ticket.id" class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                                    <td class="p-3 sm:p-4 text-xs sm:text-sm text-gray-900 dark:text-gray-100 text-justify">
                                        {{ ticket.titre }}
                                        <!-- Info mobile pour client et statut -->
                                        <div class="sm:hidden mt-1 text-xs text-gray-500 dark:text-gray-400 text-left">
                                            {{ ticket.client?.name }} -
                                            <span :class="getStatusColor(ticket.statut)">{{ ticket.statut }}</span>
                                        </div>
                                    </td>
                                    <td class="hidden sm:table-cell p-3 sm:p-4 text-xs sm:text-sm text-gray-900 dark:text-gray-100 text-left">
                                        {{ ticket.client?.name }}
                                    </td>
                                    <td class="hidden md:table-cell p-3 sm:p-4 text-xs sm:text-sm text-left">
                                        <span :class="getStatusColor(ticket.statut)">
                                            {{ ticket.statut }}
                                        </span>
                                    </td>
                                    <td class="p-3 sm:p-4 text-xs sm:text-sm text-left">
                                        <!-- Version Desktop -->
                                        <div class="hidden sm:flex space-x-2 justify">
                                            <Link
                                                :href="route('tickets.show', ticket.id)"
                                                class="px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-md hover:bg-blue-200 dark:hover:bg-blue-800 transition"
                                            >
                                                <span class="flex items-center space-x-1">
                                                    <span class="text-lg" role="img" aria-label="voir">👁️</span>
                                                    <span>Voir</span>
                                                </span>
                                            </Link>
                                            <Link
                                                v-if="canManageTickets"
                                                :href="route('tickets.edit', ticket.id)"
                                                class="px-3 py-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-400 rounded-md hover:bg-yellow-200 dark:hover:bg-yellow-800 transition"
                                            >
                                                <span class="flex items-center space-x-1">
                                                    <span class="text-lg" role="img" aria-label="modifier">✏️</span>
                                                    <span>Modifier</span>
                                                </span>
                                            </Link>
                                            <button
                                                v-if="canDeleteTickets"
                                                @click="deleteTicket(ticket.id)"
                                                class="px-3 py-1 bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-400 rounded-md hover:bg-red-200 dark:hover:bg-red-800 transition"
                                            >
                                                <span class="flex items-center space-x-1">
                                                    <span class="text-lg" role="img" aria-label="supprimer">🗑️</span>
                                                    <span>Supprimer</span>
                                                </span>
                                            </button>
                                        </div>

                                        <!-- Version Mobile -->
                                        <div class="flex sm:hidden space-x-2 justify-center">
                                            <Link
                                                :href="route('tickets.show', ticket.id)"
                                                class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300"
                                            >
                                                <span class="text-lg" role="img" aria-label="voir">👁️</span>
                                            </Link>
                                            <Link
                                                v-if="canManageTickets"
                                                :href="route('tickets.edit', ticket.id)"
                                                class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-800 dark:hover:text-yellow-300"
                                            >
                                                <span class="text-lg" role="img" aria-label="modifier">✏️</span>
                                            </Link>
                                            <button
                                                v-if="canDeleteTickets"
                                                @click="deleteTicket(ticket.id)"
                                                class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300"
                                            >
                                                <span class="text-lg" role="img" aria-label="supprimer">🗑️</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
