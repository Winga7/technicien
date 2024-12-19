<script setup>
import { useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    telephone: props.user.telephone || "",
    role: props.user.role,
});

const submit = () => {
    form.put(route("users.update", props.user.id), {
        onSuccess: () => {
            // La redirection sera gérée par le contrôleur
        },
    });
};
</script>

<template>
    <AppLayout title="Modifier l'utilisateur">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Modifier l'utilisateur
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-zinc-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <form @submit.prevent="submit">
                        <div class="mb-6">
                            <InputLabel for="name" value="Nom" class="text-gray-700 dark:text-gray-300" />
                            <TextInput
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="mt-1 block w-full bg-white dark:bg-zinc-800 border-gray-300 dark:border-zinc-600 text-gray-900 dark:text-gray-100"
                                required
                                autofocus
                            />
                            <InputError :message="form.errors.name" class="mt-2" />
                        </div>

                        <div class="mb-6">
                            <InputLabel for="email" value="Email" class="text-gray-700 dark:text-gray-300" />
                            <TextInput
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="mt-1 block w-full bg-white dark:bg-zinc-800 border-gray-300 dark:border-zinc-600 text-gray-900 dark:text-gray-100"
                                required
                            />
                            <InputError :message="form.errors.email" class="mt-2" />
                        </div>

                        <div class="mb-6">
                            <InputLabel for="telephone" value="Téléphone" class="text-gray-700 dark:text-gray-300" />
                            <TextInput
                                id="telephone"
                                v-model="form.telephone"
                                type="tel"
                                class="mt-1 block w-full bg-white dark:bg-zinc-800 border-gray-300 dark:border-zinc-600 text-gray-900 dark:text-gray-100"
                            />
                            <InputError :message="form.errors.telephone" class="mt-2" />
                        </div>

                        <div class="mb-6">
                            <InputLabel for="role" value="Rôle" class="text-gray-700 dark:text-gray-300" />
                            <select
                                id="role"
                                v-model="form.role"
                                class="mt-1 block w-full bg-white dark:bg-zinc-800 border-gray-300 dark:border-zinc-600 text-gray-900 dark:text-gray-100 rounded-md shadow-sm"
                            >
                                <option value="technicien">Technicien</option>
                                <option value="admin">Administrateur</option>
                            </select>
                            <InputError :message="form.errors.role" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end">
                            <PrimaryButton
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                                class="bg-indigo-600 dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-600"
                            >
                                Mettre à jour
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
