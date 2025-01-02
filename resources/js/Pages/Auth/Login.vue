<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import ThemeSwitch from '@/Components/ThemeSwitch.vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Connexion à l'application" />

    <div class="flex items-center justify-center min-h-screen bg-gray-100 dark:bg-zinc-900">
        <div class="w-full max-w-md p-6">
            <div class="absolute top-2 right-2 md:top-4 md:right-4">
                <ThemeSwitch />
            </div>

            <div class="bg-white dark:bg-zinc-800 shadow-md overflow-hidden sm:rounded-lg p-6">
                <div class="flex justify-center mb-6">
                    <AuthenticationCardLogo />
                </div>

                <div v-if="status" class="mb-4 font-medium text-sm text-green-600 dark:text-green-400 p-2 bg-green-50 dark:bg-green-900/20 rounded">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" aria-label="Formulaire de connexion">
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

                    <div class="mt-4">
                        <InputLabel for="password" value="Mot de passe" class="text-gray-700 dark:text-gray-300" />
                        <TextInput
                            id="password"
                            v-model="form.password"
                            type="password"
                            class="mt-1 block w-full bg-white dark:bg-zinc-800 border-gray-300 dark:border-zinc-600 text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                            required
                            autocomplete="current-password"
                        />
                        <InputError class="mt-2 text-red-600 dark:text-red-400" :message="form.errors.password" />
                    </div>

                    <div class="block mt-4">
                        <label class="flex items-center">
                            <Checkbox v-model:checked="form.remember" name="remember" />
                            <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Se souvenir de moi</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100"
                        >
                            Mot de passe oublié ?
                        </Link>

                        <PrimaryButton
                            class="ms-4 bg-indigo-600 dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-600 text-white"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Se connecter
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
