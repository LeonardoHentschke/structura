<script setup>
import { ref, onMounted } from 'vue';
import { RouterLink, RouterView, useRouter } from "vue-router";
import { useAuthStore } from "@/stores/auth";
import { NavigationMenu, NavigationMenuContent, NavigationMenuItem, NavigationMenuLink, NavigationMenuList, NavigationMenuTrigger } from '@/components/ui/navigation-menu';
import { Button } from '@/components/ui/button';
import Loader from "@/components/Loader.vue";
import { useGlobalStore } from '@/stores/global';
import ThemeSelector from './components/ThemeSelector.vue';

const authStore = useAuthStore();
const router = useRouter();
const globalStore = useGlobalStore();
const isProfileModalVisible = ref(false);

const components = [
  {
    title: 'Projetos',
    href: '/projects',
    description: 'Gerencie seus projetos',
  },
  {
    title: 'Clientes',
    href: '/clients',
    description: 'Gerencie seu portfólio de clientes',
  },
  {
    title: 'Tipo de projeto',
    href: '/project-type',
    description: 'Defina seus tipos de projetos',
  },
  {
    title: 'Situação',
    href: '/project-situation',
    description: 'Defina suas situações de projeto',
  },
  {
    title: 'Financeiro',
    href: '/financials',
    description: 'Gerenciamento financeiro',
  },
];

const openProfileModal = () => {
  isProfileModalVisible.value = true;
};

const closeProfileModal = () => {
  isProfileModalVisible.value = false;
};

const saveProfileChanges = async () => {
  const success = await authStore.updateProfile({
    id: authStore.user.id,
    name: authStore.user.name,
    email: authStore.user.email,
  });

  if (success) {
    closeProfileModal();
  } else {
    console.error("Falha ao salvar alterações no perfil.");
  }
};
onMounted(() => {
  router.beforeEach((to, from, next) => {
    globalStore.startLoading();
    next();
  });

  router.afterEach(() => {
    globalStore.finishLoading();
  });
});
</script>

<template>
  <div class="flex flex-col min-h-screen">
    <Loader v-if="globalStore.isLoading" />
    <header class="sticky z-40 top-0 bg-background/80 backdrop-blur-lg border-b border-border dark:bg-gray-900/80">
      <nav class="p-4 mx-auto max-w-screen-lg flex items-center justify-between">
        <NavigationMenu>
          <NavigationMenuList>
            <NavigationMenuItem>
              <RouterLink :to="{ name: 'home' }">
                <Button class="nav-button text-black dark:text-white">Inicio</Button>
              </RouterLink>
            </NavigationMenuItem>
            <NavigationMenuItem>
              <RouterLink :to="{ name: 'about' }">
                <Button class="nav-button text-black dark:text-white">Sobre</Button>
              </RouterLink>
            </NavigationMenuItem>
            <NavigationMenuItem v-if="authStore.user">
              <RouterLink :to="{ name: 'dashboard' }">
                <Button class="nav-button text-black dark:text-white">Dashboard</Button>
              </RouterLink>
            </NavigationMenuItem>
            <NavigationMenuItem v-if="authStore.user">
              <NavigationMenuTrigger class="dark:bg-gray-900/80">Menus</NavigationMenuTrigger>
              <NavigationMenuContent>
                <ul class="grid w-[400px] gap-3 p-4 md:w-[500px] md:grid-cols-2 lg:w-[600px] dark:bg-gray-900">
                  <li v-for="component in components" :key="component.title">
                    <NavigationMenuLink as-child>
                      <a
                        :href="component.href"
                        class="nav-button block select-none space-y-1 rounded-md p-3 leading-none no-underline outline-none transition-colors hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground"
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
          <div v-if="authStore.user" class="flex items-center space-x-4">
            <!-- Botão de perfil -->
            <Button @click="openProfileModal" class="user-profile-btn flex items-center space-x-2 cursor-pointer text-black bg-white dark:bg-gray-900/80 dark:text-white">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="user-icon w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.5 20.118a7.5 7.5 0 0 1 15 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.5-1.632Z" />
              </svg>
              <span class="text-sm font-semibold">{{ authStore.user.name }}</span>
            </Button>

            <!-- Botão de Logout -->
            <button @click="authStore.logout" type="button" class="logout-cano-btn Btn">
              <div class="sign">
                <svg viewBox="0 0 512 512">
                  <path d="M377.9 105.9L500.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L377.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9v-62.1h-128c-17.7 0-32-14.3-32-32v-64c0-17.7 14.3-32 32-32h128V138c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM160 96h-64c-17.7 0-32 14.3-32 32v256c0 17.7 14.3 32 32 32h64c17.7 0 32 14.3 32 32s-14.3 32-32 32h-64c-53 0-96-43-96-96V128c0-53 43-96 96-96h64c17.7 0 32 14.3 32 32s-14.3 32-32 32z"></path>
                </svg>
              </div>
              <div class="text">Logout</div>
            </button>
          </div>

          <div v-else class="flex items-center space-x-6">
            <RouterLink :to="{ name: 'register' }">
              <Button class="register-button text-black bg-white dark:bg-gray-900/80 dark:text-white">Registrar-se</Button>
            </RouterLink>
            <RouterLink :to="{ name: 'login' }">
              <Button>Entrar</Button>
            </RouterLink>
          </div>

          <ThemeSelector />
        </section>
      </nav>
    </header>

    <main class="flex-grow h-full">
      <RouterView />
    </main>

    <!-- Modal de Edição de Perfil -->
    <div v-if="isProfileModalVisible" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
      <div class="bg-white text-black dark:bg-gray-900 dark:text-white p-8 rounded-lg shadow-lg w-96">
        <h2 class="text-2xl font-semibold mb-4">Editar Perfil</h2>
        <form @submit.prevent="saveProfileChanges">
          <label for="name" class="block text-sm font-medium">Nome</label>
          <input type="text" id="name" v-model="authStore.user.name" class="w-full text-black p-2 border rounded mb-4" />

          <label for="email" class="block text-sm font-medium">Email</label>
          <input type="email" id="email" v-model="authStore.user.email" class="w-full text-black p-2 border rounded mb-4" />

          <div class="flex justify-end space-x-4">
            <Button type="button" variant="outline" @click="closeProfileModal">Cancelar</Button>
            <Button type="submit" class="bg-primary text-white dark:text-black">Salvar</Button>
          </div>
        </form>
      </div>
    </div>

    <footer class="footer mt-auto text-sm">
      <div class="brick-wall absolute inset-0 opacity-50 z-0"></div>
      <div class="metal-beam relative z-10 mx-auto text-center mt-2 p-2 rounded-lg border border-gray-700 shadow-lg">
          <div class="beam-content">
            <div class="rivet-left"></div>
            <p class="text-gray-200 text-sm font-semibold">© 2024 Structura. Todos os direitos reservados. <a href="#" class="text-primary hover:text-primary-foreground">Política de Privacidade</a></p>
            <div class="rivet-right"></div>
          </div>
        </div>
    </footer>
  </div>
</template>

<style scoped>
  .absolute {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .metal-beam {
    background: radial-gradient(circle, #666, #4a4a4a);
    border-radius: 6px;
    padding: 2px 6px;
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.6), inset 0 0 6px rgba(255, 255, 255, 0.05);
    border: 1px solid #333;
    position: relative;
    max-width: 500px;
  }
  .rivet-left,
  .rivet-right {
      top: 50%;
      transform: translateY(-50%);
      position: absolute;
      width: 8px;
      height: 8px; /* Parafusos menores */
      background: radial-gradient(circle, #333 30%, #666 80%);
      border-radius: 50%;
  }

  .rivet-left {
      left: 0;
      margin-left: 6px;
  }

  .rivet-right {
      right: 0;
      margin-right: 6px;
  }

  /* Luz industrial discreta nas bordas */
  .metal-beam:before {
      content: '';
      position: absolute;
      top: -30px; 
      left: -30px;
      background: radial-gradient(circle, rgba(255, 255, 255, 0.05), transparent);
      z-index: 1;
      opacity: 0.1;
  }

  /* Texto menor e mais discreto */
  .beam-content p {
      font-family: 'Roboto', sans-serif;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.8px;
      color: #f3f3f3;
      text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
      padding: 4px;
      border-radius: 4px;
      font-size: 0.65rem;
  }

  /* Links */
  .beam-content a {
      color: #ffcc00;
      text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
      font-weight: 500;
      border-bottom: 1px solid #ffcc00;
  }

  /* Faixa de aviso de obras no topo do rodapé */
  .footer::before {
    content: '';
    position: absolute;
    top: 0; 
    left: 0;
    width: 100%;
    height: 12px; 
    background: repeating-linear-gradient(
      45deg,
      #ffcc00 0,
      #ffcc00 20px,
      #333 20px,
      #333 40px
    );
    opacity: 1; 
  }

  .footer {
    background-color: #2C2C2C; /* Cor cinza antracite */
    padding: 20px 0;
    position: relative;
    z-index: 10;
  }
  .nav-button {
    background: transparent;
    transition: all 0.3s ease;
    border: 1px solid transparent;
  }

  .nav-button:hover {
    color: #ffcc00;
    border-color: #ffcc00;
    background: rgba(255, 204, 0, 0.1);
    box-shadow: 0px 0px 5px rgba(255, 204, 0, 0.8);
  }

  /* Botão de registrar-se mais chamativo */
  .register-button {
    background: transparent;
    border: none;
    padding: 8px 16px;
    font-weight: bold;
    transition: all 0.3s ease;
    border-radius: 6px;
  }

  .register-button:hover {
    color: #2C2C2C;
    background-color: #ffd633;
    box-shadow: 0px 4px 10px rgba(255, 204, 0, 0.6);
    transform: translateY(-2px);
  }
  /* Botão de Logout Cano */
  .logout-cano-btn {
    background-color: var(--logout-bg);
    display: flex;
    align-items: center;
    justify-content: flex-start;
    width: 35px;
    height: 35px;
    border: none;
    border-radius: 15px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    transition-duration: 0.3s;
    box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
  }

  /* Ícone de logout */
  .sign {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition-duration: 0.3s;
  }

  .sign svg {
    width: 17px;
    fill: var(--logout-text);
  }

  /* Texto de logout */
  .text {
    position: absolute;
    right: 0;
    width: 0;
    opacity: 0;
    color: var(--logout-text);
    font-size: 1em;
    font-weight: 600;
    transition-duration: 0.3s;
  }

  /* Efeito de hover no botão */
  .logout-cano-btn:hover {
    width: 125px;
    border-radius: 15px;
    transition-duration: 0.3s;
    background-color: crimson;
  }

  .logout-cano-btn:hover .sign {
    width: 30%;
    padding-left: 20px;
    transition-duration: 0.3s;
  }

  .logout-cano-btn:hover .text {
    width: 70%;
    opacity: 1;
    padding-right: 10px;
    transition-duration: 0.3s;
  }

  .logout-cano-btn:active {
    transform: translate(2px, 2px);
  }

  /* Botão de Perfil do Usuário */
  .user-profile-btn {
    padding: 10px 16px;
    border-radius: 5px;
    display: flex;
    align-items: center;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }

  .user-profile-btn:hover {
    color: #2C2C2C;
    background-color: #ffd633;
    box-shadow: 0px 4px 10px rgba(255, 204, 0, 0.6);
    transform: translateY(-2px);
  }
</style>
