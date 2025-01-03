<script setup>
import { ref, onMounted, watch } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";

defineProps({
    clients: Array,
});

const isDark = ref(localStorage.getItem("theme") === "dark");
const showClientForm = ref(false);
const showTicketForm = ref(false);
const isNewClient = ref(false);
const imagePreview = ref([]);
const selectedImages = ref([]);

// Form pour client
const clientForm = useForm({
    name: "",
    prenom: "",
    email: "",
    telephone: "",
    addresse: "",
});

// Form pour ticket
const ticketForm = useForm({
    titre: "",
    description: "",
    client_id: "",
    client: null,
    statut: "en attente",
    images: [],
});

const handleImageUpload = (e) => {
    const files = Array.from(e.target.files);
    selectedImages.value = files;
    imagePreview.value = [];

    files.forEach(file => {
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value.push(e.target.result);
        };
        reader.readAsDataURL(file);
    });

    ticketForm.images = files;
};

const removeImage = (index) => {
    imagePreview.value.splice(index, 1);
    selectedImages.value.splice(index, 1);
    ticketForm.images = selectedImages.value;
};

const resetClientForm = () => {
    showClientForm.value = false;
    clientForm.reset();
};

const resetTicketForm = () => {
    showTicketForm.value = false;
    ticketForm.reset();
    isNewClient.value = false;
    imagePreview.value = [];
    selectedImages.value = [];
};

const submitClient = () => {
    clientForm.post(route("clients.store"), {
        onSuccess: () => {
            showClientForm.value = false;
            clientForm.reset();
        },
    });
};

const submitTicket = () => {
    if (isNewClient.value) {
        ticketForm.client_id = null;
    } else {
        ticketForm.client = null;
    }

    ticketForm.post(route('tickets.store'), {
        onSuccess: () => {
            showTicketForm.value = false;
            ticketForm.reset();
            isNewClient.value = false;
            imagePreview.value = [];
            selectedImages.value = [];
        },
        preserveScroll: true,
        onError: (errors) => {
            console.error(errors);
        }
    });
};

watch(isNewClient, (newValue) => {
    if (newValue) {
        ticketForm.client = {
            name: "",
            prenom: "",
            email: "",
            telephone: "",
            addresse: "",
        };
        ticketForm.client_id = "";
    } else {
        ticketForm.client = null;
    }
});

onMounted(() => {
    window.addEventListener("theme-changed", (e) => {
        isDark.value = e.detail;
    });
});
</script>

<template>
    <div class="bg-white dark:bg-zinc-900 text-gray-800 dark:text-white">
        <!-- Quick Actions -->
        <div
            class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 p-4 sm:p-6"
        >
            <button
                @click="showTicketForm = true"
                class="bg-gray-100 dark:bg-zinc-800 p-4 sm:p-6 rounded-lg hover:bg-gray-200 dark:hover:bg-zinc-700 transition text-left"
            >
                <div class="flex items-center space-x-3 mb-4">
                    <span
                        class="text-2xl"
                        role="img"
                        aria-label="nouveau ticket"
                        >➕</span
                    >
                    <h3 class="text-lg sm:text-xl font-semibold">
                        Nouveau ticket
                    </h3>
                </div>
                <p
                    class="text-sm sm:text-base text-gray-600 dark:text-gray-400"
                >
                    Créer un nouveau ticket
                </p>
            </button>

            <Link
                :href="route('tickets.index')"
                class="bg-gray-100 dark:bg-zinc-800 p-4 sm:p-6 rounded-lg hover:bg-gray-200 dark:hover:bg-zinc-700 transition"
            >
                <div class="flex items-center space-x-3 mb-4">
                    <span class="text-2xl" role="img" aria-label="tickets"
                        >🎫</span
                    >
                    <h3 class="text-lg sm:text-xl font-semibold">
                        Tickets en cours
                    </h3>
                </div>
                <p
                    class="text-sm sm:text-base text-gray-600 dark:text-gray-400"
                >
                    Voir la liste des tickets en cours
                </p>
            </Link>

            <button
                @click="showClientForm = true"
                class="bg-gray-100 dark:bg-zinc-800 p-4 sm:p-6 rounded-lg hover:bg-gray-200 dark:hover:bg-zinc-700 transition text-left"
            >
                <div class="flex items-center space-x-3 mb-4">
                    <span
                        class="text-2xl"
                        role="img"
                        aria-label="nouveau client"
                        >👥</span
                    >
                    <h3 class="text-lg sm:text-xl font-semibold">
                        Nouveau client
                    </h3>
                </div>
                <p
                    class="text-sm sm:text-base text-gray-600 dark:text-gray-400"
                >
                    Créer un nouveau client
                </p>
            </button>
        </div>

        <!-- Modal Formulaire Client -->
        <div
            v-if="showClientForm"
            class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50"
            @click.self="resetClientForm"
        >
            <div
                class="bg-white dark:bg-zinc-800 rounded-lg p-6 max-w-2xl w-full mx-4 relative"
            >
                <div class="flex justify-between items-center mb-4">
                    <h3
                        class="text-lg font-medium text-gray-900 dark:text-gray-100"
                    >
                        Nouveau Client
                    </h3>
                    <button
                        @click="resetClientForm"
                        class="text-gray-400 hover:text-gray-500 text-xl font-medium px-2 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded"
                    >
                        ×
                    </button>
                </div>

                <form @submit.prevent="submitClient" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel
                                class="dark:text-gray-200"
                                value="Nom"
                            />
                            <TextInput
                                v-model="clientForm.name"
                                type="text"
                                class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                required
                            />
                        </div>
                        <div>
                            <InputLabel
                                class="dark:text-gray-200"
                                value="Prénom"
                            />
                            <TextInput
                                v-model="clientForm.prenom"
                                type="text"
                                class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                required
                            />
                        </div>
                    </div>

                    <div>
                        <InputLabel class="dark:text-gray-200" value="Email" />
                        <TextInput
                            v-model="clientForm.email"
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
                            v-model="clientForm.telephone"
                            type="tel"
                            class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            required
                        />
                    </div>

                    <div>
                        <InputLabel
                            class="dark:text-gray-200"
                            value="Adresse"
                        />
                        <TextInput
                            v-model="clientForm.addresse"
                            type="text"
                            class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                            required
                        />
                    </div>

                    <div class="flex justify-end space-x-3">
                        <SecondaryButton
                            @click.prevent="resetClientForm"
                            class="dark:bg-zinc-700 dark:text-gray-300 dark:hover:bg-zinc-600"
                        >
                            Annuler
                        </SecondaryButton>
                        <PrimaryButton
                            type="submit"
                            :disabled="clientForm.processing"
                            class="bg-indigo-600 dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-600"
                        >
                            Créer le client
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Formulaire Ticket -->
        <div
            v-if="showTicketForm"
            class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 p-4 overflow-y-auto"
            @click.self="resetTicketForm"
        >
            <div class="bg-white dark:bg-zinc-800 rounded-lg p-6 w-full max-w-2xl my-8 relative">
                <div class="flex justify-between items-center mb-4 sticky top-0 bg-white dark:bg-zinc-800 z-10">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                        Nouveau Ticket
                    </h3>
                    <button
                        @click="resetTicketForm"
                        class="text-gray-400 hover:text-gray-500 text-xl font-medium px-2 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded"
                    >
                        ×
                    </button>
                </div>

                <!-- Contenu scrollable -->
                <div class="max-h-[calc(100vh-8rem)] overflow-y-auto">
                    <form @submit.prevent="submitTicket" class="space-y-4">
                        <!-- Type de client -->
                        <div>
                            <InputLabel class="dark:text-gray-200" value="Type de client" />
                            <div class="flex space-x-4 mt-2">
                                <label class="inline-flex items-center">
                                    <input
                                        type="radio"
                                        v-model="isNewClient"
                                        :value="false"
                                        class="form-radio border-gray-200 dark:border-zinc-700 text-indigo-600 dark:bg-zinc-900 focus:ring-indigo-500"
                                    />
                                    <span class="ml-2 text-gray-700 dark:text-gray-200">Client existant</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input
                                        type="radio"
                                        v-model="isNewClient"
                                        :value="true"
                                        class="form-radio border-gray-200 dark:border-zinc-700 text-indigo-600 dark:bg-zinc-900 focus:ring-indigo-500"
                                    />
                                    <span class="ml-2 text-gray-700 dark:text-gray-200">Nouveau client</span>
                                </label>
                            </div>
                        </div>

                        <!-- Sélection du client existant -->
                        <div v-if="!isNewClient">
                            <InputLabel class="dark:text-gray-200" value="Client" />
                            <select
                                v-model="ticketForm.client_id"
                                class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                required
                            >
                                <option value="">Sélectionner un client</option>
                                <option v-for="client in clients" :key="client.id" :value="client.id">
                                    {{ client.name }} {{ client.prenom }}
                                </option>
                            </select>
                        </div>

                        <!-- Formulaire nouveau client -->
                        <div v-else class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <InputLabel class="dark:text-gray-200" value="Nom" />
                                    <TextInput
                                        v-model="ticketForm.client.name"
                                        type="text"
                                        class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        required
                                    />
                                </div>
                                <div>
                                    <InputLabel class="dark:text-gray-200" value="Prénom" />
                                    <TextInput
                                        v-model="ticketForm.client.prenom"
                                        type="text"
                                        class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        required
                                    />
                                </div>
                            </div>

                            <div>
                                <InputLabel class="dark:text-gray-200" value="Email" />
                                <TextInput
                                    v-model="ticketForm.client.email"
                                    type="email"
                                    class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    required
                                />
                            </div>

                            <div>
                                <InputLabel class="dark:text-gray-200" value="Téléphone" />
                                <TextInput
                                    v-model="ticketForm.client.telephone"
                                    type="tel"
                                    class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    required
                                />
                            </div>

                            <div>
                                <InputLabel class="dark:text-gray-200" value="Adresse" />
                                <TextInput
                                    v-model="ticketForm.client.addresse"
                                    type="text"
                                    class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    required
                                />
                            </div>
                        </div>

                        <!-- Détails du ticket -->
                        <div>
                            <InputLabel class="dark:text-gray-200" value="Titre du ticket" />
                            <TextInput
                                v-model="ticketForm.titre"
                                type="text"
                                class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                required
                            />
                        </div>

                        <div>
                            <InputLabel class="dark:text-gray-200" value="Description" />
                            <textarea
                                v-model="ticketForm.description"
                                class="mt-1 block w-full border-gray-300 dark:border-zinc-700 dark:bg-zinc-900 dark:text-gray-100 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                rows="4"
                                required
                            ></textarea>
                        </div>

                        <!-- Images -->
                        <div>
                            <InputLabel class="dark:text-gray-200" value="Images" />
                            <input
                                type="file"
                                @change="handleImageUpload"
                                multiple
                                accept="image/*"
                                class="mt-1 block w-full text-gray-700 dark:text-gray-200"
                            />
                            <div v-if="imagePreview.length" class="mt-2 grid grid-cols-3 gap-4">
                                <div v-for="(preview, index) in imagePreview" :key="index" class="relative">
                                    <img :src="preview" class="w-full h-32 object-cover rounded-lg" />
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
                            <SecondaryButton
                                @click.prevent="resetTicketForm"
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
        </div>
    </div>
</template>
