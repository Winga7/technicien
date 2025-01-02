<script setup>
import { ref, watch, computed } from "vue";
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import ApplicationMark from "@/Components/ApplicationMark.vue";
import Banner from "@/Components/Banner.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";
import NavLink from "@/Components/NavLink.vue";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink.vue";
import ThemeSwitch from "@/Components/ThemeSwitch.vue";

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);

const switchToTeam = (team) => {
    router.put(
        route("current-team.update"),
        {
            team_id: team.id,
        },
        {
            preserveState: false,
        }
    );
};

const logout = () => {
    router.post(route("logout"));
};

const isDark = ref(localStorage.getItem("theme") === "dark");

const toggleTheme = () => {
    isDark.value = !isDark.value;
    localStorage.setItem("theme", isDark.value ? "dark" : "light");
    document.documentElement.classList.toggle("dark", isDark.value);
};

const page = usePage();

watch(
    () => page.props.auth.user,
    (user) => {
        if (user?.must_reset_password && route().current() !== "profile.show") {
            router.visit(route("profile.show"), {
                preserveState: true,
                preserveScroll: true,
            });
        }
    },
    { immediate: true }
);

const userInitials = computed(() => {
    const names = page.props.auth.user.name.split(" ");
    return names
        .map((name) => name.charAt(0).toUpperCase())
        .join("")
        .slice(0, 2);
});
</script>

<template>
    <div class="min-h-screen bg-gray-100 dark:bg-zinc-900">
        <Head :title="title" />

        <Banner />

        <nav
            class="bg-white dark:bg-zinc-800 border-b border-gray-100 dark:border-zinc-700"
        >
            <!-- Primary Navigation Menu -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <Link :href="route('dashboard')">
                                <ApplicationMark class="block h-9 w-auto" />
                            </Link>
                        </div>

                        <!-- Navigation Links -->
                        <div
                            class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                        >
                            <NavLink
                                :href="route('dashboard')"
                                :active="route().current('dashboard')"
                                class="text-gray-700 dark:text-gray-200"
                            >
                                <span class="flex items-center">
                                    <span
                                        class="text-xl mr-3"
                                        role="img"
                                        aria-label="dashboard"
                                        >📊</span
                                    >
                                    <span>Tableau de bord</span>
                                </span>
                            </NavLink>

                            <!-- Lien Users visible pour tous mais avec des permissions différentes -->
                            <NavLink
                                :href="route('users.index')"
                                :active="route().current('users.*')"
                                class="text-gray-700 dark:text-gray-200"
                            >
                                <span class="flex items-center">
                                    <span
                                        class="text-xl mr-3"
                                        role="img"
                                        aria-label="utilisateurs"
                                        >👨‍💻</span
                                    >
                                    <span>Utilisateurs</span>
                                </span>
                            </NavLink>

                            <NavLink
                                :href="route('clients.index')"
                                :active="route().current('clients.*')"
                                class="text-gray-700 dark:text-gray-200"
                            >
                                <span class="flex items-center">
                                    <span
                                        class="text-xl mr-3"
                                        role="img"
                                        aria-label="clients"
                                        >👥</span
                                    >
                                    <span>Clients</span>
                                </span>
                            </NavLink>

                            <NavLink
                                :href="route('tickets.index')"
                                :active="route().current('tickets.*')"
                                class="text-gray-700 dark:text-gray-200"
                            >
                                <span class="flex items-center">
                                    <span
                                        class="text-xl mr-3"
                                        role="img"
                                        aria-label="tickets"
                                        >🎫</span
                                    >
                                    <span>Tickets</span>
                                </span>
                            </NavLink>
                        </div>
                    </div>

                    <!-- Settings Dropdown -->
                    <div class="hidden md:flex items-center space-x-4">
                        <ThemeSwitch />

                        <!-- User Dropdown -->
                        <div class="ms-3 relative">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <button
                                        v-if="
                                            $page.props.jetstream
                                                .managesProfilePhotos
                                        "
                                        class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition"
                                    >
                                        <img
                                            class="size-8 rounded-full object-cover"
                                            :src="
                                                $page.props.auth.user
                                                    .profile_photo_url
                                            "
                                            :alt="$page.props.auth.user.name"
                                        />
                                    </button>

                                    <span v-else class="inline-flex rounded-md">
                                        <button
                                            type="button"
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition ease-in-out duration-150"
                                        >
                                            {{ $page.props.auth.user.name }}

                                            <svg
                                                class="ms-2 -me-0.5 size-4"
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="1.5"
                                                stroke="currentColor"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                                                />
                                            </svg>
                                        </button>
                                    </span>
                                </template>

                                <template #content>
                                    <!-- Gestion du Compte -->
                                    <div
                                        class="block px-4 py-2 text-xs text-gray-400"
                                    >
                                        Gestion du compte
                                    </div>

                                    <DropdownLink :href="route('profile.show')">
                                        Profil
                                    </DropdownLink>

                                    <DropdownLink
                                        v-if="
                                            $page.props.jetstream.hasApiFeatures
                                        "
                                        :href="route('api-tokens.index')"
                                    >
                                        API Tokens
                                    </DropdownLink>

                                    <div class="border-t border-gray-200" />

                                    <!-- Déconnexion -->
                                    <form @submit.prevent="logout">
                                        <DropdownLink as="button">
                                            Déconnexion
                                        </DropdownLink>
                                    </form>
                                </template>
                            </Dropdown>
                        </div>
                    </div>

                    <!-- Hamburger -->
                    <div class="-me-2 flex items-center sm:hidden space-x-2">
                        <ThemeSwitch />
                        <button
                            @click="showingNavigationDropdown = !showingNavigationDropdown"
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-200 hover:text-gray-500 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-zinc-700 focus:outline-none focus:bg-gray-100 dark:focus:bg-zinc-700 focus:text-gray-500 dark:focus:text-white transition duration-150 ease-in-out"
                        >
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path
                                    :class="{'hidden': showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                                <path
                                    :class="{'hidden': !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Responsive Navigation Menu -->
            <div
                :class="{
                    block: showingNavigationDropdown,
                    hidden: !showingNavigationDropdown,
                }"
                class="sm:hidden"
            >
                <div class="pt-2 pb-3 space-y-1">
                    <ResponsiveNavLink
                        :href="route('dashboard')"
                        :active="route().current('dashboard')"
                    >
                        <span class="flex items-center text-gray-600 dark:text-gray-300">
                            <span class="text-xl mr-3" role="img" aria-label="dashboard">📊</span>
                            <span>Tableau de bord</span>
                        </span>
                    </ResponsiveNavLink>

                    <ResponsiveNavLink
                        :href="route('users.index')"
                        :active="route().current('users.*')"
                    >
                        <span class="flex items-center text-gray-600 dark:text-gray-300">
                            <span class="text-xl mr-3" role="img" aria-label="utilisateurs">👨‍💻</span>
                            <span>Utilisateurs</span>
                        </span>
                    </ResponsiveNavLink>

                    <ResponsiveNavLink
                        :href="route('clients.index')"
                        :active="route().current('clients.*')"
                    >
                        <span class="flex items-center text-gray-600 dark:text-gray-300">
                            <span class="text-xl mr-3" role="img" aria-label="clients">👥</span>
                            <span>Clients</span>
                        </span>
                    </ResponsiveNavLink>

                    <ResponsiveNavLink
                        :href="route('tickets.index')"
                        :active="route().current('tickets.*')"
                    >
                        <span class="flex items-center text-gray-600 dark:text-gray-300">
                            <span class="text-xl mr-3" role="img" aria-label="tickets">🎫</span>
                            <span>Tickets</span>
                        </span>
                    </ResponsiveNavLink>
                </div>

                <!-- Section profil mobile -->
                <div class="pt-4 pb-1 border-t border-gray-200 dark:border-zinc-700">
                    <div class="px-4">
                        <div class="font-medium text-base text-gray-800 dark:text-gray-200">
                            {{ $page.props.auth.user.name }}
                        </div>
                        <div class="font-medium text-sm text-gray-500 dark:text-gray-400">
                            {{ $page.props.auth.user.email }}
                        </div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <ResponsiveNavLink
                            :href="route('profile.show')"
                            class="text-gray-600 dark:text-gray-300"
                        >
                            Profile
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="text-gray-600 dark:text-gray-300"
                        >
                            Déconnexion
                        </ResponsiveNavLink>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Heading -->
        <header v-if="$slots.header" class="bg-white dark:bg-zinc-800 shadow">
            <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <!-- Page Content -->
        <main>
            <slot />
        </main>
    </div>
</template>
