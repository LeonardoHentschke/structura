<script setup>
import { ref, onMounted, computed } from 'vue'
import { usePage } from "@inertiajs/vue3";
import { switchTheme } from "@/theme";
import { MoonIcon, SunIcon } from '@radix-icons/vue'
import { Button } from '@/components/ui/button'
import {
  NavigationMenu,
  NavigationMenuContent,
  NavigationMenuItem,
  NavigationMenuLink,
  NavigationMenuList,
  NavigationMenuTrigger,
  navigationMenuTriggerStyle
} from '@/components/ui/navigation-menu';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuTrigger,
  DropdownMenuLabel,
  DropdownMenuSeparator
} from '@/components/ui/dropdown-menu'

const components = [
  {
    title: 'Alert Dialog',
    href: '/docs/primitives/alert-dialog',
    description:
      'A modal dialog that interrupts the user with important content and expects a response.',
  },
  {
    title: 'Hover Card',
    href: '/docs/primitives/hover-card',
    description:
      'For sighted users to preview content available behind a link.',
  },
  {
    title: 'Progress',
    href: '/docs/primitives/progress',
    description:
      'Displays an indicator showing the completion progress of a task, typically displayed as a progress bar.',
  },
]

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
    <nav class="p-6 mx-auto max-w-screen-lg flex items-center justify-between">
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
            <NavigationMenuTrigger>Cadastros</NavigationMenuTrigger>
            <NavigationMenuContent>
              <ul
                class="grid w-[400px] gap-3 p-4 md:w-[500px] md:grid-cols-2 lg:w-[600px]"
              >
                <li v-for="component in components" :key="component.title">
                  <NavigationMenuLink as-child>
                    <a
                      :href="component.href"
                      class="block select-none space-y-1 rounded-md p-3 leading-none no-underline outline-none transition-colors hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground"
                    >
                      <div class="text-sm font-medium leading-none">
                        {{ component.title }}
                      </div>
                      <p
                        class="line-clamp-2 text-sm leading-snug text-muted-foreground"
                      >
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

          <Button variant="secondary" :href="route('register')"
            >Cadastrar-se</Button
          >
        </div>
        <button
          @click="toggleTheme"
          class="hover:bg-foreground/15 w-7 h-7 grid place-items-center rounded-full hover:outline outline-1 outline-foreground"
        >
          <component :is="isDarkMode ? SunIcon : MoonIcon" />
        </button>
      </div>
    </nav>
  </header>

  <main class="p-6 mx-auto max-w-screen-lg">
    <slot />
  </main>
</template>
