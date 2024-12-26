<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";

defineProps({
    clients: {
        type: Array,
        required: true,
    },
});

const showCreateForm = ref(false);

const form = useForm({
    name: "",
    prenom: "",
    email: "",
    telephone: "",
    addresse: "",
});

const submit = () => {
    form.post(route("clients.store"), {
        onSuccess: () => {
            showCreateForm.value = false;
            form.reset();
        },
    });
};
</script>

<template>
    <AppLayout title="Clients">
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <span class="text-2xl" role="img" aria-label="clients">👥</span>
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        Gestion des Clients
                    </h2>
                </div>
                <Link
                    :href="route('clients.create')"
                    class="px-4 py-2 bg-green-600 dark:bg-green-500 text-white rounded-md hover:bg-green-700 dark:hover:bg-green-600 transition"
                >
                    <span class="flex items-center space-x-2">
                        <span class="text-lg" role="img" aria-label="nouveau">➕</span>
                        <span>Nouveau Client</span>
                    </span>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Formulaire de création -->
                <div v-if="showCreateForm" class="mb-6">
                    <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                        <form @submit.prevent="submit" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <InputLabel class="dark:text-gray-200" value="Nom" />
                                    <TextInput
                                        v-model="form.name"
                                        type="text"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        required
                                    />
                                </div>
                                <div>
                                    <InputLabel class="dark:text-gray-200" value="Prénom" />
                                    <TextInput
                                        v-model="form.prenom"
                                        type="text"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        required
                                    />
                                </div>
                                <div>
                                    <InputLabel class="dark:text-gray-200" value="Email" />
                                    <TextInput
                                        v-model="form.email"
                                        type="email"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        required
                                    />
                                </div>
                                <div>
                                    <InputLabel class="dark:text-gray-200" value="Téléphone" />
                                    <TextInput
                                        v-model="form.telephone"
                                        type="tel"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    />
                                </div>
                            </div>
                            <div>
                                <InputLabel class="dark:text-gray-200" value="Adresse" />
                                <textarea
                                    v-model="form.addresse"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    rows="3"
                                />
                            </div>
                            <div class="flex justify-end space-x-3">
                                <SecondaryButton
                                    @click="showCreateForm = false"
                                    class="dark:bg-zinc-700 dark:text-gray-300 dark:hover:bg-zinc-600"
                                >
                                    Annuler
                                </SecondaryButton>
                                <PrimaryButton
                                    type="submit"
                                    :disabled="form.processing"
                                    class="bg-indigo-600 dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-600 text-white"
                                >
                                    Créer le client
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Liste des clients -->
                <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left border-b dark:border-zinc-700">
                                <th class="px-4 py-3 text-gray-700 dark:text-gray-300">Nom</th>
                                <th class="px-4 py-3 text-gray-700 dark:text-gray-300">Prénom</th>
                                <th class="px-4 py-3 text-gray-700 dark:text-gray-300">Email</th>
                                <th class="px-4 py-3 text-gray-700 dark:text-gray-300">Téléphone</th>
                                <th class="px-4 py-3 text-gray-700 dark:text-gray-300">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="client in clients" :key="client.id" class="border-b dark:border-zinc-700">
                                <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ client.name }}</td>
                                <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ client.prenom }}</td>
                                <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ client.email }}</td>
                                <td class="px-4 py-3 text-gray-800 dark:text-gray-200">{{ client.telephone }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex space-x-3">
                                        <Link
                                            :href="route('clients.show', client.id)"
                                            class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300"
                                        >
                                            Voir
                                        </Link>
                                        <Link
                                            :href="route('clients.edit', client.id)"
                                            class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300"
                                        >
                                            Modifier
                                        </Link>
                                        <button
                                            @click="deleteClient(client.id)"
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
    </AppLayout>
</template>
