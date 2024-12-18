<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

defineProps({
    clients: {
        type: Array,
        required: true,
    },
    techniciens: {
        type: Array,
        required: true,
    },
});

const isNewClient = ref(false);
const imagePreview = ref([]);

const form = useForm({
    titre: "",
    description: "",
    statut: "en attente",
    client_id: "",
    client: {
        name: "",
        prenom: "",
        email: "",
        telephone: "",
        addresse: "",
    },
    technicien_id: "",
    images: [],
});

const handleImageUpload = (e) => {
    const files = Array.from(e.target.files);
    form.images = []; // Réinitialiser les images
    imagePreview.value = []; // Réinitialiser les previews

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

const submit = () => {
    form.post(route("tickets.store"), {
        onSuccess: () => {
            form.reset();
            imagePreview.value = [];
        },
        onError: (errors) => {
            console.error(errors);
        },
    });
};
</script>

<template>
    <AppLayout title="Nouveau ticket">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Nouveau ticket
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div
                    class="p-4 sm:p-8 bg-white dark:bg-zinc-800 shadow sm:rounded-lg"
                >
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Type de client -->
                        <div>
                            <InputLabel
                                class="dark:text-gray-200"
                                value="Type de client"
                            />
                            <div class="flex space-x-4 mt-2">
                                <label class="inline-flex items-center">
                                    <input
                                        type="radio"
                                        v-model="isNewClient"
                                        :value="false"
                                        class="form-radio border-gray-200 dark:border-gray-700 text-indigo-600 dark:bg-gray-900 focus:ring-indigo-500"
                                    />
                                    <span
                                        class="ml-2 text-gray-700 dark:text-gray-200"
                                        >Client existant</span
                                    >
                                </label>
                                <label class="inline-flex items-center">
                                    <input
                                        type="radio"
                                        v-model="isNewClient"
                                        :value="true"
                                        class="form-radio border-gray-200 dark:border-gray-700 text-indigo-600 dark:bg-gray-900 focus:ring-indigo-500"
                                    />
                                    <span
                                        class="ml-2 text-gray-700 dark:text-gray-200"
                                        >Nouveau client</span
                                    >
                                </label>
                            </div>
                        </div>

                        <!-- Sélection du client existant -->
                        <div v-if="!isNewClient">
                            <InputLabel
                                class="dark:text-gray-200"
                                value="Client"
                            />
                            <select
                                v-model="form.client_id"
                                class="mt-1 block w-full border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            >
                                <option value="">Sélectionner un client</option>
                                <option
                                    v-for="client in clients"
                                    :key="client.id"
                                    :value="client.id"
                                >
                                    {{ client.name }} - {{ client.prenom }}
                                </option>
                            </select>
                        </div>

                        <!-- Formulaire nouveau client -->
                        <div v-else class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <!-- name -->
                                <div>
                                    <InputLabel
                                        class="dark:text-gray-200"
                                        value="Nom"
                                    />
                                    <TextInput
                                        v-model="form.client.name"
                                        type="text"
                                        class="mt-1 block w-full border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        required
                                    />
                                </div>
                                <!-- Prénom -->
                                <div>
                                    <InputLabel
                                        class="dark:text-gray-200"
                                        value="Prénom"
                                    />
                                    <TextInput
                                        v-model="form.client.prenom"
                                        type="text"
                                        class="mt-1 block w-full border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        required
                                    />
                                </div>
                                <!-- Email -->
                                <div>
                                    <InputLabel
                                        class="dark:text-gray-200"
                                        value="Email"
                                    />
                                    <TextInput
                                        v-model="form.client.email"
                                        type="email"
                                        class="mt-1 block w-full border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        required
                                    />
                                </div>
                                <!-- Téléphone -->
                                <div>
                                    <InputLabel
                                        class="dark:text-gray-200"
                                        value="Téléphone"
                                    />
                                    <TextInput
                                        v-model="form.client.telephone"
                                        type="tel"
                                        class="mt-1 block w-full border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    />
                                </div>
                            </div>
                            <!-- Adresse reste en pleine largeur -->
                            <div>
                                <InputLabel
                                    class="dark:text-gray-200"
                                    value="Adresse"
                                />
                                <textarea
                                    v-model="form.client.addresse"
                                    class="mt-1 block w-full border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    rows="3"
                                />
                            </div>
                        </div>

                        <!-- Détails du ticket -->
                        <div>
                            <InputLabel
                                class="dark:text-gray-200"
                                value="Titre du ticket"
                            />
                            <TextInput
                                v-model="form.titre"
                                type="text"
                                class="mt-1 block w-full border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required
                            />
                        </div>

                        <div>
                            <InputLabel
                                class="dark:text-gray-200"
                                value="Description"
                            />
                            <textarea
                                v-model="form.description"
                                rows="4"
                                class="mt-1 block w-full border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required
                            />
                        </div>

                        <div>
                            <InputLabel
                                class="dark:text-gray-200"
                                value="Technicien assigné"
                            />
                            <select
                                v-model="form.technicien_id"
                                class="mt-1 block w-full border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            >
                                <option value="">
                                    Sélectionner un technicien
                                </option>
                                <option
                                    v-for="tech in techniciens"
                                    :key="tech.id"
                                    :value="tech.id"
                                >
                                    {{ tech.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Upload d'images -->
                        <div>
                            <InputLabel
                                class="dark:text-gray-200"
                                value="Images"
                            />
                            <input
                                type="file"
                                @change="handleImageUpload"
                                multiple
                                accept="image/*"
                                class="mt-1 block w-full text-gray-700 dark:text-gray-200 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-800 dark:file:bg-indigo-600 file:text-white dark:file:text-gray-200 hover:file:bg-gray-700 dark:hover:file:bg-indigo-700"
                            />
                            <div
                                v-if="imagePreview.length"
                                class="mt-2 grid grid-cols-3 gap-4"
                            >
                                <div
                                    v-for="(image, index) in imagePreview"
                                    :key="index"
                                    class="relative"
                                >
                                    <img
                                        :src="image"
                                        class="w-full h-32 object-cover rounded"
                                    />
                                    <button
                                        @click.prevent="removeImage(index)"
                                        class="absolute top-0 right-0 bg-red-500 text-white p-1 rounded-full"
                                    >
                                        X
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <PrimaryButton
                                class="dark:bg-indigo-600 dark:hover:bg-indigo-700 dark:text-gray-200"
                                type="submit"
                                :disabled="form.processing"
                            >
                                Créer le ticket
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
