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
                <h2
                    class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
                >
                    Tickets
                </h2>
                <Link
                    :href="route('tickets.create')"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition"
                >
                    Nouveau Ticket
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
                                                class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300"
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
                                                class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300"
                                            >
                                                Modifier
                                            </Link>
                                            <button
                                                v-if="
                                                    $page.props.auth.user
                                                        .role === 'admin'
                                                "
                                                @click="deleteTicket(ticket.id)"
                                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
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
