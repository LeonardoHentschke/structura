<script setup>
import { useAuthStore } from "@/stores/auth";
import { storeToRefs } from "pinia";
import { onMounted, reactive } from "vue";

const { errors } = storeToRefs(useAuthStore());
const { authenticate } = useAuthStore();

const formData = reactive({
  email: "",
  password: "",
});

onMounted(() => (errors.value = {}));
</script>

<template>
  <main class="flex h-screen w-full items-center justify-center bg-gray-100 dark:bg-gray-900">
    <!-- Card de Login -->
    <form
      class="bg-white dark:bg-zinc-900 shadow-2xl rounded-2xl overflow-hidden border-4 border-[#ffd633] w-full max-w-md"
      @submit.prevent="authenticate('login', formData)"
    >
      <div class="px-8 py-10 md:px-10">
        <h2 class="text-4xl font-extrabold text-center text-zinc-800 dark:text-white">
          Bem-vindo de volta!
        </h2>
        <p class="text-center text-zinc-600 dark:text-zinc-400 mt-3">
          Acesse sua conta e continue de onde parou.
        </p>
        <div class="mt-10">
          <div class="relative">
            <label
              class="block mb-3 text-sm font-medium text-zinc-600 dark:text-zinc-200"
              for="email"
            >
              Email
            </label>
            <input
              id="email"
              placeholder="Seu email de preferência"
              v-model="formData.email"
              class="block w-full px-4 py-3 mt-2 text-zinc-800 bg-white border-2 rounded-lg dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 focus:border-[#ffd633] dark:focus:border-[#ffd633] focus:ring-opacity-50 focus:outline-none focus:ring focus:ring-[#ffd633]"
              type="email"
            />
            <p v-if="errors.email" class="text-red-500 text-xs mt-1">{{ errors.email[0] }}</p>
          </div>
          <div class="mt-6">
            <label
              class="block mb-3 text-sm font-medium text-zinc-600 dark:text-zinc-200"
              for="password"
            >
              Senha
            </label>
            <input
              id="password"
              placeholder="Uma senha segura"
              v-model="formData.password"
              class="block w-full px-4 py-3 mt-2 text-zinc-800 bg-white border-2 rounded-lg dark:border-zinc-600 dark:bg-zinc-800 dark:text-zinc-200 focus:border-[#ffd633] dark:focus:border-[#ffd633] focus:ring-opacity-50 focus:outline-none focus:ring focus:ring-[#ffd633]"
              type="password"
            />
            <p v-if="errors.password" class="text-red-500 text-xs mt-1">{{ errors.password[0] }}</p>
          </div>
          <div class="mt-10">
            <button
              class="w-full px-4 py-3 tracking-wide text-zinc-800 dark:text-zinc-900 transition-colors duration-200 transform bg-[#ffd633] rounded-lg hover:bg-[#e6c02e] focus:outline-none focus:ring-4 focus:ring-[#ffd633]/50 dark:focus:ring-[#ffd633]/30"
              type="submit"
            >
              Entrar
            </button>
          </div>
        </div>
      </div>
      <div class="px-8 py-4 bg-[#fff5cc] dark:bg-zinc-800">
        <div class="text-sm text-[#d4b020] dark:text-[#ffd633] text-center">
          Não tem uma conta?
          <a class="font-medium underline" href="/register">Registre-se</a>
        </div>
      </div>
    </form>
  </main>
</template>
