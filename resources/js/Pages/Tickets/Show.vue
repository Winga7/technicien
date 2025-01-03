<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";
import { ref, onMounted, onUnmounted, computed } from "vue";
import { useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import { router } from "@inertiajs/vue3";

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
const editingIntervention = ref(null);
const processing = ref(false);
const imagePreview = ref([]);
const showEditForm = ref(false);
const interventionPreview = ref([]);
const ticketImagePreview = ref([]);

const interventionForm = useForm({
    titre: "",
    description: "",
    ticket_id: props.ticket.id,
    client_id: props.ticket.client_id,
    techniciens: [],
    statut: "en attente",
    images: [],
});

const editForm = useForm({
    titre: props.ticket.titre,
    description: props.ticket.description,
    statut: props.ticket.statut,
    client_id: props.ticket.client_id,
    images: [],
});

const getTechniciens = computed(() => {
    // Récupérer tous les techniciens des interventions
    const techniciens = props.ticket.interventions
        .flatMap((intervention) => intervention.techniciens)
        .filter((technicien) => technicien !== null);

    // Supprimer les doublons en utilisant les IDs
    return [...new Map(techniciens.map((tech) => [tech.id, tech])).values()];
});

const submitIntervention = () => {
    const formData = new FormData();
    formData.append('titre', interventionForm.titre);
    formData.append('description', interventionForm.description);
    formData.append('ticket_id', props.ticket.id);
    formData.append('client_id', props.ticket.client_id);
    formData.append('statut', interventionForm.statut || 'en cours');

    if (interventionForm.techniciens) {
        interventionForm.techniciens.forEach(techId => {
            formData.append('techniciens[]', techId);
        });
    }

    if (interventionForm.images && interventionForm.images.length > 0) {
        interventionForm.images.forEach((image) => {
            formData.append('images[]', image);
        });
    }

    router.post(route('interventions.store'), formData, {
        preserveScroll: true,
        onSuccess: () => {
            showInterventionForm.value = false;
            interventionForm.reset();
            interventionPreview.value = [];
        },
    });
};

const getStatusColor = (statut) => {
    switch (statut) {
        case "en attente":
            return "text-yellow-500";
        case "en cours":
            return "text-blue-500";
        case "terminé":
            return "text-green-500";
        default:
            return "text-gray-500";
    }
};

// Ajouter cette fonction pour gérer l'upload
const handleImageUpload = (e) => {
    const files = Array.from(e.target.files);
    interventionForm.images = []; // Réinitialiser les images
    imagePreview.value = []; // Réinitialiser les previews

    files.forEach((file) => {
        interventionForm.images.push(file);
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value.push(e.target.result);
        };
        reader.readAsDataURL(file);
    });
};

// Ajouter cette fonction pour supprimer une image
const removeImage = (index) => {
    imagePreview.value.splice(index, 1);
    interventionForm.images.splice(index, 1);
};

// Pour les interventions
const handleInterventionImageUpload = (e) => {
    const files = Array.from(e.target.files);
    interventionForm.images = [];
    interventionPreview.value = []; // Nouveau ref pour les previews d'intervention

    files.forEach((file) => {
        interventionForm.images.push(file);
        const reader = new FileReader();
        reader.onload = (e) => {
            interventionPreview.value.push(e.target.result);
        };
        reader.readAsDataURL(file);
    });
};

const removeInterventionImage = (index) => {
    interventionPreview.value.splice(index, 1);
    interventionForm.images.splice(index, 1);
};

// Pour le ticket
const handleEditImageUpload = (e) => {
    const files = Array.from(e.target.files);
    editForm.images = files;
    ticketImagePreview.value = []; // Nouveau ref pour les previews de ticket

    files.forEach((file) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            ticketImagePreview.value.push(e.target.result);
        };
        reader.readAsDataURL(file);
    });
};

const removeTicketImage = (index) => {
    ticketImagePreview.value.splice(index, 1);
    const newImages = [...editForm.images];
    newImages.splice(index, 1);
    editForm.images = newImages;
};

const activeMenu = ref(null);

const toggleMenu = (interventionId) => {
    activeMenu.value =
        activeMenu.value === interventionId ? null : interventionId;
};

const updateIntervention = (intervention) => {
    editingIntervention.value = intervention;
    editForm.titre = intervention.titre;
    editForm.description = intervention.description;
    editForm.statut = intervention.statut;
    editForm.techniciens = intervention.techniciens.map(tech => tech.id);
    editForm.images = [];
    interventionPreview.value = [];
    activeMenu.value = null;
};

const submitEdit = () => {
    const formData = new FormData();
    formData.append('_method', 'PUT');
    formData.append('titre', editForm.titre);
    formData.append('description', editForm.description);
    formData.append('technicien_id', editForm.technicien_id);

    if (editForm.images && editForm.images.length > 0) {
        editForm.images.forEach((image) => {
            formData.append('images[]', image);
        });
    }

    router.post(route('interventions.update', editingIntervention.value.id), formData, {
        preserveScroll: true,
        onSuccess: () => {
            editingIntervention.value = null;
            editForm.reset();
            interventionPreview.value = [];
        },
    });
};

const resetEditForm = () => {
    showEditForm.value = false;
    editForm.reset();
    imagePreview.value = [];
};

const cancelEdit = () => {
    editingIntervention.value = null;
    editForm.reset();
};

const deleteIntervention = (interventionId) => {
    if (confirm("Êtes-vous sûr de vouloir supprimer cette intervention ?")) {
        router.delete(route("interventions.destroy", interventionId), {
            preserveScroll: true,
            onSuccess: () => {
                activeMenu.value = null;
            },
        });
    }
};

// Fermer le menu si on clique ailleurs
const closeMenu = (e) => {
    if (!e.target.closest(".intervention-menu")) {
        activeMenu.value = null;
    }
};

onMounted(() => {
    document.addEventListener("click", closeMenu);
});

onUnmounted(() => {
    document.removeEventListener("click", closeMenu);
});

const submitInterventionEdit = () => {
    const formData = new FormData();
    formData.append('_method', 'PUT');
    formData.append('titre', editForm.titre);
    formData.append('description', editForm.description);
    formData.append('statut', editForm.statut);

    if (editForm.techniciens) {
        editForm.techniciens.forEach(techId => {
            formData.append('techniciens[]', techId);
        });
    }

    if (editForm.images && editForm.images.length > 0) {
        editForm.images.forEach((image) => {
            formData.append('images[]', image);
        });
    }

    router.post(route('interventions.update', editingIntervention.value.id), formData, {
        headers: {
            'Content-Type': 'multipart/form-data',
        },
        preserveScroll: true,
        onSuccess: () => {
            editingIntervention.value = null;
            editForm.reset();
            interventionPreview.value = [];
            activeMenu.value = null;
        },
    });
};
</script>

<template>
    <AppLayout :title="'Ticket #' + ticket.id">
        <template #header>
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <span class="text-2xl" role="img" aria-label="ticket"
                        >🎫</span
                    >
                    <h2
                        class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
                    >
                        Ticket #{{ ticket.id }} - {{ ticket.titre }}
                    </h2>
                </div>
                <div class="flex space-x-4">
                    <button
                        @click="showEditForm = true"
                        class="px-4 py-2 bg-yellow-600 dark:bg-yellow-500 text-white rounded-md hover:bg-yellow-700 dark:hover:bg-yellow-600 transition"
                    >
                        <span class="flex items-center space-x-2">
                            <span
                                class="text-lg"
                                role="img"
                                aria-label="modifier"
                                >✏️</span
                            >
                            <span>Modifier</span>
                        </span>
                    </button>
                    <Link
                        :href="route('tickets.index')"
                        class="px-4 py-2 bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-900 rounded-md hover:bg-gray-700 dark:hover:bg-gray-300 transition"
                    >
                        <span class="flex items-center space-x-2">
                            <span class="text-lg" role="img" aria-label="retour"
                                >↩️</span
                            >
                            <span>Retour aux tickets</span>
                        </span>
                    </Link>
                </div>
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
                                            >Créé le:
                                        </span>
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
                                        v-for="(image, index) in ticket.images"
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
                                            >Nom:
                                        </span>
                                        <span
                                            class="text-gray-900 dark:text-gray-100"
                                        >
                                            {{ ticket.client.name }}
                                            {{ ticket.client.firstname }}
                                        </span>
                                    </div>
                                    <div>
                                        <span
                                            class="text-gray-500 dark:text-gray-400"
                                            >Email:
                                        </span>
                                        <span
                                            class="text-gray-900 dark:text-gray-100"
                                        >
                                            {{ ticket.client.email }}
                                        </span>
                                    </div>
                                    <div>
                                        <span
                                            class="text-gray-500 dark:text-gray-400"
                                            >Téléphone:
                                        </span>
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

                            <div>
                                <h3
                                    class="text-lg font-medium text-gray-900 dark:text-gray-100"
                                >
                                    Techniciens assignés
                                </h3>
                                <div class="mt-4 space-y-2">
                                    <div v-if="getTechniciens.length > 0">
                                        <div
                                            v-for="technicien in getTechniciens"
                                            :key="technicien.id"
                                            class="flex items-center space-x-2 text-gray-900 dark:text-gray-100"
                                        >
                                            <span
                                                class="text-lg"
                                                role="img"
                                                aria-label="technicien"
                                                >👨‍🔧</span
                                            >
                                            <span>{{ technicien.name }}</span>
                                        </div>
                                    </div>
                                    <div
                                        v-else
                                        class="text-gray-500 dark:text-gray-400"
                                    >
                                        Aucun technicien assigné
                                    </div>
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

                    <!-- Formulaire de modification d'intervention -->
                    <div
                        v-if="editingIntervention"
                        class="mb-6 p-4 border border-gray-200 dark:border-zinc-700 rounded-lg"
                    >
                        <form @submit.prevent="submitInterventionEdit" class="space-y-4">
                            <div>
                                <InputLabel
                                    value="Titre"
                                    class="dark:text-gray-200"
                                />
                                <TextInput
                                    v-model="editForm.titre"
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
                                    v-model="editForm.description"
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
                                    v-model="editForm.techniciens"
                                    multiple
                                    class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                >
                                    <option
                                        v-for="technicien in techniciens"
                                        :key="technicien.id"
                                        :value="technicien.id"
                                    >
                                        {{ technicien.name }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <InputLabel value="Images" class="dark:text-gray-200" />
                                <input
                                    type="file"
                                    @change="handleEditImageUpload"
                                    multiple
                                    accept="image/*"
                                    class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                />
                                <div
                                    v-if="imagePreview.length"
                                    class="mt-2 grid grid-cols-3 gap-4"
                                >
                                    <div
                                        v-for="(preview, index) in imagePreview"
                                        :key="index"
                                        class="relative"
                                    >
                                        <img
                                            :src="preview"
                                            class="w-full h-32 object-cover rounded-lg"
                                        />
                                        <button
                                            @click.prevent="removeImage(index)"
                                            class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center"
                                        >
                                            ×
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-end space-x-3">
                                <SecondaryButton @click="cancelEdit">
                                    Annuler
                                </SecondaryButton>
                                <PrimaryButton
                                    type="submit"
                                    :disabled="editForm.processing"
                                >
                                    Sauvegarder les modifications
                                </PrimaryButton>
                            </div>
                        </form>
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
                                    class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
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
                                    class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    required
                                ></textarea>
                            </div>

                            <div>
                                <InputLabel
                                    value="Techniciens"
                                    class="dark:text-gray-200"
                                />
                                <select
                                    v-model="interventionForm.techniciens"
                                    multiple
                                    class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                >
                                    <option
                                        v-for="technicien in techniciens"
                                        :key="technicien.id"
                                        :value="technicien.id"
                                    >
                                        {{ technicien.name }}
                                    </option>
                                </select>
                                <p
                                    class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                                >
                                    Maintenez Ctrl (Windows) ou Cmd (Mac) pour
                                    sélectionner plusieurs techniciens
                                </p>
                            </div>

                            <!-- Ajouter le champ pour l'upload des images -->
                            <div>
                                <InputLabel
                                    value="Images"
                                    class="dark:text-gray-200"
                                />
                                <input
                                    type="file"
                                    @change="handleInterventionImageUpload"
                                    multiple
                                    accept="image/*"
                                    class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
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
                                            :alt="'Image ' + (index + 1)"
                                            class="w-full h-32 object-cover rounded"
                                        />
                                        <button
                                            @click.prevent="removeImage(index)"
                                            class="absolute top-0 right-0 p-1 bg-red-500 text-white rounded-full"
                                        >
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                class="h-4 w-4"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"
                                                />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
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
                                            >Techniciens:
                                        </span>
                                        <div class="flex flex-wrap gap-2 mt-1">
                                            <span
                                                v-if="
                                                    intervention.techniciens &&
                                                    intervention.techniciens
                                                        .length > 0
                                                "
                                                v-for="tech in intervention.techniciens"
                                                :key="tech.id"
                                                class="text-gray-700 dark:text-gray-300"
                                            >
                                                {{ tech.name }}
                                            </span>
                                            <span
                                                v-else
                                                class="text-gray-700 dark:text-gray-300"
                                            >
                                                Non assigné
                                            </span>
                                        </div>
                                    </div>
                                    <!-- Ajout de l'affichage des images -->
                                    <div
                                        v-if="intervention.images"
                                        class="mt-4"
                                    >
                                        <div class="grid grid-cols-3 gap-4">
                                            <img
                                                v-for="(
                                                    image, index
                                                ) in intervention.images"
                                                :key="index"
                                                :src="`/storage/${image}`"
                                                :alt="'Image ' + (index + 1)"
                                                class="w-full h-32 object-cover rounded-lg"
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div class="intervention-menu relative">
                                    <button
                                        @click.stop="
                                            toggleMenu(intervention.id)
                                        "
                                        class="p-1 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded-full"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            class="h-5 w-5 text-gray-500 dark:text-gray-400"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                        >
                                            <path
                                                d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"
                                            />
                                        </svg>
                                    </button>

                                    <!-- Menu déroulant -->
                                    <div
                                        v-if="activeMenu === intervention.id"
                                        class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-zinc-700 ring-1 ring-black ring-opacity-5 z-10"
                                    >
                                        <div class="py-1">
                                            <button
                                                @click="
                                                    updateIntervention(
                                                        intervention
                                                    )
                                                "
                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-zinc-600"
                                            >
                                                Modifier
                                            </button>
                                            <button
                                                @click="
                                                    deleteIntervention(
                                                        intervention.id
                                                    )
                                                "
                                                class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-zinc-600"
                                            >
                                                Supprimer
                                            </button>
                                        </div>
                                    </div>
                                </div>
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

        <!-- Modal d'édition du ticket -->
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
                        Modifier le Ticket #{{ ticket.id }}
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
                        <InputLabel
                            class="dark:text-gray-200"
                            value="Titre du ticket"
                        />
                        <TextInput
                            v-model="editForm.titre"
                            type="text"
                            class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            required
                        />
                    </div>

                    <div>
                        <InputLabel
                            class="dark:text-gray-200"
                            value="Description"
                        />
                        <textarea
                            v-model="editForm.description"
                            class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            rows="4"
                            required
                        ></textarea>
                    </div>

                    <div>
                        <InputLabel class="dark:text-gray-200" value="Statut" />
                        <select
                            v-model="editForm.statut"
                            class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                        >
                            <option value="en attente">En attente</option>
                            <option value="en cours">En cours</option>
                            <option value="terminé">Terminé</option>
                        </select>
                    </div>

                    <div>
                        <InputLabel class="dark:text-gray-200" value="Images" />
                        <input
                            type="file"
                            @change="handleEditImageUpload"
                            multiple
                            accept="image/*"
                            class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100"
                        />
                        <div
                            v-if="imagePreview.length"
                            class="mt-2 grid grid-cols-3 gap-4"
                        >
                            <div
                                v-for="(preview, index) in imagePreview"
                                :key="index"
                                class="relative"
                            >
                                <img
                                    :src="preview"
                                    class="w-full h-32 object-cover rounded-lg"
                                />
                                <button
                                    @click.prevent="removeImage(index)"
                                    class="absolute top-0 right-0 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center"
                                >
                                    ×
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button
                            type="button"
                            @click="resetEditForm"
                            class="px-4 py-2 bg-gray-300 dark:bg-zinc-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-400 dark:hover:bg-zinc-500 transition"
                        >
                            Annuler
                        </button>
                        <button
                            type="submit"
                            class="px-4 py-2 bg-indigo-600 dark:bg-indigo-500 text-white rounded-md hover:bg-indigo-700 dark:hover:bg-indigo-600 transition"
                        >
                            Mettre à jour
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
