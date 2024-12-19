<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    ticket: {
        type: Object,
        required: true,
    },
    clients: {
        type: Array,
        required: true,
    },
    techniciens: {
        type: Array,
        required: true,
    },
});

const imagePreview = ref(
    props.ticket.images ? JSON.parse(props.ticket.images) : []
);

const form = useForm({
    titre: props.ticket.titre,
    description: props.ticket.description,
    statut: props.ticket.statut,
    client_id: props.ticket.client_id,
    technicien_id: props.ticket.technicien_id || "",
    images: [],
    _method: "PUT",
});

const handleImageUpload = (e) => {
    const files = Array.from(e.target.files);
    form.images = files;
    imagePreview.value = [];

    files.forEach((file) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value.push(e.target.result);
        };
        reader.readAsDataURL(file);
    });
};

const removeImage = (index) => {
    imagePreview.value.splice(index, 1);
    if (form.images.length > 0) {
        form.images.splice(index, 1);
    }
};

const submit = () => {
    form.post(route("tickets.update", props.ticket.id), {
        onSuccess: () => {
            form.reset();
            imagePreview.value = [];
        },
    });
};
</script>

<template>
    <AppLayout title="Modifier le ticket">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Modifier le ticket #{{ ticket.id }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white dark:bg-zinc-800 overflow-hidden shadow-xl sm:rounded-lg p-6"
                >
                    <form @submit.prevent="submit" class="space-y-6">
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
                                value="Statut"
                            />
                            <select
                                v-model="form.statut"
                                class="mt-1 block w-full border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            >
                                <option value="en attente">En attente</option>
                                <option value="en cours">En cours</option>
                                <option value="terminé">Terminé</option>
                            </select>
                        </div>

                        <div>
                            <InputLabel
                                class="dark:text-gray-200"
                                value="Client"
                            />
                            <select
                                v-model="form.client_id"
                                class="mt-1 block w-full border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                required
                            >
                                <option value="">Sélectionner un client</option>
                                <option
                                    v-for="client in clients"
                                    :key="client.id"
                                    :value="client.id"
                                >
                                    {{ client.name }} {{ client.prenom }}
                                </option>
                            </select>
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
                                        v-for="(image, index) in JSON.parse(
                                            ticket.images
                                        )"
                                        :key="index"
                                        :src="`/storage/${image}`"
                                        :alt="'Image ' + (index + 1)"
                                        class="w-full h-48 object-cover rounded-lg"
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
                                :disabled="form.processing"
                            >
                                Mettre à jour le ticket
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
