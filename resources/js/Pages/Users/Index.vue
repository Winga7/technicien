<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    users: {
        type: Array,
        required: true,
    },
});

const editUser = (user) => {
    alert("Modifier " + user.nom);
};

const deleteUser = (id) => {
    alert("Supprimer l'utilisateur avec ID " + id);
};
</script>

<template>
    <AppLayout title="Gestion des Utilisateurs">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Gestion des Utilisateurs
            </h2>
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
                                    {{ user.nom }}
                                </td>
                                <td
                                    class="px-4 py-2 text-gray-800 dark:text-gray-200"
                                >
                                    {{ user.email }}
                                </td>
                                <td
                                    class="px-4 py-2 text-gray-800 dark:text-gray-200"
                                >
                                    {{ user.role }}
                                </td>
                                <td class="px-4 py-2">
                                    <button
                                        @click="editUser(user)"
                                        class="bg-blue-500 text-white px-3 py-1 rounded mr-2 hover:bg-blue-600"
                                    >
                                        Modifier
                                    </button>
                                    <button
                                        @click="deleteUser(user.id)"
                                        class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600"
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
