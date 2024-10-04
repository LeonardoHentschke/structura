<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { usePage } from "@inertiajs/vue3";
import { switchTheme } from "@/theme";
import { MoonIcon, SunIcon } from '@radix-icons/vue';
import { Button } from '@/components/ui/button';
import {
  NavigationMenu,
  NavigationMenuItem,
  NavigationMenuLink,
  NavigationMenuList,
} from '@/components/ui/navigation-menu';
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
  DropdownMenuLabel,
  DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu';
import { Inertia } from '@inertiajs/inertia';
import Loader from '@/components/Loader.vue';

const page = usePage();
const user = computed(() => page.props.auth.user);
const isDarkMode = ref(false);
const isLoading = ref(false);

const start = () => {
  isLoading.value = true;
};

const finish = () => {
  isLoading.value = false;
};

onMounted(() => {
  isDarkMode.value = localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);

  Inertia.on('start', start);
  Inertia.on('finish', finish);
  Inertia.on('success', finish);
  Inertia.on('error', finish);
});

onUnmounted(() => {
  Inertia.off('start', start);
  Inertia.off('finish', finish);
  Inertia.off('success', finish);
  Inertia.off('error', finish);
});

const toggleTheme = () => {
  switchTheme();
  isDarkMode.value = !isDarkMode.value;
};
</script>

<template>
  <div class="flex flex-col min-h-screen">
    <!-- Loader Component -->
    <Loader v-if="isLoading" />

    <!-- Header -->
    <header class="sticky z-40 top-0 bg-secondary backdrop-blur-lg border-b-2 border-primary">
      <nav class="nav-bar flex items-center justify-between p-4 mx-auto max-w-screen-xl">
        <NavigationMenu>
          <NavigationMenuList class="flex space-x-8"> <!-- Espaçamento entre os links -->
            <NavigationMenuItem v-if="!user">
              <NavigationMenuLink :href="route('home')" class="nav-link">Início</NavigationMenuLink>
            </NavigationMenuItem>
            <NavigationMenuItem v-if="!user">
              <NavigationMenuLink :href="route('about')" class="nav-link">Sobre</NavigationMenuLink>
            </NavigationMenuItem>
            <NavigationMenuItem v-if="user">
              <NavigationMenuLink :href="route('dashboard')" class="nav-link">Dashboard</NavigationMenuLink>
            </NavigationMenuItem>
            <NavigationMenuItem v-if="user">
              <NavigationMenuLink :href="route('clients')" class="nav-link">Clientes</NavigationMenuLink>
            </NavigationMenuItem>
          </NavigationMenuList>
        </NavigationMenu>

        <div class="flex items-center space-x-4">
          <!-- Verifica se há um usuário logado -->
          <div v-if="user" class="flex items-center space-x-4">
            <!-- Botão de perfil -->
            <Button class="user-profile-btn flex items-center space-x-2 cursor-pointer" :href="route('profile.edit')">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="user-icon w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.118a7.5 7.5 0 0 1 15 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.5-1.632Z" />
              </svg>
              <span class="user-name text-sm font-semibold">{{ user.name }}</span>
            </Button>

            <!-- Botão de Logout -->
            <Link :href="route('logout')" method="post" as="button">
              <button type="submit" class="logout-cano-btn Btn">
                <div class="sign">
                  <svg viewBox="0 0 512 512" class="logout-icon">
                    <path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9v-62.1h-128c-17.7 0-32-14.3-32-32v-64c0-17.7 14.3-32 32-32h128V138c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96h-64c-17.7 0-32 14.3-32 32v256c0 17.7 14.3 32 32 32h64c17.7 0 32 14.3 32 32s-14.3 32-32 32h-64c-53 0-96-43-96-96V128c0-53 43-96 96-96h64c17.7 0 32 14.3 32 32s-14.3 32-32 32z"></path>
                  </svg>
                </div>
                <div class="text">Logout</div>
              </button>
            </Link>
          </div>

          <!-- Caso não haja usuário logado -->
          <div v-else class="space-x-2">
            <Button :href="route('login')" class="button-link">Entrar</Button>
            <Button variant="secondary" :href="route('register')" class="button-link-secondary">Cadastrar-se</Button>
          </div>

          <!-- Botão de troca de tema (claro/escuro) -->
          <button @click="toggleTheme" class="hover:bg-foreground/15 w-7 h-7 grid place-items-center rounded-full hover:outline outline-1 outline-foreground">
            <component :is="isDarkMode ? SunIcon : MoonIcon" />
          </button>
        </div>
      </nav>
    </header>

    <!-- Main content -->
    <main class="p-6 mx-auto max-w-screen-lg">
      <slot />
    </main>

    <!-- Footer -->
    <footer class="footer relative mt-auto text-sm">
      <div class="brick-wall absolute inset-0 opacity-50 z-0"></div>
      <div class="metal-beam relative z-10 mx-auto text-center mt-2 p-2 rounded-lg border border-gray-700 shadow-lg">
        <div class="beam-content">
          <p class="text-gray-200 text-sm font-semibold">© 2024 Structura. Todos os direitos reservados. <a href="#" class="text-primary hover:text-primary-foreground">Política de Privacidade</a></p>
        </div>
      </div>
    </footer>
  </div>
</template>
