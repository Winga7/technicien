<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";

const props = defineProps({
    client: {
        type: Object,
        required: true,
    },
    tickets: {
        type: Array,
        required: true,
    },
});

const isEditing = ref(false);

const form = useForm({
    name: props.client.name,
    prenom: props.client.prenom,
    email: props.client.email,
    telephone: props.client.telephone || "",
    addresse: props.client.addresse || "",
});

const submit = () => {
    form.put(route("clients.update", props.client.id), {
        onSuccess: () => {
            isEditing.value = false;
        },
    });
};

const getStatusColor = (statut) => {
    switch (statut) {
        case "en attente":
            return "bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300";
        case "en cours":
            return "bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300";
        case "terminé":
            return "bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300";
        default:
            return "bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300";
    }
};

const showTicketForm = ref(false);
const imagePreview = ref([]);

const ticketForm = useForm({
    titre: '',
    description: '',
    client_id: props.client.id,
    statut: 'en attente',
    images: [],
});

const handleImageUpload = (e) => {
    const files = Array.from(e.target.files);
    ticketForm.images = files;
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
    if (ticketForm.images.length > 0) {
        ticketForm.images.splice(index, 1);
    }
};

const submitTicket = () => {
    ticketForm.post(route('tickets.store'), {
        onSuccess: () => {
            resetTicketForm();
        },
    });
};

const resetTicketForm = () => {
    ticketForm.reset();
    imagePreview.value = [];
    showTicketForm.value = false;
};
</script>

<template>
    <AppLayout :title="'Client - ' + client.name + ' ' + client.prenom">
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <span class="text-2xl" role="img" aria-label="client">👤</span>
                    <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                        {{ client.name }} {{ client.prenom }}
                    </h2>
                </div>
                <div class="space-x-4">
                    <button
                        @click="showTicketForm = true"
                        class="px-4 py-2 bg-green-600 dark:bg-green-500 text-white rounded-md hover:bg-green-700 dark:hover:bg-green-600 transition"
                    >
                        <span class="flex items-center space-x-2">
                            <span class="text-lg" role="img" aria-label="nouveau ticket">🎫</span>
                            <span>Nouveau Ticket</span>
                        </span>
                    </button>
                    <button
                        @click="isEditing = !isEditing"
                        class="px-4 py-2 bg-indigo-600 dark:bg-indigo-500 text-white rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-600 transition"
                    >
                        <span class="flex items-center space-x-2">
                            <span class="text-lg" role="img" aria-label="modifier">✏️</span>
                            <span>{{ isEditing ? 'Annuler' : 'Modifier' }}</span>
                        </span>
                    </button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Informations du client -->
                <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div v-if="!isEditing">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Informations personnelles</h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Nom complet:</span>
                                        <span class="ml-2 text-gray-900 dark:text-gray-100">{{ client.name }} {{ client.prenom }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Email:</span>
                                        <span class="ml-2 text-gray-900 dark:text-gray-100">{{ client.email }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Téléphone:</span>
                                        <span class="ml-2 text-gray-900 dark:text-gray-100">{{ client.telephone }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Adresse:</span>
                                        <span class="ml-2 text-gray-900 dark:text-gray-100">{{ client.addresse }}</span>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Statistiques</h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Nombre total de tickets:</span>
                                        <span class="ml-2 text-gray-900 dark:text-gray-100">{{ tickets.length }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Tickets en cours:</span>
                                        <span class="ml-2 text-gray-900 dark:text-gray-100">
                                            {{ tickets.filter(t => t.statut === 'en cours').length }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulaire de modification -->
                    <form v-else @submit.prevent="submit" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <InputLabel class="dark:text-gray-200" value="Nom" />
                                <TextInput
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                />
                            </div>
                            <div>
                                <InputLabel class="dark:text-gray-200" value="Prénom" />
                                <TextInput
                                    v-model="form.prenom"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                />
                            </div>
                            <div>
                                <InputLabel class="dark:text-gray-200" value="Email" />
                                <TextInput
                                    v-model="form.email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    required
                                />
                            </div>
                            <div>
                                <InputLabel class="dark:text-gray-200" value="Téléphone" />
                                <TextInput
                                    v-model="form.telephone"
                                    type="tel"
                                    class="mt-1 block w-full"
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
                            <PrimaryButton type="submit" :disabled="form.processing">
                                Enregistrer les modifications
                            </PrimaryButton>
                        </div>
                    </form>
                </div>

                <!-- Historique des tickets -->
                <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Historique des tickets</h3>
                    <div class="space-y-4">
                        <div v-for="ticket in tickets" :key="ticket.id"
                            class="border dark:border-zinc-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-zinc-700 transition cursor-pointer"
                            @click="$inertia.visit(route('tickets.show', ticket.id))"
                        >
                            <div class="flex justify-between items-start">
                                <div class="flex-grow">
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                        {{ ticket.titre }}
                                    </h4>
                                    <p class="text-gray-500 dark:text-gray-400 mt-1">{{ ticket.description }}</p>
                                    <div class="mt-2 flex items-center space-x-2">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                            Créé le {{ new Date(ticket.created_at).toLocaleDateString('fr-FR') }}
                                        </span>
                                        <span :class="getStatusColor(ticket.statut)" class="px-2 py-1 rounded-full text-xs font-medium">
                                            {{ ticket.statut }}
                                        </span>
                                    </div>

                                    <!-- Interventions liées au ticket -->
                                    <div v-if="ticket.interventions && ticket.interventions.length > 0"
                                        class="mt-4 ml-4 border-l-2 border-gray-200 dark:border-gray-700 pl-4"
                                    >
                                        <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Interventions ({{ ticket.interventions.length }})
                                        </h5>
                                        <div class="space-y-2">
                                            <div v-for="intervention in ticket.interventions"
                                                :key="intervention.id"
                                                class="bg-white dark:bg-zinc-800 p-3 rounded-md"
                                                @click.stop
                                            >
                                                <div class="font-medium text-gray-900 dark:text-gray-100">
                                                    {{ intervention.titre }}
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ intervention.description }}
                                                </div>
                                                <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                                    Par {{ intervention.technicien?.name }} -
                                                    {{ new Date(intervention.created_at).toLocaleDateString('fr-FR') }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <Link
                                    :href="route('tickets.edit', ticket.id)"
                                    class="text-yellow-600 hover:text-yellow-800 dark:text-yellow-400 dark:hover:text-yellow-300 text-sm"
                                    @click.stop
                                >
                                    Modifier
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>

    <!-- Formulaire de création de ticket -->
    <div v-if="showTicketForm"
        class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
        @click.self="resetTicketForm"
    >
        <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 max-w-2xl w-full mx-4 relative">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Nouveau Ticket</h3>
                <button
                    @click="resetTicketForm"
                    class="text-gray-400 hover:text-gray-500 text-xl font-medium px-2 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded"
                >
                    ×
                </button>
            </div>

            <form @submit.prevent="submitTicket" class="space-y-4">
                <div>
                    <InputLabel class="dark:text-gray-200" value="Titre" />
                    <TextInput
                        v-model="ticketForm.titre"
                        type="text"
                        class="mt-1 block w-full"
                        required
                    />
                </div>

                <div>
                    <InputLabel class="dark:text-gray-200" value="Description" />
                    <textarea
                        v-model="ticketForm.description"
                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        rows="4"
                        required
                    />
                </div>

                <!-- Section images -->
                <div>
                    <InputLabel class="dark:text-gray-200" value="Images" />
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 dark:border-gray-700 border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600 dark:text-gray-400">
                                <label class="relative cursor-pointer rounded-md font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 focus-within:outline-none">
                                    <span>Télécharger des fichiers</span>
                                    <input type="file" class="sr-only" multiple @change="handleImageUpload" accept="image/*">
                                </label>
                                <p class="pl-1">ou glisser-déposer</p>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                PNG, JPG, GIF jusqu'à 2MB
                            </p>
                        </div>
                    </div>

                    <!-- Aperçu des images -->
                    <div v-if="imagePreview.length > 0" class="mt-4 grid grid-cols-3 gap-4">
                        <div v-for="(image, index) in imagePreview" :key="index" class="relative">
                            <img :src="image" class="h-24 w-full object-cover rounded-lg">
                            <button
                                @click.prevent="removeImage(index)"
                                class="absolute top-0 right-0 -mt-2 -mr-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600"
                            >
                                ×
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <SecondaryButton
                        @click.prevent="showTicketForm = false"
                        class="dark:bg-zinc-700 dark:text-gray-300 dark:hover:bg-zinc-600"
                    >
                        Annuler
                    </SecondaryButton>
                    <PrimaryButton
                        type="submit"
                        :disabled="ticketForm.processing"
                        class="bg-indigo-600 dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-600"
                    >
                        Créer le ticket
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </div>
</template>
