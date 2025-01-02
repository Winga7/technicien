<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import ThemeSwitch from '@/Components/ThemeSwitch.vue';

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Mot de passe oublié" />

    <div class="flex items-center justify-center min-h-screen bg-gray-100 dark:bg-zinc-900">
        <div class="w-full max-w-md p-6">
            <div class="absolute top-4 right-4">
                <ThemeSwitch />
            </div>

            <div class="bg-white dark:bg-zinc-800 shadow-md overflow-hidden sm:rounded-lg p-6">
                <div class="flex justify-center mb-6">
                    <AuthenticationCardLogo />
                </div>

                <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                    Mot de passe oublié ? Pas de problème. Il vous suffit de nous donner votre adresse email et nous vous enverrons un lien de réinitialisation de mot de passe qui vous permettra de choisir un nouveau mot de passe.
                </div>

                <div v-if="status" class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                    {{ status }}
                </div>

                <form @submit.prevent="submit">
                    <div>
                        <InputLabel for="email" value="Email" class="text-gray-700 dark:text-gray-300" />
                        <TextInput
                            id="email"
                            v-model="form.email"
                            type="email"
                            class="mt-1 block w-full bg-white dark:bg-zinc-800 border-gray-300 dark:border-zinc-600 text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            required
                            autofocus
                            autocomplete="username"
                        />
                        <InputError class="mt-2 text-red-600 dark:text-red-400" :message="form.errors.email" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <PrimaryButton
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                            class="bg-indigo-600 dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-600 text-white"
                        >
                            Envoyer le lien de réinitialisation
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
