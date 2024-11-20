<script setup>
import { onMounted, ref, nextTick } from "vue";
import { useRouter } from 'vue-router';
import { useProjectStore } from "@/stores/project";
import { Button } from '@/components/ui/button';
import 'leaflet/dist/leaflet.css';
import L from "leaflet";

// Importando Font Awesome
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { faCalendarAlt, faHome, faTrash, faEdit, faUser, faMapMarkerAlt, faFileSignature, faRulerCombined, faDollarSign, faClipboardList, faChevronDown, faChevronUp } from '@fortawesome/free-solid-svg-icons';

const projectStore = useProjectStore();
const router = useRouter();
const expandedProjects = ref([]); // Armazena os projetos que estão expandidos
const mapInstances = ref({}); // Armazena referências dos mapas de cada projeto

// Função para alternar entre expandir e recolher o card
const toggleProjectDetails = async (project) => {
  const index = expandedProjects.value.indexOf(project.id);
  if (index === -1) {
    expandedProjects.value.push(project.id);
    await initializeMap(project);
  } else {
    expandedProjects.value.splice(index, 1);
  }
};

// Inicializa o mapa dentro do card de cada projeto
const initializeMap = async (project) => {
  await nextTick(); // Garante que o DOM esteja totalmente renderizado

  const mapId = `project-map-${project.id}`;
  const mapContainer = document.getElementById(mapId);
  if (mapContainer) {
    if (mapInstances.value[project.id]) {
      mapInstances.value[project.id].remove(); // Remove o mapa anterior se já existir
    }

    mapInstances.value[project.id] = L.map(mapContainer, {
      center: [project.address.latitude, project.address.longitude],
      zoom: 13,
    });

    // Adiciona camada de tiles do OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(mapInstances.value[project.id]);

    // Adiciona um marcador no local do projeto
    L.marker([project.address.latitude, project.address.longitude])
      .addTo(mapInstances.value[project.id])
      .bindPopup(`${project.name}<br>${project.address.street}, ${project.address.city}`)
      .openPopup();
  } else {
    console.error("Não foi possível encontrar o elemento do mapa ou ele não está visível.");
  }
};

// Função chamada quando a página é montada
onMounted(async () => {
  await projectStore.getAllProjects();
  console.log("Projetos carregados:", projectStore.projects);
});

// Função para navegar até a página de edição de um projeto específico
const editProject = (projectId) => {
  router.push({ name: 'project', params: { id: projectId } });
};
</script>

<template>
  <div class="min-h-screen flex-1 flex-col space-y-8 p-8 md:flex dark:bg-gray-900">
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
    
    <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 w-full">
      <div v-for="project in projectStore.projects" :key="project.id" class="card-project relative flex flex-col rounded-xl text-gray-700 shadow-md overflow-hidden transition-all duration-300 w-full bg-gradient-to-r from-yellow-500 to-yellow-600">
        
        <!-- Cabeçalho do Card com cor gradiente amarela -->
        <div @click="toggleProjectDetails(project)" class="relative h-40 flex items-center justify-between px-4 cursor-pointer bg-gradient-to-r from-yellow-500 to-yellow-600 text-white shadow-lg shadow-yellow-500/40 rounded-xl">
          <div class="flex items-center">
            <FontAwesomeIcon :icon="faClipboardList" class="w-10 h-10 text-white" />
            <div class="mx-4 text-lg font-bold">
              Projeto #{{ project.id }} 
              <div class="font-normal">
                {{ project.name }}
              </div>
            </div>
          </div>
          <FontAwesomeIcon :icon="expandedProjects.includes(project.id) ? faChevronUp : faChevronDown" class="w-6 h-6 text-white" />
        </div>

        <!-- Conteúdo do Card (expandido) -->
        <div v-if="expandedProjects.includes(project.id)" class="p-6 bg-white rounded-b-xl">
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

          <!-- Mapa do Projeto -->
          <div class="p-6">
            <div :id="`project-map-${project.id}`" class="h-60 w-full rounded-lg shadow-md"></div>
          </div>

          <!-- Botões de Ação do Card -->
          <div class="p-6 pt-0 flex justify-between">
            <button @click="editProject(project.id)" class="select-none rounded-lg bg-yellow-500 py-2 px-4 text-center align-middle font-sans text-xs font-bold uppercase text-white shadow-md shadow-yellow-500/20 transition-all hover:shadow-lg hover:shadow-yellow-500/40 focus:opacity-[0.85] focus:shadow-none active:opacity-[0.85] active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none flex items-center space-x-1">
              <FontAwesomeIcon :icon="faEdit" class="w-4 h-4" />
              <span>Editar</span>
            </button>
            <button class="select-none rounded-lg bg-red-500 py-2 px-4 text-center align-middle font-sans text-xs font-bold uppercase text-white shadow-md shadow-red-500/20 transition-all hover:shadow-lg hover:shadow-red-500/40 focus:opacity-[0.85] focus:shadow-none active:opacity-[0.85] active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none flex items-center space-x-1" @click="deleteProject(project.id)">
              <FontAwesomeIcon :icon="faTrash" class="w-4 h-4" />
              <span>Excluir</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
  .cursor-pointer:hover {
    box-shadow: 0 0 10px rgba(255, 195, 0, 0.6); /* Destaque quando o mouse está sobre o cabeçalho */
  }

  .card-project{
    position: relative;
    z-index: 30;
  }
  .card-project:hover{
    box-shadow: 0px 0px 15px rgba(255, 204, 0, 0.8);
    border: 5px;
    border-color: #ffcc00;
  }
</style>
