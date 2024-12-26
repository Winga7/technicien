<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, router } from "@inertiajs/vue3"; // Ajout de router ici
import { ref } from "vue";

const props = defineProps({
    tickets: {
        type: Array,
        required: true,
    },
});

const deleteTicket = (id) => {
    if (confirm("Êtes-vous sûr de vouloir supprimer ce ticket ?")) {
        router.delete(route("tickets.destroy", id), {
            preserveScroll: true,
            onSuccess: () => {
                // Optionnel : Vous pouvez ajouter un traitement après la suppression réussie
                console.log("Ticket supprimé avec succès");
            },
            onError: () => {
                // Optionnel : Gestion des erreurs
                console.error("Erreur lors de la suppression du ticket");
            },
        });
    }
};

const getStatusColor = (statut) => {
    switch (statut) {
        case "en attente":
            return "text-blue-500";
        case "en cours":
            return "text-yellow-500";
        case "terminé":
            return "text-green-500";
        default:
            return "text-gray-500";
    }
};
</script>

<template>
    <AppLayout title="Tickets">
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <span class="text-2xl" role="img" aria-label="tickets">🎫</span>
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        Gestion des Tickets
                    </h2>
                </div>
                <Link
                    :href="route('tickets.create')"
                    class="px-4 py-2 bg-green-600 dark:bg-green-500 text-white rounded-md hover:bg-green-700 dark:hover:bg-green-600 transition"
                >
                    <span class="flex items-center space-x-2">
                        <span class="text-lg" role="img" aria-label="nouveau">➕</span>
                        <span>Nouveau Ticket</span>
                    </span>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white dark:bg-zinc-800 overflow-hidden shadow-xl sm:rounded-lg p-6"
                >
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr
                                    class="text-left border-b dark:border-zinc-700"
                                >
                                    <th
                                        class="px-4 py-3 text-gray-700 dark:text-gray-300"
                                    >
                                        ID
                                    </th>
                                    <th
                                        class="px-4 py-3 text-gray-700 dark:text-gray-300"
                                    >
                                        Titre
                                    </th>
                                    <th
                                        class="px-4 py-3 text-gray-700 dark:text-gray-300"
                                    >
                                        Client
                                    </th>
                                    <th
                                        class="px-4 py-3 text-gray-700 dark:text-gray-300"
                                    >
                                        Statut
                                    </th>
                                    <th
                                        class="px-4 py-3 text-gray-700 dark:text-gray-300"
                                    >
                                        Date
                                    </th>
                                    <th
                                        class="px-4 py-3 text-gray-700 dark:text-gray-300"
                                    >
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="ticket in tickets"
                                    :key="ticket.id"
                                    class="border-b dark:border-zinc-700 hover:bg-gray-50 dark:hover:bg-zinc-700"
                                >
                                    <td
                                        class="px-4 py-3 text-gray-800 dark:text-gray-200"
                                    >
                                        #{{ ticket.id }}
                                    </td>
                                    <td
                                        class="px-4 py-3 text-gray-800 dark:text-gray-200"
                                    >
                                        {{ ticket.titre }}
                                    </td>
                                    <td
                                        class="px-4 py-3 text-gray-800 dark:text-gray-200"
                                    >
                                        {{ ticket.client.name }}
                                        {{ ticket.client.prenom }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <span
                                            :class="
                                                getStatusColor(ticket.statut)
                                            "
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        >
                                            {{ ticket.statut }}
                                        </span>
                                    </td>
                                    <td
                                        class="px-4 py-3 text-gray-800 dark:text-gray-200"
                                    >
                                        {{
                                            new Date(
                                                ticket.created_at
                                            ).toLocaleDateString()
                                        }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div
                                            class="flex items-center space-x-2"
                                        >
                                            <Link
                                                :href="
                                                    route(
                                                        'tickets.show',
                                                        ticket.id
                                                    )
                                                "
                                                class="px-3 py-1.5 bg-indigo-600 dark:bg-indigo-500 text-white rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-600 transition text-sm font-medium"
                                            >
                                                Voir
                                            </Link>
                                            <Link
                                                :href="
                                                    route(
                                                        'tickets.edit',
                                                        ticket.id
                                                    )
                                                "
                                                class="px-3 py-1.5 bg-yellow-600 dark:bg-yellow-500 text-white rounded-md hover:bg-yellow-700 dark:hover:bg-yellow-600 transition text-sm font-medium"
                                            >
                                                Modifier
                                            </Link>
                                            <button
                                                v-if="
                                                    $page.props.auth.user
                                                        .role === 'admin'
                                                "
                                                @click="deleteTicket(ticket.id)"
                                                class="px-3 py-1.5 bg-red-600 dark:bg-red-500 text-white rounded-md hover:bg-red-700 dark:hover:bg-red-600 transition text-sm font-medium"
                                            >
                                                Supprimer
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
