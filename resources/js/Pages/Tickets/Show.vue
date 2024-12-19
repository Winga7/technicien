<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";

// Props existants
const props = defineProps({
    ticket: {
        type: Object,
        required: true,
    },
    techniciens: {
        type: Array,
        required: true,
    },
});

const showInterventionForm = ref(false);
const processing = ref(false);

const interventionForm = useForm({
    titre: "",
    description: "",
    ticket_id: props.ticket.id,
    client_id: props.ticket.client_id,
    technicien_id: "",
    statut: "en attente",
});

const submitIntervention = async () => {
    processing.value = true;
    interventionForm.post(route("interventions.store"), {
        onSuccess: () => {
            showInterventionForm.value = false;
            interventionForm.reset();
        },
        onFinish: () => {
            processing.value = false;
        },
    });
};

const getStatusColor = (statut) => {
    switch (statut) {
        case "en attente":
            return "text-blue-500";
        case "en cours":
            return "text-yellow-500";
        case "terminé":
            return "text-green-500";
        default:
            return "text-gray-500";
    }
};
</script>

<template>
    <AppLayout :title="'Ticket #' + ticket.id">
        <template #header>
            <div class="flex justify-between items-center">
                <h2
                    class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
                >
                    Ticket #{{ ticket.id }} - {{ ticket.titre }}
                </h2>
                <Link
                    :href="route('tickets.index')"
                    class="px-4 py-2 bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-900 rounded-md hover:bg-gray-700 dark:hover:bg-gray-300 transition"
                >
                    Retour aux tickets
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white dark:bg-zinc-800 overflow-hidden shadow-xl sm:rounded-lg p-6"
                >
                    <!-- Informations du ticket -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Colonne de gauche -->
                        <div class="space-y-6">
                            <div>
                                <h3
                                    class="text-lg font-medium text-gray-900 dark:text-gray-100"
                                >
                                    Détails du ticket
                                </h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <span
                                            class="text-gray-500 dark:text-gray-400"
                                            >Statut:</span
                                        >
                                        <span
                                            :class="[
                                                'px-2 py-1 rounded-full text-sm font-medium',
                                                getStatusColor(ticket.statut),
                                            ]"
                                        >
                                            {{ ticket.statut }}
                                        </span>
                                    </div>
                                    <div>
                                        <span
                                            class="text-gray-500 dark:text-gray-400"
                                            >Créé le:</span
                                        >
                                        <span
                                            class="text-gray-900 dark:text-gray-100"
                                        >
                                            {{
                                                new Date(
                                                    ticket.created_at
                                                ).toLocaleDateString()
                                            }}
                                        </span>
                                    </div>
                                    <div>
                                        <span
                                            class="text-gray-500 dark:text-gray-400"
                                            >Description:</span
                                        >
                                        <p
                                            class="mt-1 text-gray-900 dark:text-gray-100"
                                        >
                                            {{ ticket.description }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Images du ticket -->
                            <div v-if="ticket.images">
                                <h3
                                    class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4"
                                >
                                    Images
                                </h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <img
                                        v-for="(image, index) in JSON.parse(
                                            ticket.images
                                        )"
                                        :key="index"
                                        :src="`/storage/${image}`"
                                        :alt="'Image ' + (index + 1)"
                                        class="w-full h-48 object-cover rounded-lg"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Colonne de droite -->
                        <div class="space-y-6">
                            <div>
                                <h3
                                    class="text-lg font-medium text-gray-900 dark:text-gray-100"
                                >
                                    Informations du client
                                </h3>
                                <div class="mt-4 space-y-2">
                                    <div>
                                        <span
                                            class="text-gray-500 dark:text-gray-400"
                                            >Nom:</span
                                        >
                                        <span
                                            class="text-gray-900 dark:text-gray-100"
                                        >
                                            {{ ticket.client.name }}
                                            {{ ticket.client.prenom }}
                                        </span>
                                    </div>
                                    <div>
                                        <span
                                            class="text-gray-500 dark:text-gray-400"
                                            >Email:</span
                                        >
                                        <span
                                            class="text-gray-900 dark:text-gray-100"
                                        >
                                            {{ ticket.client.email }}
                                        </span>
                                    </div>
                                    <div>
                                        <span
                                            class="text-gray-500 dark:text-gray-400"
                                            >Téléphone:</span
                                        >
                                        <span
                                            class="text-gray-900 dark:text-gray-100"
                                        >
                                            {{ ticket.client.telephone }}
                                        </span>
                                    </div>
                                    <div>
                                        <span
                                            class="text-gray-500 dark:text-gray-400"
                                            >Adresse:</span
                                        >
                                        <p
                                            class="text-gray-900 dark:text-gray-100"
                                        >
                                            {{ ticket.client.addresse }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div v-if="ticket.technicien">
                                <h3
                                    class="text-lg font-medium text-gray-900 dark:text-gray-100"
                                >
                                    Technicien assigné
                                </h3>
                                <div class="mt-4">
                                    <span
                                        class="text-gray-900 dark:text-gray-100"
                                    >
                                        {{ ticket.technicien.name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section des interventions -->
        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white dark:bg-zinc-800 overflow-hidden shadow-xl sm:rounded-lg p-6"
                >
                    <div class="flex justify-between items-center mb-6">
                        <h3
                            class="text-lg font-medium text-gray-900 dark:text-gray-100"
                        >
                            Interventions
                        </h3>
                        <button
                            @click="showInterventionForm = true"
                            class="px-4 py-2 bg-indigo-600 dark:bg-indigo-500 text-white rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-600 transition"
                        >
                            Nouvelle intervention
                        </button>
                    </div>

                    <!-- Formulaire d'intervention -->
                    <div
                        v-if="showInterventionForm"
                        class="mb-6 p-4 border border-gray-200 dark:border-zinc-700 rounded-lg"
                    >
                        <form
                            @submit.prevent="submitIntervention"
                            class="space-y-4"
                        >
                            <div>
                                <InputLabel
                                    value="Titre"
                                    class="dark:text-gray-200"
                                />
                                <TextInput
                                    v-model="interventionForm.titre"
                                    type="text"
                                    class="mt-1 block w-full border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required
                                />
                            </div>

                            <div>
                                <InputLabel
                                    value="Description"
                                    class="dark:text-gray-200"
                                />
                                <textarea
                                    v-model="interventionForm.description"
                                    rows="3"
                                    class="mt-1 block w-full border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required
                                ></textarea>
                            </div>

                            <div>
                                <InputLabel
                                    value="Technicien"
                                    class="dark:text-gray-200"
                                />
                                <select
                                    v-model="interventionForm.technicien_id"
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

                            <div class="flex justify-end space-x-3">
                                <SecondaryButton
                                    @click="showInterventionForm = false"
                                >
                                    Annuler
                                </SecondaryButton>
                                <PrimaryButton
                                    type="submit"
                                    :disabled="processing"
                                >
                                    Ajouter l'intervention
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>

                    <!-- Liste des interventions -->
                    <div class="space-y-4">
                        <div
                            v-for="intervention in ticket.interventions"
                            :key="intervention.id"
                            class="p-4 border border-gray-200 dark:border-zinc-700 rounded-lg"
                        >
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4
                                        class="font-medium text-gray-900 dark:text-gray-100"
                                    >
                                        {{ intervention.titre }}
                                    </h4>
                                    <p
                                        class="mt-1 text-gray-600 dark:text-gray-400"
                                    >
                                        {{ intervention.description }}
                                    </p>
                                    <!-- Ajout de l'information du technicien -->
                                    <div class="mt-2 text-sm">
                                        <span
                                            class="text-gray-500 dark:text-gray-400"
                                            >Technicien:
                                        </span>
                                        <span
                                            class="text-gray-700 dark:text-gray-300"
                                        >
                                            {{
                                                intervention.technicien
                                                    ? intervention.technicien
                                                          .name
                                                    : "Non assigné"
                                            }}
                                        </span>
                                    </div>
                                </div>
                                <span
                                    :class="getStatusColor(intervention.statut)"
                                    class="px-2 py-1 rounded-full text-xs font-medium"
                                >
                                    {{ intervention.statut }}
                                </span>
                            </div>
                            <div
                                class="mt-2 text-sm text-gray-500 dark:text-gray-400"
                            >
                                {{
                                    new Date(
                                        intervention.created_at
                                    ).toLocaleDateString()
                                }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
