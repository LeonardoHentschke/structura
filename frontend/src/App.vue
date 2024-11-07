<script setup>
import { ref, onMounted } from 'vue'
import { RouterLink, RouterView } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { switchTheme } from "@/lib/theme";

import { NavigationMenu, NavigationMenuContent, NavigationMenuItem, NavigationMenuLink, NavigationMenuList, NavigationMenuTrigger, navigationMenuTriggerStyle } from '@/components/ui/navigation-menu';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger, DropdownMenuLabel, DropdownMenuSeparator } from '@/components/ui/dropdown-menu';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { MoonIcon, SunIcon } from '@radix-icons/vue';
import { Button } from '@/components/ui/button';

const authStore = useAuthStore();
const isDarkMode = ref(false);

const components = [
{
    title: 'Projetos',
    href: '/projects',
    description:
      'Gerencie seus projetos',
  },
  {
    title: 'Clientes',
    href: '/clients',
    description:
      'Gerencie seu portfólio de clientes',
  },
  {
    title: 'Tipo de projeto',
    href: '/project-type',
    description:
      'Defina seus tipos de projetos',
  },
  {
    title: 'Situação',
    href: '/project-situation',
    description:
      'Defina suas situações de projeto',
  },
];

onMounted(() => {
  isDarkMode.value = localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);
});

const toggleTheme = () => {
  switchTheme();
  isDarkMode.value = !isDarkMode.value;
};
</script>

<template>
  <header class="sticky z-40 top-0 bg-background/80 backdrop-blur-lg border-b border-border">
    <nav class="p-4 mx-auto max-w-screen-lg flex items-center justify-between">
      <NavigationMenu>
        <NavigationMenuList>
          <NavigationMenuItem v-if="!authStore.user">
            <RouterLink :to="{ name: 'home' }">
              <Button variant="ghost">Inicio</Button>
            </RouterLink>
          </NavigationMenuItem>
          <NavigationMenuItem v-if="authStore.user">
            <RouterLink :to="{ name: 'dashboard' }">
              <Button variant="ghost">Dashboard</Button>
            </RouterLink>
          </NavigationMenuItem>
          <NavigationMenuItem v-if="authStore.user">
            <NavigationMenuTrigger>Menus</NavigationMenuTrigger>
            <NavigationMenuContent>
              <ul class="grid w-[400px] gap-3 p-4 md:w-[500px] md:grid-cols-2 lg:w-[600px] ">
                <li v-for="component in components" :key="component.title">
                  <NavigationMenuLink as-child>
                    <a
                      :href="component.href"
                      class="block select-none space-y-1 rounded-md p-3 leading-none no-underline outline-none transition-colors hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground"
                    >
                      <div class="text-sm font-medium leading-none">{{ component.title }}</div>
                      <p class="line-clamp-2 text-sm leading-snug text-muted-foreground">
                        {{ component.description }}
                      </p>
                    </a>
                  </NavigationMenuLink>
                </li>
              </ul>
            </NavigationMenuContent>
          </NavigationMenuItem>
        </NavigationMenuList>
      </NavigationMenu>

      <section class="flex items-center space-x-6">
        <DropdownMenu v-if="authStore.user">
          <DropdownMenuTrigger as-child>
            <Button variant="outline" class="shadow-none">
              <Avatar class="h-8 w-8 mr-2">
                <AvatarImage
                  src="https://github.com/radix-vue.png"
                  alt="@radix-vue"
                />
                <AvatarFallback>CN</AvatarFallback>
              </Avatar>
              {{ authStore.user.name }}
            </Button>
          </DropdownMenuTrigger>
          <DropdownMenuContent class="w-56">
            <DropdownMenuLabel>Minha conta</DropdownMenuLabel>
            <DropdownMenuSeparator />
            <DropdownMenuItem>
                Editar perfil
            </DropdownMenuItem>
            <DropdownMenuItem>
              <form @submit.prevent="authStore.logout">
                <button>Sair</button> 
              </form>
            </DropdownMenuItem>
          </DropdownMenuContent>
        </DropdownMenu>

        <div v-else class="flex items-center space-x-6">
          <RouterLink :to="{ name: 'register' }">
            <Button variant="outline">Registrar-se</Button>
          </RouterLink>
          <RouterLink :to="{ name: 'login' }">
            <Button>Entrar</Button>
          </RouterLink>
        </div>

        <Button variant="ghost" size="icon" @click="toggleTheme">
            <component :is="isDarkMode ? SunIcon : MoonIcon" />
        </Button>
      </section>
    </nav>
  </header>

  <RouterView />
</template>
