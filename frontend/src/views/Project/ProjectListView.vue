<script setup>
import { onMounted } from "vue";
import { useProjectStore } from "@/stores/project";
import { Button } from '@/components/ui/button';

// Importando Font Awesome
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faCalendarAlt, faHome, faTrash, faEdit, faUser, faMapMarkerAlt, faFileSignature, faRulerCombined, faDollarSign, faClipboardList } from '@fortawesome/free-solid-svg-icons';

const projectStore = useProjectStore();

onMounted(async () => {
  await projectStore.getAllProjects();
  console.log("Projetos carregados:", projectStore.projects);
});
</script>

<template>
  <div class="hidden h-full flex-1 flex-col space-y-8 p-8 md:flex dark:bg-gray-900">
    <div class="flex items-center justify-between space-y-2">
      <div>
        <h2 class="text-2xl font-bold tracking-tight text-primary dark:text-yellow-500">Projetos!</h2>
        <p class="text-muted-foreground text-sm tracking-wide dark:text-gray-300">Aqui está uma lista de seus projetos!</p>
      </div>
      <div class="flex items-center space-x-2">
        <Button>
          <router-link :to="{ name: 'project' }">Adicionar novo projeto</router-link>
        </Button>
      </div>
    </div>

    <div v-if="projectStore.projects.length === 0" class="text-center text-gray-500">Nenhum projeto cadastrado ainda.</div>
    
    <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
      <!-- Iterando sobre os projetos para exibir em cards -->
      <div v-for="project in projectStore.projects" :key="project.id" class="relative flex w-80 flex-col rounded-xl bg-white bg-clip-border text-gray-700 shadow-md overflow-hidden">
        <!-- Cabeçalho do Card com cor gradiente amarela -->
        <div class="relative mx-2 -mt-8 h-40 overflow-hidden rounded-xl bg-gradient-to-r from-yellow-500 to-yellow-600 bg-clip-border text-white shadow-lg shadow-yellow-500/40 flex items-center justify-center">
          <FontAwesomeIcon :icon="faClipboardList" class="w-10 h-10 text-white" />
          <div class="mx-4 text-lg">
            Projeto #{{ project.id }} 
            <div>
              {{ project.name }}
            </div>
          </div>
        </div>

        <!-- Conteúdo do Card -->
        <div class="p-6">
          <div class="block font-sans text-base font-light leading-relaxed text-inherit antialiased space-y-2">
            <div class="flex items-center space-x-2">
              <FontAwesomeIcon :icon="faUser" class="w-5 h-5 text-yellow-500" />
              <p><strong>Cliente:</strong> {{ project.client.name }} (CPF: {{ project.client.cpf }})</p>
            </div>
            <div class="flex items-center space-x-2">
              <FontAwesomeIcon :icon="faMapMarkerAlt" class="w-5 h-5 text-yellow-500" />
              <p><strong>Endereço:</strong> {{ project.address.street }}, {{ project.address.city }}</p>
            </div>
            <div class="flex items-center space-x-2">
              <FontAwesomeIcon :icon="faFileSignature" class="w-5 h-5 text-yellow-500" />
              <p><strong>Situação:</strong> {{ project.situation.name }}</p>
            </div>
            <div class="flex items-center space-x-2">
              <FontAwesomeIcon :icon="faClipboardList" class="w-5 h-5 text-yellow-500" />
              <p><strong>Tipo:</strong> {{ project.type.name }}</p>
            </div>
            <div class="flex items-center space-x-2">
              <FontAwesomeIcon :icon="faCalendarAlt" class="w-5 h-5 text-yellow-500" />
              <p><strong>Entrada:</strong> {{ new Date(project.created_at).toLocaleDateString() }}</p>
            </div>
            <div class="flex items-center space-x-2">
              <FontAwesomeIcon :icon="faRulerCombined" class="w-5 h-5 text-yellow-500" />
              <p><strong>M²:</strong> {{ project.square_meters }}</p>
            </div>
            <div class="flex items-center space-x-2">
              <FontAwesomeIcon :icon="faDollarSign" class="w-5 h-5 text-yellow-500" />
              <p><strong>Valor:</strong> R$ {{ project.price }}</p>
            </div>
            <div class="flex items-center space-x-2">
              <FontAwesomeIcon :icon="faHome" class="w-5 h-5 text-yellow-500" />
              <p><strong>MCMV:</strong> {{ project.mcmv ? "Sim" : "Não" }}</p>
            </div>
          </div>
        </div>

        <!-- Botões de Ação do Card -->
        <div class="p-6 pt-0 flex justify-between">
          <button class="select-none rounded-lg bg-yellow-500 py-2 px-4 text-center align-middle font-sans text-xs font-bold uppercase text-white shadow-md shadow-yellow-500/20 transition-all hover:shadow-lg hover:shadow-yellow-500/40 focus:opacity-[0.85] focus:shadow-none active:opacity-[0.85] active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none flex items-center space-x-1">
            <FontAwesomeIcon :icon="faEdit" class="w-4 h-4" />
            <router-link :to="{ name: 'project', params: { id: project.id } }">Editar</router-link>
          </button>
          <button class="select-none rounded-lg bg-red-500 py-2 px-4 text-center align-middle font-sans text-xs font-bold uppercase text-white shadow-md shadow-red-500/20 transition-all hover:shadow-lg hover:shadow-red-500/40 focus:opacity-[0.85] focus:shadow-none active:opacity-[0.85] active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none flex items-center space-x-1" @click="deleteProject(project.id)">
            <FontAwesomeIcon :icon="faTrash" class="w-4 h-4" />
            <span>Excluir</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
