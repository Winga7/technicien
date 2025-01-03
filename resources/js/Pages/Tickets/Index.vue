<script setup>
import { Link, router, useForm } from "@inertiajs/vue3"; // Importer router dans le même import que Link
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, computed, watch } from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";

const props = defineProps({
    tickets: {
        type: Array,
        required: true,
    },
    clients: {
        type: Array,
        required: true,
    },
    auth: {
        type: Object,
        required: true,
    },
});

console.log('Clients reçus:', props.clients); // Ajoutez cette ligne temporairement

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
                router.visit(route("tickets.index"));
            },
            onError: (error) => {
                console.error("Erreur lors de la suppression du ticket:", error);
                alert("Une erreur est survenue lors de la suppression du ticket");
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

const showTicketForm = ref(false);

const isNewClient = ref(false);
const imagePreview = ref([]);

const form = useForm({
    titre: '',
    description: '',
    client_id: '',
    statut: 'en attente',
    images: [],
    client: null,
});

const submitTicket = () => {
    form.post(route('tickets.store'), {
        onSuccess: () => {
            showTicketForm.value = false;
            form.reset();
        },
    });
};

const resetTicketForm = () => {
    form.reset();
    isNewClient.value = false;
    imagePreview.value = [];
    showTicketForm.value = false;
};

const handleImageUpload = (e) => {
    const files = Array.from(e.target.files);
    form.images = [];
    imagePreview.value = [];

    files.forEach((file) => {
        form.images.push(file);
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value.push(e.target.result);
        };
        reader.readAsDataURL(file);
    });
};

const removeImage = (index) => {
    imagePreview.value.splice(index, 1);
    form.images.splice(index, 1);
};

watch(isNewClient, (newValue) => {
    if (newValue) {
        form.client = {
            name: "",
            prenom: "",
            email: "",
            telephone: "",
            addresse: "",
        };
        form.client_id = "";
    } else {
        form.client = null;
    }
});

const showEditForm = ref(false);
const editForm = useForm({
    id: '',
    titre: '',
    description: '',
    statut: '',
});

const editTicket = (ticket) => {
    editForm.id = ticket.id;
    editForm.titre = ticket.titre;
    editForm.description = ticket.description;
    editForm.statut = ticket.statut;
    showEditForm.value = true;
};

const submitEdit = () => {
    editForm.put(route('tickets.update', editForm.id), {
        onSuccess: () => {
            showEditForm.value = false;
            editForm.reset();
        },
    });
};

const resetEditForm = () => {
    showEditForm.value = false;
    editForm.reset();
};

// Ajouter un watcher pour réinitialiser le formulaire quand le modal se ferme
watch(showTicketForm, (newValue) => {
    if (!newValue) {
        resetTicketForm();
    }
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
                <button
                    v-if="canManageTickets"
                    @click="showTicketForm = true"
                    class="w-full sm:w-auto px-4 py-2 bg-green-600 dark:bg-green-500 text-white rounded-md hover:bg-green-700 dark:hover:bg-green-600 transition text-center"
                >
                    <span class="flex items-center justify-center space-x-2">
                        <span class="text-lg" role="img" aria-label="nouveau"
                            >🎫</span
                        >
                        <span>Nouveau Ticket</span>
                    </span>
                </button>
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
                                            <button
                                                v-if="canManageTickets"
                                                @click="editTicket(ticket)"
                                                class="px-3 py-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-400 rounded-md hover:bg-yellow-200 dark:hover:bg-yellow-800 transition"
                                            >
                                                <span class="flex items-center space-x-1">
                                                    <span class="text-lg" role="img" aria-label="modifier">✏️</span>
                                                    <span>Modifier</span>
                                                </span>
                                            </button>
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
                                            <button
                                                v-if="canManageTickets"
                                                @click="editTicket(ticket)"
                                                class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-800 dark:hover:text-yellow-300"
                                            >
                                                <span
                                                    class="text-lg"
                                                    role="img"
                                                    aria-label="modifier"
                                                    >✏️</span
                                                >
                                            </button>
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

    <div
        v-if="showTicketForm"
        class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4 overflow-y-auto"
        @click.self="resetTicketForm"
    >
        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 w-full max-w-2xl my-8 relative">
            <!-- En-tête du modal -->
            <div class="flex justify-between items-center mb-4 sticky top-0 bg-white dark:bg-zinc-800 z-10">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Nouveau Ticket
                </h3>
                <button
                    @click="resetTicketForm"
                    class="text-gray-400 hover:text-gray-500 text-xl font-medium px-2 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded"
                >
                    ×
                </button>
            </div>

            <!-- Contenu scrollable -->
            <div class="max-h-[calc(100vh-8rem)] overflow-y-auto">
                <form @submit.prevent="submitTicket" class="space-y-4">
                    <!-- Type de client -->
                    <div>
                        <InputLabel class="dark:text-gray-200" value="Type de client" />
                        <div class="flex space-x-4 mt-2">
                            <label class="inline-flex items-center">
                                <input
                                    type="radio"
                                    v-model="isNewClient"
                                    :value="false"
                                    class="form-radio border-gray-200 dark:border-zinc-700 text-indigo-600 dark:bg-zinc-900 focus:ring-indigo-500"
                                />
                                <span class="ml-2 text-gray-700 dark:text-gray-200">Client existant</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input
                                    type="radio"
                                    v-model="isNewClient"
                                    :value="true"
                                    class="form-radio border-gray-200 dark:border-zinc-700 text-indigo-600 dark:bg-zinc-900 focus:ring-indigo-500"
                                />
                                <span class="ml-2 text-gray-700 dark:text-gray-200">Nouveau client</span>
                            </label>
                        </div>
                    </div>

                    <!-- Sélection du client existant -->
                    <div v-if="!isNewClient">
                        <select
                            v-model="form.client_id"
                            class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        >
                            <option value="">Sélectionner un client</option>
                            <option v-for="client in clients" :key="client.id" :value="client.id">
                                {{ client.name }} {{ client.prenom }}
                            </option>
                        </select>
                    </div>

                    <!-- Formulaire nouveau client -->
                    <div v-else class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Nom et prénom -->
                            <div>
                                <InputLabel class="dark:text-gray-200" value="Nom" />
                                <TextInput
                                    v-model="form.client.name"
                                    type="text"
                                    class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    required
                                />
                            </div>
                            <div>
                                <InputLabel class="dark:text-gray-200" value="Prénom" />
                                <TextInput
                                    v-model="form.client.prenom"
                                    type="text"
                                    class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    required
                                />
                            </div>
                            <!-- Email et téléphone -->
                            <div>
                                <InputLabel class="dark:text-gray-200" value="Email" />
                                <TextInput
                                    v-model="form.client.email"
                                    type="email"
                                    class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    required
                                />
                            </div>
                            <div>
                                <InputLabel class="dark:text-gray-200" value="Téléphone" />
                                <TextInput
                                    v-model="form.client.telephone"
                                    type="tel"
                                    class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                />
                            </div>
                        </div>
                        <!-- Adresse -->
                        <div>
                            <InputLabel class="dark:text-gray-200" value="Adresse" />
                            <textarea
                                v-model="form.client.addresse"
                                class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                rows="3"
                            ></textarea>
                        </div>
                    </div>

                    <!-- Détails du ticket -->
                    <div>
                        <InputLabel class="dark:text-gray-200" value="Titre du ticket" />
                        <TextInput
                            v-model="form.titre"
                            type="text"
                            class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            required
                        />
                    </div>

                    <div>
                        <InputLabel class="dark:text-gray-200" value="Description" />
                        <textarea
                            v-model="form.description"
                            class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            rows="4"
                            required
                        ></textarea>
                    </div>

                    <!-- Images -->
                    <div>
                        <InputLabel class="dark:text-gray-200" value="Images" />
                        <input
                            type="file"
                            @change="handleImageUpload"
                            multiple
                            accept="image/*"
                            class="mt-1 block w-full text-gray-700 dark:text-gray-200"
                        />
                        <!-- Prévisualisation des images -->
                        <div v-if="imagePreview.length" class="mt-2 grid grid-cols-3 gap-4">
                            <div v-for="(preview, index) in imagePreview" :key="index" class="relative">
                                <img :src="preview" class="w-full h-32 object-cover rounded-lg" />
                                <button
                                    @click.prevent="removeImage(index)"
                                    class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center"
                                >
                                    ×
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button
                            type="button"
                            @click="resetTicketForm"
                            class="px-4 py-2 bg-gray-300 dark:bg-zinc-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-400 dark:hover:bg-zinc-500 transition"
                        >
                            Annuler
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-indigo-600 dark:bg-indigo-500 text-white rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-600 transition"
                        >
                            Créer le ticket
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal d'édition de ticket -->
    <div
        v-if="showEditForm"
        class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
        @click.self="resetEditForm"
    >
        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 max-w-2xl w-full mx-4 relative">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Modifier le Ticket
                </h3>
                <button
                    @click="resetEditForm"
                    class="text-gray-400 hover:text-gray-500 text-xl font-medium px-2 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded"
                >
                    ×
                </button>
            </div>

            <form @submit.prevent="submitEdit" class="space-y-4">
                <div>
                    <InputLabel class="dark:text-gray-200" value="Titre du ticket" />
                    <TextInput
                        v-model="editForm.titre"
                        type="text"
                        class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        required
                    />
                </div>

                <div>
                    <InputLabel class="dark:text-gray-200" value="Description" />
                    <textarea
                        v-model="editForm.description"
                        class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        rows="4"
                        required
                    ></textarea>
                </div>

                <div>
                    <InputLabel class="dark:text-gray-200" value="Statut" />
                    <select
                        v-model="editForm.statut"
                        class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                    >
                        <option value="en attente">En attente</option>
                        <option value="en cours">En cours</option>
                        <option value="terminé">Terminé</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-3">
                    <button
                        type="button"
                        @click="resetEditForm"
                        class="px-4 py-2 bg-gray-300 dark:bg-zinc-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-400 dark:hover:bg-zinc-500 transition"
                    >
                        Annuler
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-indigo-600 dark:bg-indigo-500 text-white rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-600 transition"
                    >
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
