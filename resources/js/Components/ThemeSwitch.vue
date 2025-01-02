<script setup>
import { ref, onMounted } from 'vue';

const isChecked = ref(false);

onMounted(() => {
    isChecked.value = localStorage.getItem('theme') === 'dark';
});

const toggleTheme = () => {
    isChecked.value = !isChecked.value;
    const theme = isChecked.value ? 'dark' : 'light';
    document.documentElement.classList.toggle('dark', isChecked.value);
    localStorage.setItem('theme', theme);
    window.dispatchEvent(new CustomEvent('theme-changed', { detail: isChecked.value }));
};
</script>

<template>
    <button
        @click="toggleTheme"
        class="relative block focus:outline-none transform scale-75 md:scale-90 lg:scale-100"
    >
        <div
            class="w-[4.5em] h-[2em] rounded-[6.25em] transition-all duration-500 ease-[cubic-bezier(0,-0.02,0.4,1.25)] relative overflow-hidden shadow-[0_-1px_1px_rgba(0,0,0,0.25),0_1px_2px_rgba(255,255,255,0.94)]"
            :class="isChecked ? 'bg-[#1D1F2C]' : 'bg-[#3D7EAE]'"
        >
            <!-- Nuages -->
            <div
                class="absolute w-5 h-5 bg-[#F3FDFF] rounded-full bottom-[-10px] left-[5px] transition-all duration-500 pointer-events-none"
                :class="isChecked ? 'bottom-[-65px]' : ''"
                style="box-shadow: 15px 5px #F3FDFF, -5px -5px #AACADF, 23px 6px #F3FDFF, 8px -2px #AACADF, 35px 0 #F3FDFF, 20px -1px #AACADF, 47px 5px #F3FDFF, 32px -5px #AACADF, 58px -1px #F3FDFF, 42px 0 #AACADF, 72px -5px #F3FDFF, 54px -7px #AACADF, 74px -28px 0 7px #F3FDFF, 64px -10px #AACADF, 66px -34px 0 7px #AACADF;"
            ></div>

            <!-- Étoiles -->
            <div
                class="absolute w-11 h-auto left-[5px] transition-all duration-500 text-white pointer-events-none"
                :class="isChecked ? 'top-1/2 -translate-y-1/2' : 'top-[-100%]'"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 144 55" fill="currentColor">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M135.831 3.00688C135.055 3.85027 134.111 4.29946 133 4.35447C134.111 4.40947 135.055 4.85867 135.831 5.71123C136.607 6.55462 136.996 7.56303 136.996 8.72727C136.996 7.95722 137.172 7.25134 137.525 6.59129C137.886 5.93124 138.372 5.39954 138.98 5.00535C139.598 4.60199 140.268 4.39114 141 4.35447C139.88 4.2903 138.936 3.85027 138.16 3.00688C137.384 2.16348 136.996 1.16425 136.996 0C136.996 1.16425 136.607 2.16348 135.831 3.00688Z"/>
                </svg>
            </div>

            <!-- Cercle principal (soleil/lune) -->
            <div
                class="absolute w-[3.375em] h-[3.375em] bg-opacity-10 bg-white rounded-full left-[-0.4375em] top-[-0.4375em] transition-all duration-300 flex items-center justify-center pointer-events-none"
                :class="isChecked ? 'left-[2.5em]' : ''"
            >
                <div
                    class="w-[2.125em] h-[2.125em] rounded-full transition-all duration-500 relative overflow-hidden"
                    :class="isChecked ? 'bg-[#C4C9D1]' : 'bg-[#ECCA2F]'"
                >
                    <!-- Cratères de la lune -->
                    <template v-if="isChecked">
                        <div class="absolute w-3 h-3 bg-[#959DB1] rounded-full top-[25%] left-[25%]"></div>
                        <div class="absolute w-4 h-4 bg-[#959DB1] rounded-full top-[50%] left-[50%]"></div>
                        <div class="absolute w-3 h-3 bg-[#959DB1] rounded-full bottom-[25%] right-[25%]"></div>
                    </template>
                </div>
            </div>
        </div>
    </button>
</template>
