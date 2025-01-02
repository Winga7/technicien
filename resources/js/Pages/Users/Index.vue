<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";
import { ref, computed } from "vue";

const props = defineProps({
    users: {
        type: Array,
        required: true,
    },
});

const search = ref('');
const showPhone = ref({});
const showUserForm = ref(false);

const togglePhone = (userId) => {
    showPhone.value[userId] = !showPhone.value[userId];
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

const filteredUsers = computed(() => {
    if (!search.value) return props.users;
    const searchLower = search.value.toLowerCase();
    const searchPhone = search.value.replace(/\s/g, '');
    return props.users.filter(user =>
        user.name.toLowerCase().includes(searchLower) ||
        user.email.toLowerCase().includes(searchLower) ||
        user.role.toLowerCase().includes(searchLower) ||
        user.telephone.replace(/\s/g, '').includes(searchPhone)
    );
});
</script>

<template>
    <AppLayout title="Gestion des Utilisateurs">
        <template #header>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                <div class="flex items-center space-x-3">
                    <span class="text-2xl" role="img" aria-label="utilisateurs">👨‍💻</span>
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        Gestion des Utilisateurs
                    </h2>
                </div>
                <button
                    v-if="$page.props.auth.user.role === 'admin'"
                    @click="showUserForm = true"
                    class="w-full sm:w-auto px-4 py-2 bg-green-600 dark:bg-green-500 text-white rounded-md hover:bg-green-700 dark:hover:bg-green-600 transition text-center"
                >
                    <span class="flex items-center justify-center space-x-2">
                        <span class="text-lg" role="img" aria-label="nouveau">➕</span>
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
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center">
                            <span class="text-gray-500 dark:text-gray-400" role="img" aria-label="recherche">🔍</span>
                        </span>
                        <input
                            type="text"
                            v-model="search"
                            placeholder="Rechercher un utilisateur..."
                            class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400"
                        >
                    </div>
                </div>

                <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-zinc-700">
                            <thead>
                                <tr class="text-left">
                                    <th class="p-3 sm:p-4 text-xs sm:text-sm text-gray-700 dark:text-gray-300">Nom</th>
                                    <th class="hidden sm:table-cell p-3 sm:p-4 text-xs sm:text-sm text-gray-700 dark:text-gray-300">Email</th>
                                    <th class="hidden sm:table-cell p-3 sm:p-4 text-xs sm:text-sm text-gray-700 dark:text-gray-300">Téléphone</th>
                                    <th class="hidden md:table-cell p-3 sm:p-4 text-xs sm:text-sm text-gray-700 dark:text-gray-300">Rôle</th>
                                    <th v-if="$page.props.auth.user.role === 'admin'" class="p-3 sm:p-4 text-xs sm:text-sm text-gray-700 dark:text-gray-300">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                                <tr v-for="user in filteredUsers" :key="user.id" class="hover:bg-gray-50 dark:hover:bg-zinc-700">
                                    <td class="p-3 sm:p-4 text-xs sm:text-sm text-gray-900 dark:text-gray-100">
                                        {{ user.name }}
                                        <!-- Info mobile -->
                                        <div class="sm:hidden mt-1 text-xs text-gray-500 dark:text-gray-400">
                                            {{ user.email }}<br>
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
                                    <td class="hidden sm:table-cell p-3 sm:p-4 text-xs sm:text-sm text-gray-900 dark:text-gray-100">
                                        {{ user.email }}
                                    </td>
                                    <td class="hidden sm:table-cell p-3 sm:p-4 text-xs sm:text-sm text-gray-900 dark:text-gray-100">
                                        <div class="flex items-center space-x-2">
                                            <span v-if="$page.props.auth.user.role === 'admin'">
                                                {{ user.telephone }}
                                            </span>
                                            <span v-else>
                                                <span v-if="!showPhone[user.id]">{{ "•".repeat(10) }}</span>
                                                <span v-else>{{ user.telephone }}</span>
                                                <button
                                                    @click="togglePhone(user.id)"
                                                    class="ml-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                                                >
                                                    <span v-if="!showPhone[user.id]">👁️</span>
                                                    <span v-else>👁️‍🗨️</span>
                                                </button>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="hidden md:table-cell p-3 sm:p-4 text-xs sm:text-sm text-gray-900 dark:text-gray-100">
                                        {{ user.role }}
                                    </td>
                                    <td v-if="$page.props.auth.user.role === 'admin'" class="p-3 sm:p-4 text-xs sm:text-sm">
                                        <!-- Version Desktop -->
                                        <div class="hidden sm:flex space-x-2">
                                            <Link
                                                :href="route('users.edit', user.id)"
                                                class="px-3 py-1 bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-400 rounded-md hover:bg-yellow-200 dark:hover:bg-yellow-800 transition"
                                            >
                                                <span class="flex items-center space-x-1">
                                                    <span class="text-lg" role="img" aria-label="modifier">✏️</span>
                                                    <span>Modifier</span>
                                                </span>
                                            </Link>
                                            <button
                                                @click="deleteUser(user.id)"
                                                class="px-3 py-1 bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-400 rounded-md hover:bg-red-200 dark:hover:bg-red-800 transition"
                                            >
                                                <span class="flex items-center space-x-1">
                                                    <span class="text-lg" role="img" aria-label="supprimer">🗑️</span>
                                                    <span>Supprimer</span>
                                                </span>
                                            </button>
                                        </div>

                                        <!-- Version Mobile -->
                                        <div class="flex sm:hidden space-x-2 justify-end">
                                            <Link
                                                :href="route('users.edit', user.id)"
                                                class="text-yellow-600 dark:text-yellow-400 hover:text-yellow-800 dark:hover:text-yellow-300"
                                            >
                                                <span class="text-lg" role="img" aria-label="modifier">✏️</span>
                                            </Link>
                                            <button
                                                @click="deleteUser(user.id)"
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
