<script setup>
import { ref } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import ActionMessage from "@/Components/ActionMessage.vue";
import FormSection from "@/Components/FormSection.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import TextInput from "@/Components/TextInput.vue";

const props = defineProps({
    user: Object,
});

const form = useForm({
    _method: "PUT",
    name: props.user.name,
    firstname: props.user.firstname,
    email: props.user.email,
    telephone: props.user.telephone,
    show_phone: props.user.show_phone ?? false,
    photo: null,
});

const verificationLinkSent = ref(null);
const photoPreview = ref(null);
const photoInput = ref(null);

const isValidPhone = (phone) => {
    // Format belge: +32 XXX XX XX XX ou 0X XXX XX XX
    return /^(?:\+32|0)(?:\s?\d{1,2}\s?\d{2,3}\s?\d{2}\s?\d{2})$/.test(phone);
};

const updateProfileInformation = () => {
    form.clearErrors();

    // Validation du nom
    if (!form.name?.trim()) {
        form.setError('name', 'Le nom est obligatoire');
        return;
    }
    if (!isValidName(form.name)) {
        form.setError('name', 'Le nom doit contenir entre 2 et 50 caractères et ne peut contenir que des lettres, espaces, tirets et apostrophes');
        return;
    }

    // Validation du prénom
    if (!form.firstname?.trim()) {
        form.setError('firstname', 'Le prénom est obligatoire');
        return;
    }
    if (!isValidName(form.firstname)) {
        form.setError('firstname', 'Le prénom doit contenir entre 2 et 50 caractères et ne peut contenir que des lettres, espaces, tirets et apostrophes');
        return;
    }

    // Validation de l'email
    if (!form.email?.trim()) {
        form.setError('email', 'L\'email est obligatoire');
        return;
    }
    if (!isValidEmail(form.email)) {
        form.setError('email', 'L\'email n\'est pas valide');
        return;
    }

    // Validation du téléphone (si renseigné)
    if (form.telephone && !isValidPhone(form.telephone)) {
        form.setError('telephone', 'Le numéro de téléphone doit être au format belge (ex: +32 470 12 34 56 ou 0470 12 34 56)');
        return;
    }

    if (photoInput.value) {
        form.photo = photoInput.value.files[0];
    }

    form.post(route("user-profile-information.update"), {
        errorBag: "updateProfileInformation",
        preserveScroll: true,
        onSuccess: () => clearPhotoFileInput(),
    });
};

const isValidName = (name) => {
    return /^[a-zA-ZÀ-ÿ\s'-]{2,50}$/.test(name);
};

const isValidEmail = (email) => {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
};

const sendEmailVerification = () => {
    verificationLinkSent.value = true;
};

const selectNewPhoto = () => {
    photoInput.value.click();
};

const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];

    if (!photo) return;

    const reader = new FileReader();

    reader.onload = (e) => {
        photoPreview.value = e.target.result;
    };

    reader.readAsDataURL(photo);
};

const deletePhoto = () => {
    router.delete(route("current-user-photo.destroy"), {
        preserveScroll: true,
        onSuccess: () => {
            photoPreview.value = null;
            clearPhotoFileInput();
        },
    });
};

const clearPhotoFileInput = () => {
    if (photoInput.value?.value) {
        photoInput.value.value = null;
    }
};
</script>

<template>
    <FormSection
        @submitted="updateProfileInformation"
        enctype="multipart/form-data"
    >
        <template #title>
            <span class="text-gray-900 dark:text-gray-100">
                Informations du profil
            </span>
        </template>

        <template #description>
            <span class="text-gray-600 dark:text-gray-400">
                Mettez à jour vos informations de profil et votre adresse
                e-mail.
            </span>
        </template>

        <template #form>
            <!-- Profile Photo -->
            <div
                v-if="$page.props.jetstream.managesProfilePhotos"
                class="col-span-6 sm:col-span-4"
            >
                <input
                    id="photo"
                    ref="photoInput"
                    type="file"
                    class="hidden"
                    @change="updatePhotoPreview"
                />

                <InputLabel
                    for="photo"
                    value="Photo"
                    class="text-gray-700 dark:text-gray-300"
                />

                <!-- Current Profile Photo -->
                <div v-show="!photoPreview" class="mt-2">
                    <img
                        :src="user.profile_photo_url"
                        :alt="user.name"
                        class="rounded-full size-20 object-cover"
                    />
                </div>

                <!-- New Profile Photo Preview -->
                <div v-show="photoPreview" class="mt-2">
                    <span
                        class="block rounded-full size-20 bg-cover bg-no-repeat bg-center"
                        :style="
                            'background-image: url(\'' + photoPreview + '\');'
                        "
                    />
                </div>

                <SecondaryButton
                    class="mt-2 me-2 bg-white dark:bg-zinc-800 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-zinc-600"
                    type="button"
                    @click.prevent="selectNewPhoto"
                >
                    Selectionner une nouvelle photo
                </SecondaryButton>

                <SecondaryButton
                    v-if="user.profile_photo_path"
                    type="button"
                    class="mt-2 bg-white dark:bg-zinc-800 text-gray-700 dark:text-gray-300 border-gray-300 dark:border-zinc-600"
                    @click.prevent="deletePhoto"
                >
                    Supprimer la photo
                </SecondaryButton>

                <InputError :message="form.errors.photo" class="mt-2" />
            </div>

            <!-- Name -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel
                    for="name"
                    value="Nom"
                    class="text-gray-700 dark:text-gray-300"
                />
                <TextInput
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="mt-1 block w-full bg-white dark:bg-zinc-900 border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-gray-100"
                    required
                    autocomplete="name"
                />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>

            <!-- Firstname -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel
                    for="firstname"
                    value="Prénom"
                    class="text-gray-700 dark:text-gray-300"
                />
                <TextInput
                    id="firstname"
                    v-model="form.firstname"
                    type="text"
                    class="mt-1 block w-full bg-white dark:bg-zinc-900 border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-gray-100"
                    required
                    autocomplete="firstname"
                />
                <InputError :message="form.errors.firstname" class="mt-2" />
            </div>

            <!-- Email -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel
                    for="email"
                    value="Email"
                    class="text-gray-700 dark:text-gray-300"
                />
                <TextInput
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="mt-1 block w-full bg-white dark:bg-zinc-900 border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-gray-100"
                    required
                    autocomplete="username"
                />
                <InputError :message="form.errors.email" class="mt-2" />

                <div
                    v-if="
                        $page.props.jetstream.hasEmailVerification &&
                        user.email_verified_at === null
                    "
                >
                    <p class="text-sm mt-2 text-gray-700 dark:text-gray-300">
                        Votre adresse e-mail n'est pas vérifiée.

                        <Link
                            :href="route('verification.send')"
                            method="post"
                            as="button"
                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-zinc-800"
                            @click.prevent="sendEmailVerification"
                        >
                            Cliquez ici pour ré-envoyer l'e-mail de
                            vérification.
                        </Link>
                    </p>

                    <div
                        v-show="verificationLinkSent"
                        class="mt-2 font-medium text-sm text-green-600 dark:text-green-400"
                    >
                        Un nouvel e-mail de vérification a été envoyé à votre
                        adresse e-mail.
                    </div>
                </div>
            </div>

            <!-- Telephone -->
            <div class="col-span-6 sm:col-span-4">
                <InputLabel
                    for="telephone"
                    value="Téléphone"
                    class="text-gray-700 dark:text-gray-300"
                />
                <TextInput
                    id="telephone"
                    v-model="form.telephone"
                    type="tel"
                    class="mt-1 block w-full bg-white dark:bg-zinc-900 border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-gray-100"
                    autocomplete="telephone"
                />
                <InputError :message="form.errors.telephone" class="mt-2" />
            </div>

            <!-- Show Phone -->
            <div v-if="$page.props.auth.user.role === 'admin'" class="col-span-6 sm:col-span-4">
                <div class="flex items-center">
                    <input
                        type="checkbox"
                        id="show_phone"
                        v-model="form.show_phone"
                        class="rounded border-gray-300 dark:border-zinc-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:bg-zinc-900"
                    />
                    <label for="show_phone" class="ml-2 text-gray-700 dark:text-gray-300">
                        Autoriser l'affichage de mon numéro dans la liste des utilisateurs
                    </label>
                </div>
            </div>
        </template>

        <template #actions>
            <ActionMessage
                :on="form.recentlySuccessful"
                class="me-3 text-gray-600 dark:text-gray-400"
            >
                Sauvegardé.
            </ActionMessage>

            <PrimaryButton
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
                class="bg-gray-800 dark:bg-gray-200 hover:bg-gray-700 dark:hover:bg-gray-300 text-white dark:text-gray-900"
            >
                Sauvegarder
            </PrimaryButton>
        </template>
    </FormSection>
</template>
