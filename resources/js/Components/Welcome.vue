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
        <!-- Quick Actions -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 p-4 sm:p-6">
            <Link
                :href="route('tickets.create')"
                class="bg-gray-100 dark:bg-zinc-800 p-4 sm:p-6 rounded-lg hover:bg-gray-200 dark:hover:bg-zinc-700 transition"
            >
                <div class="flex items-center space-x-3 mb-4">
                    <span class="text-2xl" role="img" aria-label="nouveau ticket">➕</span>
                    <h3 class="text-lg sm:text-xl font-semibold">Nouveau ticket</h3>
                </div>
                <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400">
                    Créer un nouveau ticket
                </p>
            </Link>

            <Link
                :href="route('tickets.index')"
                class="bg-gray-100 dark:bg-zinc-800 p-4 sm:p-6 rounded-lg hover:bg-gray-200 dark:hover:bg-zinc-700 transition"
            >
                <div class="flex items-center space-x-3 mb-4">
                    <span class="text-2xl" role="img" aria-label="tickets">🎫</span>
                    <h3 class="text-lg sm:text-xl font-semibold">Tickets en cours</h3>
                </div>
                <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400">
                    Voir la liste des tickets en cours
                </p>
            </Link>
        </div>
    </div>
</template>
