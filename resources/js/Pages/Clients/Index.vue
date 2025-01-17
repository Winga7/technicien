<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";

const props = defineProps({
    clients: Array,
});

const search = ref("");
const showClientForm = ref(false);

const sort = ref({
    column: "name",
    direction: "asc",
});

const toggleSort = (column) => {
    if (sort.value.column === column) {
        sort.value.direction = sort.value.direction === "asc" ? "desc" : "asc";
    } else {
        sort.value.column = column;
        sort.value.direction = "asc";
    }
};

const clientForm = useForm({
    name: "",
    firstname: "",
    email: "",
    telephone: "",
    addresse: "",
});

const filteredClients = computed(() => {
    let clients = props.clients;

    if (search.value) {
        const searchLower = search.value.toLowerCase();
        const searchPhone = search.value.replace(/\s/g, "");
        clients = clients.filter(
            (client) =>
                client.name.toLowerCase().includes(searchLower) ||
                client.firstname.toLowerCase().includes(searchLower) ||
                client.email.toLowerCase().includes(searchLower) ||
                client.telephone.replace(/\s/g, "").includes(searchPhone)
        );
    }

    return clients.sort((a, b) => {
        const modifier = sort.value.direction === "asc" ? 1 : -1;
        const aValue = a[sort.value.column].toLowerCase();
        const bValue = b[sort.value.column].toLowerCase();
        return aValue > bValue ? modifier : -modifier;
    });
});

const isValidName = (name) => {
    return /^[a-zA-ZÀ-ÿ\s'-]{2,50}$/.test(name);
};

const isValidPhone = (phone) => {
    // Format belge: +32 XXX XX XX XX ou 0X XXX XX XX
    return /^(?:\+32|0)(?:\s?\d{1,2}\s?\d{2,3}\s?\d{2}\s?\d{2})$/.test(phone);
};

const submitClient = () => {
    clientForm.clearErrors();

    // Validation du nom
    if (!clientForm.name?.trim()) {
        clientForm.setError('name', 'Le nom est obligatoire');
        return;
    }
    if (!isValidName(clientForm.name)) {
        clientForm.setError('name', 'Le nom doit contenir entre 2 et 50 caractères et ne peut contenir que des lettres, espaces, tirets et apostrophes');
        return;
    }

    // Validation du prénom
    if (!clientForm.firstname?.trim()) {
        clientForm.setError('firstname', 'Le prénom est obligatoire');
        return;
    }
    if (!isValidName(clientForm.firstname)) {
        clientForm.setError('firstname', 'Le prénom doit contenir entre 2 et 50 caractères et ne peut contenir que des lettres, espaces, tirets et apostrophes');
        return;
    }

    // Validation de l'email
    if (!clientForm.email?.trim()) {
        clientForm.setError('email', 'L\'email est obligatoire');
        return;
    }
    if (!isValidEmail(clientForm.email)) {
        clientForm.setError('email', 'L\'email n\'est pas valide');
        return;
    }

    // Validation du téléphone
    if (clientForm.telephone && !isValidPhone(clientForm.telephone)) {
        clientForm.setError('telephone', 'Le numéro de téléphone doit être au format belge (ex: +32 470 12 34 56 ou 0470 12 34 56)');
        return;
    }

    clientForm.post(route("clients.store"), {
        preserveScroll: true,
        onSuccess: () => {
            showClientForm.value = false;
            clientForm.reset();
        },
        onError: (errors) => {
            Object.keys(errors).forEach(key => {
                clientForm.setError(key, errors[key]);
            });
        }
    });
};

const isValidEmail = (email) => {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
};

const resetClientForm = () => {
    showClientForm.value = false;
    clientForm.reset();
};

const deleteClient = (id) => {
    if (confirm("Êtes-vous sûr de vouloir supprimer ce client ?")) {
        router.delete(route("clients.destroy", id), {
            preserveScroll: true,
            onSuccess: () => {
                // La page sera automatiquement mise à jour
            },
            onError: (error) => {
                console.error(
                    "Erreur lors de la suppression du client:",
                    error
                );
                alert(
                    "Une erreur est survenue lors de la suppression du client"
                );
            },
        });
    }
};
</script>

<template>
    <AppLayout title="Clients">
        <template #header>
            <div
                class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0"
            >
                <div class="flex items-center space-x-3">
                    <span class="text-2xl" role="img" aria-label="clients"
                        >👥</span
                    >
                    <h2
                        class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
                    >
                        Gestion des Clients
                    </h2>
                </div>
                <button
                    @click="showClientForm = true"
                    class="w-full sm:w-auto px-4 py-2 bg-green-600 dark:bg-green-500 text-white rounded-md hover:bg-green-700 dark:hover:bg-green-600 transition text-center"
                >
                    <span class="flex items-center justify-center space-x-2">
                        <span class="text-lg" role="img" aria-label="nouveau"
                            >👥</span
                        >
                        <span>Nouveau Client</span>
                    </span>
                </button>
            </div>
        </template>

        <div class="py-6 sm:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Barre de recherche -->
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
                            placeholder="Rechercher un client..."
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
                                        @click="toggleSort('name')"
                                        class="p-3 sm:p-4 text-xs sm:text-sm text-gray-700 dark:text-gray-300 cursor-pointer hover:bg-gray-50 dark:hover:bg-zinc-700"
                                    >
                                        <div
                                            class="flex items-center space-x-1"
                                        >
                                            <span>Nom</span>
                                            <span
                                                v-if="sort.column === 'name'"
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
                                        @click="toggleSort('firstname')"
                                        class="p-3 sm:p-4 text-xs sm:text-sm text-gray-700 dark:text-gray-300 cursor-pointer hover:bg-gray-50 dark:hover:bg-zinc-700"
                                    >
                                        <div
                                            class="flex items-center space-x-1"
                                        >
                                            <span>Prénom</span>
                                            <span
                                                v-if="
                                                    sort.column === 'firstname'
                                                "
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
                                        @click="toggleSort('email')"
                                        class="hidden sm:table-cell p-3 sm:p-4 text-xs sm:text-sm text-gray-700 dark:text-gray-300 cursor-pointer hover:bg-gray-50 dark:hover:bg-zinc-700"
                                    >
                                        <div
                                            class="flex items-center space-x-1"
                                        >
                                            <span>Email</span>
                                            <span
                                                v-if="sort.column === 'email'"
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
                                        @click="toggleSort('telephone')"
                                        class="hidden md:table-cell p-3 sm:p-4 text-xs sm:text-sm text-gray-700 dark:text-gray-300 cursor-pointer hover:bg-gray-50 dark:hover:bg-zinc-700"
                                    >
                                        <div
                                            class="flex items-center space-x-1"
                                        >
                                            <span>Téléphone</span>
                                            <span
                                                v-if="
                                                    sort.column === 'telephone'
                                                "
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
                                    v-for="client in filteredClients"
                                    :key="client.id"
                                    class="hover:bg-gray-50 dark:hover:bg-zinc-700"
                                >
                                    <!-- <td class="p-3 sm:p-4 text-xs sm:text-sm text-gray-900 dark:text-gray-100">
                                        #{{ client.id }}
                                    </td> -->
                                    <td
                                        class="p-3 sm:p-4 text-xs sm:text-sm text-gray-900 dark:text-gray-100"
                                    >
                                        {{ client.name }}
                                    </td>
                                    <td
                                        class="p-3 sm:p-4 text-xs sm:text-sm text-gray-900 dark:text-gray-100"
                                    >
                                        {{ client.firstname }}
                                        <!-- Info mobile -->
                                        <div
                                            class="sm:hidden mt-1 text-xs text-gray-500 dark:text-gray-400"
                                        >
                                            {{ client.email }}<br />
                                            {{ client.telephone }}
                                        </div>
                                    </td>
                                    <td
                                        class="hidden sm:table-cell p-3 sm:p-4 text-xs sm:text-sm text-gray-900 dark:text-gray-100"
                                    >
                                        {{ client.email }}
                                    </td>
                                    <td
                                        class="hidden md:table-cell p-3 sm:p-4 text-xs sm:text-sm text-gray-900 dark:text-gray-100"
                                    >
                                        {{ client.telephone }}
                                    </td>
                                    <td class="p-3 sm:p-4 text-xs sm:text-sm">
                                        <!-- Version Desktop -->
                                        <div class="hidden sm:flex space-x-2">
                                            <Link
                                                :href="
                                                    route(
                                                        'clients.show',
                                                        client.id
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
                                                @click="deleteClient(client.id)"
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
                                            class="flex sm:hidden space-x-2 justify-end"
                                        >
                                            <Link
                                                :href="
                                                    route(
                                                        'clients.show',
                                                        client.id
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
                                                @click="deleteClient(client.id)"
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

        <!-- Modal Nouveau Client -->
        <div
            v-if="showClientForm"
            class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
        >
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-xl p-6 w-full max-w-md">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Nouveau Client</h2>
                    <button
                        @click="showClientForm = false"
                        class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300"
                    >
                        ×
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <InputLabel class="dark:text-gray-200" value="Nom" />
                        <TextInput
                            v-model="clientForm.name"
                            type="text"
                            class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            required
                        />
                        <InputError :message="clientForm.errors.name" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel class="dark:text-gray-200" value="Prénom" />
                        <TextInput
                            v-model="clientForm.firstname"
                            type="text"
                            class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            required
                        />
                        <InputError :message="clientForm.errors.firstname" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel class="dark:text-gray-200" value="Email" />
                        <TextInput
                            v-model="clientForm.email"
                            type="email"
                            class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            required
                        />
                        <InputError :message="clientForm.errors.email" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel class="dark:text-gray-200" value="Téléphone" />
                        <TextInput
                            v-model="clientForm.telephone"
                            type="tel"
                            class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        />
                        <InputError :message="clientForm.errors.telephone" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel class="dark:text-gray-200" value="Adresse" />
                        <textarea
                            v-model="clientForm.addresse"
                            rows="3"
                            class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        ></textarea>
                        <InputError :message="clientForm.errors.addresse" class="mt-2" />
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button
                            @click="showClientForm = false"
                            class="px-4 py-2 bg-gray-300 dark:bg-zinc-700 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-400 dark:hover:bg-zinc-600"
                        >
                            Annuler
                        </button>
                        <button
                            @click="submitClient"
                            class="px-4 py-2 bg-indigo-600 dark:bg-indigo-500 text-white rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-600"
                        >
                            Créer le client
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
