<script setup>
import { ref, onMounted, computed } from 'vue'
import { usePage } from "@inertiajs/vue3";
import { switchTheme } from "@/theme";
import { MoonIcon, SunIcon } from '@radix-icons/vue'
import { Button } from '@/components/ui/button'
import { NavigationMenu, NavigationMenuItem, NavigationMenuLink, NavigationMenuList, navigationMenuTriggerStyle } from '@/components/ui/navigation-menu';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger, DropdownMenuLabel, DropdownMenuSeparator } from '@/components/ui/dropdown-menu';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';

const page = usePage();
const user = computed(() => page.props.auth.user);
const isDarkMode = ref(false);

onMounted(() => {
  isDarkMode.value = localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);
});

const toggleTheme = () => {
  switchTheme();
  isDarkMode.value = !isDarkMode.value;
};
</script>

<template>
  <header
    class="sticky z-40 top-0 bg-background/80 backdrop-blur-lg border-b border-border"
  >
    <nav class="p-3 mx-auto max-w-screen-lg flex items-center justify-between">
      <NavigationMenu>
        <NavigationMenuList>
          <NavigationMenuItem v-if="!user">
            <NavigationMenuLink
              :href="route('home')"
              :class="navigationMenuTriggerStyle()"
            >
              Inicio
            </NavigationMenuLink>
          </NavigationMenuItem>
          <NavigationMenuItem v-if="!user">
            <NavigationMenuLink
              :href="route('about')"
              :class="navigationMenuTriggerStyle()"
            >
              Sobre
            </NavigationMenuLink>
          </NavigationMenuItem>
          <NavigationMenuItem v-if="user">
            <NavigationMenuLink
              :href="route('dashboard')"
              :class="navigationMenuTriggerStyle()"
            >
              Dashboard
            </NavigationMenuLink>
          </NavigationMenuItem>
          <NavigationMenuItem v-if="user">
            <NavigationMenuLink
              :href="route('clients')"
              :class="navigationMenuTriggerStyle()"
            >
              Clientes
            </NavigationMenuLink>
          </NavigationMenuItem>
        </NavigationMenuList>
      </NavigationMenu>

      <div class="flex items-center space-x-6">
        <DropdownMenu v-if="user">
          <DropdownMenuTrigger as-child>
            <Button variant="outline" class="shadow-none">
              <Avatar class="h-8 w-8 mr-2">
                <AvatarImage
                  src="https://github.com/radix-vue.png"
                  alt="@radix-vue"
                />
                <AvatarFallback>CN</AvatarFallback>
              </Avatar>
              {{ user.name }}
            </Button>
          </DropdownMenuTrigger>
          <DropdownMenuContent class="w-56">
            <DropdownMenuLabel>Minha conta</DropdownMenuLabel>
            <DropdownMenuSeparator />
            <DropdownMenuItem>
              <Link :href="route('profile.edit')">
                Editar perfil
              </Link>
            </DropdownMenuItem>
            <DropdownMenuItem>
              <Link :href="route('logout')" method="post" as="button">
                Sair
              </Link>
            </DropdownMenuItem>
          </DropdownMenuContent>
        </DropdownMenu>
        <div v-else class="space-x-2">
          <Button :href="route('login')">Entrar</Button>

          <Button variant="secondary" :href="route('register')">
            Cadastrar-se
          </Button>
        </div>
        <Button
          @click="toggleTheme"
          variant="ghost"
        >
          <component :is="isDarkMode ? SunIcon : MoonIcon" />
        </Button>
      </div>
    </nav>
  </header>

  <main class="p-6 mx-auto max-w-screen-lg">
    <slot />
  </main>
</template>
