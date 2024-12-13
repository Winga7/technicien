<script setup>
import { ref } from "vue";

const form = ref({
    nom: "",
    email: "",
    role: "technicien",
});

const submit = () => {
    alert("Soumission du formulaire avec : " + JSON.stringify(form.value));
};
</script>

<template>
    <div class="max-w-2xl mx-auto p-4">
        <form @submit.prevent="submit">
            <div class="mb-4">
                <label class="block mb-2">Nom</label>
                <input
                    v-model="form.nom"
                    type="text"
                    class="w-full px-3 py-2 border rounded"
                    required
                />
            </div>

            <div class="mb-4">
                <label class="block mb-2">Email</label>
                <input
                    v-model="form.email"
                    type="email"
                    class="w-full px-3 py-2 border rounded"
                    required
                />
            </div>

            <div class="mb-4">
                <label class="block mb-2">Rôle</label>
                <select
                    v-model="form.role"
                    class="w-full px-3 py-2 border rounded"
                >
                    <option value="technicien">Technicien</option>
                    <option value="admin">Administrateur</option>
                </select>
            </div>

            <div>
                <button
                    type="submit"
                    class="bg-blue-500 text-white px-4 py-2 rounded"
                >
                    Ajouter
                </button>
            </div>
        </form>
    </div>
</template>
