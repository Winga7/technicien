<script setup></script>
<template>
    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
        <div class="bg-zinc-800 p-6 rounded-lg">
            <h3 class="text-xl font-semibold mb-2">Interventions en attente</h3>
            <p class="text-3xl font-bold text-blue-400">12</p>
        </div>
        <div class="bg-zinc-800 p-6 rounded-lg">
            <h3 class="text-xl font-semibold mb-2">Interventions en cours</h3>
            <p class="text-3xl font-bold text-yellow-400">5</p>
        </div>
        <div class="bg-zinc-800 p-6 rounded-lg">
            <h3 class="text-xl font-semibold mb-2">Interventions terminées</h3>
            <p class="text-3xl font-bold text-green-400">28</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <Link
            :href="route('interventions.create')"
            class="bg-zinc-800 p-6 rounded-lg hover:bg-zinc-700 transition"
        >
            <h3 class="text-xl font-semibold mb-4">Nouvelle Intervention</h3>
            <p class="text-gray-400">Créer une nouvelle fiche d'intervention</p>
        </Link>

        <Link
            :href="route('interventions.index')"
            class="bg-zinc-800 p-6 rounded-lg hover:bg-zinc-700 transition"
        >
            <h3 class="text-xl font-semibold mb-4">Mes Interventions</h3>
            <p class="text-gray-400">Voir la liste de mes interventions</p>
        </Link>
    </div>
</template>
