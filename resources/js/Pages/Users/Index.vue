<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";
import { router } from "@inertiajs/vue3";

const props = defineProps({
    users: {
        type: Array,
        required: true,
    },
});

const editUser = (user) => {
    alert("Modifier " + user.name);
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
</script>

<template>
    <AppLayout title="Gestion des Utilisateurs">
        <template #header>
            <div class="flex justify-between items-center">
                <h2
                    class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
                >
                    Gestion des Utilisateurs
                </h2>
                <Link
                    v-if="$page.props.auth.user.role === 'admin'"
                    :href="route('users.create')"
                    class="bg-blue-500 dark:bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-600 dark:hover:bg-blue-700 transition"
                >
                    Créer un utilisateur
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white dark:bg-zinc-800 overflow-hidden shadow-xl sm:rounded-lg p-6"
                >
                    <table class="w-full">
                        <thead>
                            <tr class="text-left">
                                <th
                                    class="px-4 py-2 text-gray-700 dark:text-gray-300"
                                >
                                    Nom
                                </th>
                                <th
                                    class="px-4 py-2 text-gray-700 dark:text-gray-300"
                                >
                                    Email
                                </th>
                                <th
                                    class="px-4 py-2 text-gray-700 dark:text-gray-300"
                                >
                                    Téléphone
                                </th>
                                <th
                                    class="px-4 py-2 text-gray-700 dark:text-gray-300"
                                >
                                    Rôle
                                </th>
                                <th
                                    class="px-4 py-2 text-gray-700 dark:text-gray-300"
                                >
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="user in users"
                                :key="user.id"
                                class="border-t dark:border-zinc-700"
                            >
                                <td
                                    class="px-4 py-2 text-gray-800 dark:text-gray-200"
                                >
                                    {{ user.name }}
                                </td>
                                <td
                                    class="px-4 py-2 text-gray-800 dark:text-gray-200"
                                >
                                    {{ user.email }}
                                </td>
                                <td
                                    class="px-4 py-2 text-gray-800 dark:text-gray-200"
                                >
                                    {{ user.telephone }}
                                </td>
                                <td
                                    class="px-4 py-2 text-gray-800 dark:text-gray-200"
                                >
                                    {{ user.role }}
                                </td>
                                <td class="px-4 py-2 space-x-2">
                                    <Link
                                        v-if="
                                            $page.props.auth.user.role ===
                                            'admin'
                                        "
                                        :href="route('users.edit', user.id)"
                                        class="inline-flex items-center px-3 py-1 bg-blue-500 dark:bg-blue-600 text-white rounded-md hover:bg-blue-600 dark:hover:bg-blue-700 transition"
                                    >
                                        Modifier
                                    </Link>
                                    <button
                                        v-if="
                                            $page.props.auth.user.role ===
                                            'admin'
                                        "
                                        @click="deleteUser(user.id)"
                                        class="inline-flex items-center px-3 py-1 bg-red-500 dark:bg-red-600 text-white rounded-md hover:bg-red-600 dark:hover:bg-red-700 transition"
                                    >
                                        Supprimer
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
