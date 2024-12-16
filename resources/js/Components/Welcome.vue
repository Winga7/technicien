<script setup>
import { ref, onMounted } from "vue";
import { Link } from "@inertiajs/vue3";

const isDark = ref(localStorage.getItem("theme") === "dark");

onMounted(() => {
    window.addEventListener("theme-changed", (e) => {
        isDark.value = e.detail;
    });
});
</script>

<template>
    <div class="bg-white dark:bg-zinc-900 text-gray-800 dark:text-white">
        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12 p-6">
            <div class="bg-gray-100 dark:bg-zinc-800 p-6 rounded-lg">
                <h3 class="text-xl font-semibold mb-2">
                    Interventions en attente
                </h3>
                <p class="text-3xl font-bold text-blue-400">12</p>
            </div>
            <div class="bg-gray-100 dark:bg-zinc-800 p-6 rounded-lg">
                <h3 class="text-xl font-semibold mb-2">
                    Interventions en cours
                </h3>
                <p class="text-3xl font-bold text-yellow-400">5</p>
            </div>
            <div class="bg-gray-100 dark:bg-zinc-800 p-6 rounded-lg">
                <h3 class="text-xl font-semibold mb-2">
                    Interventions terminées
                </h3>
                <p class="text-3xl font-bold text-green-400">28</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
            <Link
                :href="route('tickets.create')"
                class="bg-gray-100 dark:bg-zinc-800 p-6 rounded-lg hover:bg-gray-200 dark:hover:bg-zinc-700 transition"
            >
                <h3 class="text-xl font-semibold mb-4">Nouveau ticket</h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Créer un nouveau ticket
                </p>
            </Link>

            <Link
                :href="route('tickets.index')"
                class="bg-gray-100 dark:bg-zinc-800 p-6 rounded-lg hover:bg-gray-200 dark:hover:bg-zinc-700 transition"
            >
                <h3 class="text-xl font-semibold mb-4">Tickets en cours</h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Voir la liste des tickets en cours
                </p>
            </Link>
        </div>
    </div>
</template>
