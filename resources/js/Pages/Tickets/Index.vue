<script setup>
import { Link, router } from "@inertiajs/vue3"; // Importer router dans le même import que Link
import AppLayout from "@/Layouts/AppLayout.vue";
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

const search = ref("");

// Ajouter l'état pour le tri
const sort = ref({
    column: "date",
    direction: "asc",
});

// Ajouter la fonction de tri
const toggleSort = (column) => {
    if (sort.value.column === column) {
        sort.value.direction = sort.value.direction === "asc" ? "desc" : "asc";
    } else {
        sort.value.column = column;
        sort.value.direction = "asc";
    }
};

const deleteTicket = (id) => {
    if (confirm("Êtes-vous sûr de vouloir supprimer ce ticket ?")) {
        router.delete(route("tickets.destroy", id), {
            preserveScroll: true,
            onSuccess: () => {
                // Au lieu de recharger la page, laissez Inertia gérer la redirection
                router.visit(route("tickets.index"));
            },
            onError: (error) => {
                console.error(
                    "Erreur lors de la suppression du ticket:",
                    error
                );
                alert(
                    "Une erreur est survenue lors de la suppression du ticket"
                );
            },
        });
    }
};

// Modifier le computed filteredTickets
const filteredTickets = computed(() => {
    let tickets = props.tickets;

    // Filtrage par recherche
    if (search.value) {
        const searchLower = search.value.toLowerCase();
        const searchPhone = search.value.replace(/\s/g, "");
        tickets = tickets.filter(
            (ticket) =>
                ticket.titre.toLowerCase().includes(searchLower) ||
                ticket.client?.name.toLowerCase().includes(searchLower) ||
                ticket.statut.toLowerCase().includes(searchLower)
        );
    }

    // Tri
    return tickets.sort((a, b) => {
        const modifier = sort.value.direction === "asc" ? 1 : -1;
        let aValue, bValue;

        if (sort.value.column === "client") {
            aValue = (a.client?.name || "").toLowerCase();
            bValue = (b.client?.name || "").toLowerCase();
        } else {
            aValue = (a[sort.value.column] || "").toLowerCase();
            bValue = (b[sort.value.column] || "").toLowerCase();
        }

        return aValue > bValue ? modifier : -modifier;
    });
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
    return userRole.value === "admin" || userRole.value === "technicien";
});

const canDeleteTickets = computed(() => {
    return userRole.value === "admin";
});
</script>

<template>
    <AppLayout title="Tickets">
        <template #header>
            <div
                class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0"
            >
                <div class="flex items-center space-x-3">
                    <span class="text-2xl" role="img" aria-label="tickets"
                        >🎫</span
                    >
                    <h2
                        class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
                    >
                        Gestion des Tickets
                    </h2>
                </div>
                <Link
                    v-if="canManageTickets"
                    :href="route('tickets.create')"
                    class="w-full sm:w-auto px-4 py-2 bg-green-600 dark:bg-green-500 text-white rounded-md hover:bg-green-700 dark:hover:bg-green-600 transition text-center"
                >
                    <span class="flex items-center justify-center space-x-2">
                        <span class="text-lg" role="img" aria-label="nouveau"
                            >🎫</span
                        >
                        <span>Nouveau Ticket</span>
                    </span>
                </Link>
            </div>
        </template>

        <div class="py-6 sm:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="mb-6">
                    <div class="relative">
                        <span
                            class="absolute inset-y-0 left-0 pl-3 flex items-center"
                        >
                            <span
                                class="text-gray-500 dark:text-gray-400"
                                role="img"
                                aria-label="recherche"
                                >🔍</span
                            >
                        </span>
                        <input
                            type="text"
                            v-model="search"
                            placeholder="Rechercher un ticket..."
                            class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400"
                        />
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-zinc-800 overflow-hidden shadow-xl sm:rounded-lg"
                >
                    <!-- Table responsive -->
                    <div class="overflow-x-auto">
                        <table
                            class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700"
                        >
                            <thead>
                                <tr class="text-left">
                                    <th
                                        @click="toggleSort('titre')"
                                        class="p-3 sm:p-4 text-xs sm:text-sm text-gray-700 dark:text-gray-300 cursor-pointer hover:bg-gray-50 dark:hover:bg-zinc-700"
                                    >
                                        <div
                                            class="flex items-center space-x-1"
                                        >
                                            <span>Titre</span>
                                            <span
                                                v-if="sort.column === 'titre'"
                                                class="text-xs"
                                            >
                                                {{
                                                    sort.direction === "asc"
                                                        ? "▲"
                                                        : "▼"
                                                }}
                                            </span>
                                        </div>
                                    </th>
                                    <th
                                        @click="toggleSort('client')"
                                        class="hidden sm:table-cell p-3 sm:p-4 text-xs sm:text-sm text-gray-700 dark:text-gray-300 cursor-pointer hover:bg-gray-50 dark:hover:bg-zinc-700"
                                    >
                                        <div
                                            class="flex items-center space-x-1"
                                        >
                                            <span>Client</span>
                                            <span
                                                v-if="sort.column === 'client'"
                                                class="text-xs"
                                            >
                                                {{
                                                    sort.direction === "asc"
                                                        ? "▲"
                                                        : "▼"
                                                }}
                                            </span>
                                        </div>
                                    </th>
                                    <th
                                        @click="toggleSort('statut')"
                                        class="hidden md:table-cell p-3 sm:p-4 text-xs sm:text-sm text-gray-700 dark:text-gray-300 cursor-pointer hover:bg-gray-50 dark:hover:bg-zinc-700"
                                    >
                                        <div
                                            class="flex items-center space-x-1"
                                        >
                                            <span>Statut</span>
                                            <span
                                                v-if="sort.column === 'statut'"
                                                class="text-xs"
                                            >
                                                {{
                                                    sort.direction === "asc"
                                                        ? "▲"
                                                        : "▼"
                                                }}
                                            </span>
                                        </div>
                                    </th>
                                    <th
                                        class="p-3 sm:p-4 text-xs sm:text-sm text-gray-700 dark:text-gray-300"
                                    >
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody
                                class="divide-y divide-gray-200 dark:divide-zinc-700"
                            >
                                <tr
                                    v-for="ticket in filteredTickets"
                                    :key="ticket.id"
                                    class="hover:bg-gray-50 dark:hover:bg-zinc-700"
                                >
                                    <td
                                        class="p-3 sm:p-4 text-xs sm:text-sm text-gray-900 dark:text-gray-100 text-justify"
                                    >
                                        {{ ticket.titre }}
                                        <!-- Info mobile pour client et statut -->
                                        <div
                                            class="sm:hidden mt-1 text-xs text-gray-500 dark:text-gray-400 text-left"
                                        >
                                            {{ ticket.client?.name }} -
                                            <span
                                                :class="
                                                    getStatusColor(
                                                        ticket.statut
                                                    )
                                                "
                                                >{{ ticket.statut }}</span
                                            >
                                        </div>
                                    </td>
                                    <td
                                        class="hidden sm:table-cell p-3 sm:p-4 text-xs sm:text-sm text-gray-900 dark:text-gray-100 text-left"
                                    >
                                        {{ ticket.client?.name }}
                                    </td>
                                    <td
                                        class="hidden md:table-cell p-3 sm:p-4 text-xs sm:text-sm text-left"
                                    >
                                        <span
                                            :class="
                                                getStatusColor(ticket.statut)
                                            "
                                        >
                                            {{ ticket.statut }}
                                        </span>
                                    </td>
                                    <td
                                        class="p-3 sm:p-4 text-xs sm:text-sm text-left"
                                    >
                                        <!-- Version Desktop -->
                                        <div
                                            class="hidden sm:flex space-x-2 justify"
                                        >
                                            <Link
                                                :href="
                                                    route(
                                                        'tickets.show',
                                                        ticket.id
                                                    )
                                                "
                                                class="px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-md hover:bg-blue-200 dark:hover:bg-blue-800 transition"
                                            >
                                                <span
                                                    class="flex items-center space-x-1"
                                                >
                                                    <span
                                                        class="text-lg"
                                                        role="img"
                                                        aria-label="voir"
                                                        >👁️</span
                                                    >
                                                    <span>Voir</span>
                                                </span>
                                            </Link>
                                            <Link
                                                v-if="canManageTickets"
                                                :href="
                                                    route(
                                                        'tickets.edit',
                                                        ticket.id
                                                    )
                                                "
                                                class="px-3 py-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-400 rounded-md hover:bg-yellow-200 dark:hover:bg-yellow-800 transition"
                                            >
                                                <span
                                                    class="flex items-center space-x-1"
                                                >
                                                    <span
                                                        class="text-lg"
                                                        role="img"
                                                        aria-label="modifier"
                                                        >✏️</span
                                                    >
                                                    <span>Modifier</span>
                                                </span>
                                            </Link>
                                            <button
                                                v-if="canDeleteTickets"
                                                @click="deleteTicket(ticket.id)"
                                                class="px-3 py-1 bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-400 rounded-md hover:bg-red-200 dark:hover:bg-red-800 transition"
                                            >
                                                <span
                                                    class="flex items-center space-x-1"
                                                >
                                                    <span
                                                        class="text-lg"
                                                        role="img"
                                                        aria-label="supprimer"
                                                        >🗑️</span
                                                    >
                                                    <span>Supprimer</span>
                                                </span>
                                            </button>
                                        </div>

                                        <!-- Version Mobile -->
                                        <div
                                            class="flex sm:hidden space-x-2 justify-center"
                                        >
                                            <Link
                                                :href="
                                                    route(
                                                        'tickets.show',
                                                        ticket.id
                                                    )
                                                "
                                                class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300"
                                            >
                                                <span
                                                    class="text-lg"
                                                    role="img"
                                                    aria-label="voir"
                                                    >👁️</span
                                                >
                                            </Link>
                                            <Link
                                                v-if="canManageTickets"
                                                :href="
                                                    route(
                                                        'tickets.edit',
                                                        ticket.id
                                                    )
                                                "
                                                class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-800 dark:hover:text-yellow-300"
                                            >
                                                <span
                                                    class="text-lg"
                                                    role="img"
                                                    aria-label="modifier"
                                                    >✏️</span
                                                >
                                            </Link>
                                            <button
                                                v-if="canDeleteTickets"
                                                @click="deleteTicket(ticket.id)"
                                                class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300"
                                            >
                                                <span
                                                    class="text-lg"
                                                    role="img"
                                                    aria-label="supprimer"
                                                    >🗑️</span
                                                >
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
