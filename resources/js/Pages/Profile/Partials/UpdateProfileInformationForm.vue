<script setup>
import { ref, computed, watch } from "vue";
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
    nom: props.user.nom,
    email: props.user.email,
    telephone: props.user.telephone,
    photo: null,
});

watch(
    () => props.user,
    (newUser) => {
        form.nom = newUser.nom;
        form.email = newUser.email;
        form.telephone = newUser.telephone ?? "";
    },
    { deep: true }
);

watch(
    () => props.user.telephone,
    (newTelephone) => {
        form.telephone = newTelephone ?? "";
    },
    { immediate: true }
);

const verificationLinkSent = ref(null);
const photoPreview = ref(null);
const photoInput = ref(null);

const updateProfileInformation = () => {
    if (photoInput.value?.files?.length > 0) {
        const photo = photoInput.value.files[0];
        const formData = new FormData();

        formData.append("_method", "PUT");
        formData.append("nom", form.nom);
        formData.append("email", form.email);
        formData.append("telephone", form.telephone || "");
        formData.append("photo", photo, photo.name);

        router.post(route("user-profile-information.update"), formData, {
            preserveScroll: true,
            headers: {
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
                "Content-Type": "multipart/form-data",
            },
            onSuccess: () => {
                clearPhotoFileInput();
                form.recentlySuccessful = true;
                photoPreview.value = null;
                window.location.reload();
            },
            onError: (errors) => {
                console.error("Upload errors:", errors);
            },
        });
    } else {
        form.put(route("user-profile-information.update"));
    }
};

const sendEmailVerification = () => {
    verificationLinkSent.value = true;
};

const selectNewPhoto = () => {
    photoInput.value?.click();
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

const getUserInitials = computed(() => {
    const names = props.user.nom.split(" ");
    return names
        .map((name) => name.charAt(0).toUpperCase())
        .join("")
        .slice(0, 2);
});
</script>

<template>
    <FormSection @submitted="updateProfileInformation">
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
                    <template v-if="user.profile_photo_url">
                        <img
                            :src="user.profile_photo_url"
                            :alt="user.name"
                            class="rounded-full size-20 object-cover"
                        />
                    </template>
                    <template v-else>
                        <div
                            class="rounded-full size-20 flex items-center justify-center bg-indigo-600 dark:bg-indigo-500 text-white font-medium text-xl"
                        >
                            {{ getUserInitials }}
                        </div>
                    </template>
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
                    for="nom"
                    value="Nom"
                    class="text-gray-700 dark:text-gray-300"
                />
                <TextInput
                    id="nom"
                    v-model="form.nom"
                    type="text"
                    class="mt-1 block w-full bg-white dark:bg-zinc-900 border-gray-300 dark:border-zinc-700 text-gray-900 dark:text-gray-100"
                    required
                    autocomplete="name"
                />
                <InputError :message="form.errors.nom" class="mt-2" />
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
