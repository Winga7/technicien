<script setup>
import { ref } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import { useForm, router } from "@inertiajs/vue3";

const form = useForm({
    name: "",
    email: "",
    role: "technicien",
});

const submit = () => {
    form.post(route("users.store"), {
        onSuccess: () => {
            router.visit(route("users.index"));
        },
    });
};
</script>

<template>
    <AppLayout title="Créer un utilisateur">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                Créer un utilisateur
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white dark:bg-zinc-800 overflow-hidden shadow-xl sm:rounded-lg p-6"
                >
                    <form @submit.prevent="submit">
                        <div class="mb-6">
                            <InputLabel
                                for="name"
                                value="Nom"
                                class="text-gray-700 dark:text-gray-300"
                            />
                            <TextInput
                                id="name"
                                v-model="form.name"
                                type="text"
                                class="mt-1 block w-full bg-white dark:bg-zinc-800 border-gray-300 dark:border-zinc-600 text-gray-900 dark:text-gray-100"
                                required
                                autofocus
                            />
                            <InputError
                                :message="form.errors.name"
                                class="mt-2"
                            />
                        </div>

                        <div class="mb-6">
                            <InputLabel
                                for="email"
                                value="Email"
                                class="text-gray-700 dark:text-gray-300"
                            />
                            <TextInput
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="mt-1 block w-full bg-white dark:bg-zinc-800 border-gray-300 dark:border-zinc-600 text-gray-900 dark:text-gray-100"
                                required
                            />
                            <InputError
                                :message="form.errors.email"
                                class="mt-2"
                            />
                        </div>

                        <div class="mb-6">
                            <InputLabel
                                for="role"
                                value="Rôle"
                                class="text-gray-700 dark:text-gray-300"
                            />
                            <select
                                id="role"
                                v-model="form.role"
                                class="mt-1 block w-full bg-white dark:bg-zinc-800 border-gray-300 dark:border-zinc-600 text-gray-900 dark:text-gray-100 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="technicien">Technicien</option>
                                <option value="admin">Administrateur</option>
                            </select>
                            <InputError
                                :message="form.errors.role"
                                class="mt-2"
                            />
                        </div>

                        <div class="flex items-center justify-end">
                            <PrimaryButton
                                :class="{ 'opacity-25': form.processing }"
                                :disabled="form.processing"
                                class="bg-indigo-600 dark:bg-indigo-500 hover:bg-indigo-700 dark:hover:bg-indigo-600"
                            >
                                Créer l'utilisateur
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
