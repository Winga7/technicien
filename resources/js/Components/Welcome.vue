<script setup>
import { ref, onMounted } from "vue";
import { Link } from "@inertiajs/vue3";

const isDark = ref(localStorage.getItem('theme') === 'dark');

onMounted(() => {
    window.addEventListener('theme-changed', (e) => {
        isDark.value = e.detail;
    });
});
</script>

<template>
    <div class="bg-white dark:bg-zinc-900 text-gray-800 dark:text-white">
        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
            <Link
                :href="route('interventions.create')"
                class="bg-gray-100 dark:bg-zinc-800 p-6 rounded-lg hover:bg-gray-200 dark:hover:bg-zinc-700 transition"
            >
                <h3 class="text-xl font-semibold mb-4">
                    Nouvelle Intervention
                </h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Créer une nouvelle fiche d'intervention
                </p>
            </Link>

            <Link
                :href="route('interventions.index')"
                class="bg-gray-100 dark:bg-zinc-800 p-6 rounded-lg hover:bg-gray-200 dark:hover:bg-zinc-700 transition"
            >
                <h3 class="text-xl font-semibold mb-4">Mes Interventions</h3>
                <p class="text-gray-600 dark:text-gray-400">Voir la liste de mes interventions</p>
            </Link>
        </div>
    </div>
</template>
