<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, useForm } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";

const props = defineProps({
    users: {
        type: Array,
        required: true,
    },
});

const search = ref("");
const showPhone = ref({});
const showUserForm = ref(false);
const showEditForm = ref(false);
const editingUser = ref(null);
const sort = ref({
    column: "name",
    direction: "asc",
});

const form = useForm({
    name: "",
    firstname: "",
    email: "",
    telephone: "",
    role: "technicien",
    password: "",
    password_confirmation: "",
});

const editForm = useForm({
    name: "",
    firstname: "",
    email: "",
    telephone: "",
    role: "",
    password: "",
    password_confirmation: "",
});

const resetUserForm = () => {
    showUserForm.value = false;
    form.reset();
};

const submit = () => {
    form.post(route("users.store"), {
        onSuccess: () => {
            showUserForm.value = false;
            form.reset();
        },
    });
};

const togglePhone = (userId) => {
    showPhone.value[userId] = !showPhone.value[userId];
};

const toggleSort = (column) => {
    if (sort.value.column === column) {
        sort.value.direction = sort.value.direction === "asc" ? "desc" : "asc";
    } else {
        sort.value.column = column;
        sort.value.direction = "asc";
    }
};

const deleteUser = (id) => {
    if (confirm("Êtes-vous sûr de vouloir supprimer cet utilisateur ?")) {
        router.delete(route("users.destroy", id), {
            preserveScroll: true,
            onSuccess: () => {
                // Le rechargement sera géré par Inertia
            },
        });
    }
};

const editUser = (user) => {
    editingUser.value = user;
    editForm.name = user.name;
    editForm.firstname = user.firstname;
    editForm.email = user.email;
    editForm.telephone = user.telephone;
    editForm.role = user.role;
    showEditForm.value = true;
};

const submitEdit = () => {
    editForm.put(route("users.update", editingUser.value.id), {
        onSuccess: () => {
            showEditForm.value = false;
            editingUser.value = null;
            editForm.reset();
        },
    });
};

const resetEditForm = () => {
    showEditForm.value = false;
    editingUser.value = null;
    editForm.reset();
};

const filteredUsers = computed(() => {
    let users = props.users;

    // Filtrage par recherche
    if (search.value) {
        const searchLower = search.value.toLowerCase();
        const searchPhone = search.value.replace(/\s/g, "");
        users = users.filter(
            (user) =>
                user.name.toLowerCase().includes(searchLower) ||
                user.firstname.toLowerCase().includes(searchLower) ||
                user.email.toLowerCase().includes(searchLower) ||
                user.role.toLowerCase().includes(searchLower) ||
                user.telephone.replace(/\s/g, "").includes(searchPhone)
        );
    }

    // Tri
    return users.sort((a, b) => {
        const modifier = sort.value.direction === "asc" ? 1 : -1;
        const aValue = a[sort.value.column].toLowerCase();
        const bValue = b[sort.value.column].toLowerCase();
        return aValue > bValue ? modifier : -modifier;
    });
});
</script>

<template>
    <AppLayout title="Gestion des Utilisateurs">
        <template #header>
            <div
                class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0"
            >
                <div class="flex items-center space-x-3">
                    <span class="text-2xl" role="img" aria-label="utilisateurs"
                        >👨‍💻</span
                    >
                    <h2
                        class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
                    >
                        Gestion des Utilisateurs
                    </h2>
                </div>
                <button
                    v-if="$page.props.auth.user.role === 'admin'"
                    @click="showUserForm = true"
                    class="w-full sm:w-auto px-4 py-2 bg-green-600 dark:bg-green-500 text-white rounded-md hover:bg-green-700 dark:hover:bg-green-600 transition text-center"
                >
                    <span class="flex items-center justify-center space-x-2">
                        <span class="text-lg" role="img" aria-label="nouveau"
                            >➕</span
                        >
                        <span>Nouvel Utilisateur</span>
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
                            placeholder="Rechercher un utilisateur..."
                            class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400"
                        />
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-zinc-800 overflow-hidden shadow-xl sm:rounded-lg"
                >
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
                                        <div class="flex items-center space-x-1">
                                            <span>Nom</span>
                                            <span v-if="sort.column === 'name'" class="text-xs">
                                                {{ sort.direction === "asc" ? "▲" : "▼" }}
                                            </span>
                                        </div>
                                    </th>
                                    <th
                                        @click="toggleSort('firstname')"
                                        class="p-3 sm:p-4 text-xs sm:text-sm text-gray-700 dark:text-gray-300 cursor-pointer hover:bg-gray-50 dark:hover:bg-zinc-700"
                                    >
                                        <div class="flex items-center space-x-1">
                                            <span>Prénom</span>
                                            <span v-if="sort.column === 'firstname'" class="text-xs">
                                                {{ sort.direction === "asc" ? "▲" : "▼" }}
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
                                        class="hidden sm:table-cell p-3 sm:p-4 text-xs sm:text-sm text-gray-700 dark:text-gray-300 cursor-pointer hover:bg-gray-50 dark:hover:bg-zinc-700"
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
                                        @click="toggleSort('role')"
                                        class="hidden md:table-cell p-3 sm:p-4 text-xs sm:text-sm text-gray-700 dark:text-gray-300 cursor-pointer hover:bg-gray-50 dark:hover:bg-zinc-700"
                                    >
                                        <div
                                            class="flex items-center space-x-1"
                                        >
                                            <span>Rôle</span>
                                            <span
                                                v-if="sort.column === 'role'"
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
                                        v-if="
                                            $page.props.auth.user.role ===
                                            'admin'
                                        "
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
                                    v-for="user in filteredUsers"
                                    :key="user.id"
                                    class="hover:bg-gray-50 dark:hover:bg-zinc-700"
                                >
                                    <td
                                        class="p-3 sm:p-4 text-xs sm:text-sm text-gray-900 dark:text-gray-100"
                                    >
                                        {{ user.name }}
                                        <!-- Info mobile -->
                                        <div
                                            class="sm:hidden mt-1 text-xs text-gray-500 dark:text-gray-400"
                                        >
                                            {{ user.firstname }}<br />
                                            {{ user.email }}<br />
                                            <div class="flex items-center space-x-2">
                                                <span v-if="!showPhone[user.id]">{{ "•".repeat(10) }}</span>
                                                <span v-else>{{ user.telephone }}</span>
                                                <button
                                                    @click="togglePhone(user.id)"
                                                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                                                >
                                                    <span v-if="!showPhone[user.id]">👁️</span>
                                                    <span v-else>👁️‍🗨️</span>
                                                </button>
                                            </div>
                                            {{ user.role }}
                                        </div>
                                    </td>
                                    <td
                                        class="p-3 sm:p-4 text-xs sm:text-sm text-gray-900 dark:text-gray-100"
                                    >
                                        {{ user.firstname }}
                                    </td>
                                    <td
                                        class="hidden sm:table-cell p-3 sm:p-4 text-xs sm:text-sm text-gray-900 dark:text-gray-100"
                                    >
                                        {{ user.email }}
                                    </td>
                                    <td
                                        class="hidden sm:table-cell p-3 sm:p-4 text-xs sm:text-sm text-gray-900 dark:text-gray-100"
                                    >
                                        <div
                                            class="flex items-center space-x-2"
                                        >
                                            <span
                                                v-if="
                                                    $page.props.auth.user
                                                        .role === 'admin'
                                                "
                                            >
                                                {{ user.telephone }}
                                            </span>
                                            <span v-else>
                                                <span
                                                    v-if="!showPhone[user.id]"
                                                    >{{ "•".repeat(10) }}</span
                                                >
                                                <span v-else>{{
                                                    user.telephone
                                                }}</span>
                                                <button
                                                    @click="
                                                        togglePhone(user.id)
                                                    "
                                                    class="ml-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                                                >
                                                    <span
                                                        v-if="
                                                            !showPhone[user.id]
                                                        "
                                                        >👁️</span
                                                    >
                                                    <span v-else>👁️‍🗨️</span>
                                                </button>
                                            </span>
                                        </div>
                                    </td>
                                    <td
                                        class="hidden md:table-cell p-3 sm:p-4 text-xs sm:text-sm text-gray-900 dark:text-gray-100"
                                    >
                                        {{ user.role }}
                                    </td>
                                    <td
                                        v-if="
                                            $page.props.auth.user.role ===
                                            'admin'
                                        "
                                        class="p-3 sm:p-4 text-xs sm:text-sm"
                                    >
                                        <!-- Version Desktop -->
                                        <div class="hidden sm:flex space-x-2">
                                            <button
                                                @click="editUser(user)"
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
                                            </button>
                                            <button
                                                @click="deleteUser(user.id)"
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
                                            <button
                                                @click="editUser(user)"
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
                                                @click="deleteUser(user.id)"
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

        <!-- Modal Formulaire Utilisateur -->
        <div
            v-if="showUserForm"
            class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
            @click.self="resetUserForm"
        >
            <div
                class="bg-white dark:bg-zinc-800 rounded-lg p-6 max-w-2xl w-full mx-4 relative"
            >
                <div class="flex justify-between items-center mb-4">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-gray-100"
                    >
                        Nouvel Utilisateur
                    </h3>
                    <button
                        @click="resetUserForm"
                        class="text-gray-400 hover:text-gray-500 text-xl font-medium px-2 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded"
                    >
                        ×
                    </button>
                </div>

                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <InputLabel class="dark:text-gray-200" value="Nom" />
                        <TextInput
                            v-model="form.name"
                            type="text"
                            class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            required
                        />
                    </div>

                    <div>
                        <InputLabel class="dark:text-gray-200" value="Prénom" />
                        <TextInput
                            v-model="form.firstname"
                            type="text"
                            class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            required
                        />
                    </div>

                    <div>
                        <InputLabel class="dark:text-gray-200" value="Email" />
                        <TextInput
                            v-model="form.email"
                            type="email"
                            class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            required
                        />
                    </div>

                    <div>
                        <InputLabel
                            class="dark:text-gray-200"
                            value="Téléphone"
                        />
                        <TextInput
                            v-model="form.telephone"
                            type="tel"
                            class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            required
                        />
                    </div>

                    <div>
                        <InputLabel class="dark:text-gray-200" value="Rôle" />
                        <select
                            v-model="form.role"
                            class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            required
                        >
                            <option value="technicien">Technicien</option>
                            <option value="admin">Administrateur</option>
                        </select>
                    </div>

                    <div>
                        <InputLabel
                            class="dark:text-gray-200"
                            value="Mot de passe"
                        />
                        <TextInput
                            v-model="form.password"
                            type="password"
                            class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            required
                        />
                    </div>

                    <div>
                        <InputLabel
                            class="dark:text-gray-200"
                            value="Confirmation du mot de passe"
                        />
                        <TextInput
                            v-model="form.password_confirmation"
                            type="password"
                            class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            required
                        />
                    </div>

                    <div class="flex justify-end space-x-3">
                        <SecondaryButton
                            @click.prevent="resetUserForm"
                            class="dark:bg-zinc-700 dark:text-gray-300 dark:hover:bg-zinc-600"
                        >
                            Annuler
                        </SecondaryButton>
                        <PrimaryButton
                            type="submit"
                            :disabled="form.processing"
                            class="bg-indigo-600 dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-600"
                        >
                            Créer l'utilisateur
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal d'édition.. -->
        <div
            v-if="showEditForm"
            class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
            @click.self="resetEditForm"
        >
            <div
                class="bg-white dark:bg-zinc-800 rounded-lg p-6 max-w-2xl w-full mx-4 relative"
            >
                <div class="flex justify-between items-center mb-4">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-gray-100"
                    >
                        Modifier l'utilisateur
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
                        <InputLabel class="dark:text-gray-200" value="Nom" />
                        <TextInput
                            v-model="editForm.name"
                            type="text"
                            class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            required
                        />
                    </div>

                    <div>
                        <InputLabel class="dark:text-gray-200" value="Prénom" />
                        <TextInput
                            v-model="editForm.firstname"
                            type="text"
                            class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            required
                        />
                    </div>

                    <div>
                        <InputLabel class="dark:text-gray-200" value="Email" />
                        <TextInput
                            v-model="editForm.email"
                            type="email"
                            class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            required
                        />
                    </div>

                    <div>
                        <InputLabel
                            class="dark:text-gray-200"
                            value="Téléphone"
                        />
                        <TextInput
                            v-model="editForm.telephone"
                            type="tel"
                            class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            required
                        />
                    </div>

                    <div>
                        <InputLabel class="dark:text-gray-200" value="Rôle" />
                        <select
                            v-model="editForm.role"
                            class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            required
                        >
                            <option value="technicien">Technicien</option>
                            <option value="admin">Administrateur</option>
                        </select>
                    </div>

                    <div>
                        <InputLabel
                            class="dark:text-gray-200"
                            value="Nouveau mot de passe (optionnel)"
                        />
                        <TextInput
                            v-model="editForm.password"
                            type="password"
                            class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        />
                    </div>

                    <div>
                        <InputLabel
                            class="dark:text-gray-200"
                            value="Confirmation du nouveau mot de passe"
                        />
                        <TextInput
                            v-model="editForm.password_confirmation"
                            type="password"
                            class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        />
                    </div>


                    <div class="flex justify-end space-x-3">
                        <SecondaryButton
                            @click.prevent="resetEditForm"
                            class="dark:bg-zinc-700 dark:text-gray-300 dark:hover:bg-zinc-600"
                        >
                            Annuler
                        </SecondaryButton>
                        <PrimaryButton
                            type="submit"
                            :disabled="editForm.processing"
                            class="bg-indigo-600 dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-600"
                        >
                            Mettre à jour
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
