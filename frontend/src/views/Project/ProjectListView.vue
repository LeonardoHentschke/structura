<script setup>
import { onMounted, ref, nextTick } from "vue";
import { useRouter } from 'vue-router';
import { useProjectStore } from "@/stores/project";
import { useFinancialStore } from "@/stores/financial";
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
const selectedProjectId = ref(null);
const isModalOpen = ref(false);
const financialStore = useFinancialStore();
const financialMovements = ref([]); // Armazena as movimentações financeiras do projeto
const isDeleteModalOpen = ref(false);

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
});

// Função para navegar até a página de edição de um projeto específico
const editProject = (projectId) => {
  router.push({ name: 'project', params: { id: projectId } });
};

const fetchFinancialMovements = async (projectId) => {
  try {
    financialMovements.value = await financialStore.getFinancialMovementsByProject(projectId);
    console.log("Movimentações financeiras:", financialMovements.value);
  } catch (error) {
    console.error("Erro ao buscar movimentações financeiras:", error);
  }
};

const openMovementsModal = async (projectId) => {
  selectedProjectId.value = projectId;
  await fetchFinancialMovements(projectId);
  isModalOpen.value = true;
};

const closeMovementsModal = () => {
  isModalOpen.value = false;
  financialMovements.value = [];
};

// Abre o modal de confirmação de exclusão
const openDeleteProjectModal = (projectId) => {
  selectedProjectId.value = projectId;
  isDeleteModalOpen.value = true;
};

// Fecha o modal sem excluir
const closeDeleteModal = () => {
  isDeleteModalOpen.value = false;
  selectedProjectId.value = null;
};

// Confirma e executa a exclusão do projeto
const deleteProjectConfirmed = async () => {
  if (selectedProjectId.value) {
    await projectStore.deleteProject(selectedProjectId.value);
    isDeleteModalOpen.value = false;
    selectedProjectId.value = null;
    await projectStore.getAllProjects(); // Atualiza a lista de projetos após a exclusão
  }
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
        <Button class="bg-primary text-primary-foreground dark:bg-yellow-500 dark:text-gray-800 font-bold transition-colors duration-300 shadow-md hover:shadow-lg">
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
            <div class="flex items-center space-x-2">
              <FontAwesomeIcon :icon="faUser" class="w-5 h-5 text-yellow-500" />
              <p><strong>Responsável:</strong> {{ project.responsible.name || 'Não atribuído' }}</p>
            </div>
          </div>

          <!-- Mapa do Projeto -->
          <div class="p-6">
            <div :id="`project-map-${project.id}`" class="h-60 w-full rounded-lg shadow-md"></div>
          </div>

          <!-- Botões de Ação do Card -->
          <div class="p-6 pt-0 flex justify-center space-x-4">
            <button 
              @click="editProject(project.id)" 
              class="btn-icon bg-yellow-500 hover:shadow-yellow-500/40"
              title="Editar">
              <FontAwesomeIcon :icon="faEdit" class="icon" />
              <span class="btn-text">Editar</span>
            </button>
            
            <button 
              @click="openMovementsModal(project.id)" 
              class="btn-icon bg-blue-500 hover:shadow-blue-500/40"
              title="Movimentações">
              <FontAwesomeIcon :icon="faDollarSign" class="icon" />
              <span class="btn-text">Movimentações</span>
            </button>
            
            <button 
              @click="openDeleteProjectModal(project.id)" 
              class="btn-icon bg-red-500 hover:shadow-red-500/40"
              title="Excluir">
              <FontAwesomeIcon :icon="faTrash" class="icon" />
              <span class="btn-text">Excluir</span>
            </button>
          </div>
        </div>
      </div>
    </div>
    <div v-if="isModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-gray-800 w-11/12 max-w-4xl rounded-lg shadow-lg">
        <!-- Header do Modal -->
        <div class="flex justify-between items-center p-4 border-b border-gray-700">
          <h3 class="text-lg font-bold text-yellow-500">
            Movimentações Financeiras - Projeto #{{ selectedProjectId }}
          </h3>
          <button @click="closeMovementsModal" class="text-gray-400 hover:text-gray-300">
            ✖
          </button>
        </div>
        <!-- Conteúdo do Modal -->
        <div class="p-6">
          <table class="table-auto w-full text-left border-collapse border border-gray-700">
            <thead class="bg-yellow-500 text-gray-900">
              <tr>
                <th class="px-4 py-2 border border-gray-700">Data</th>
                <th class="px-4 py-2 border border-gray-700">Descrição</th>
                <th class="px-4 py-2 border border-gray-700">Valor</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="movement in financialMovements"
                :key="movement.id"
                class="hover:bg-gray-700 transition-colors"
              >
                <td class="px-4 py-2 border border-gray-700 text-gray-300">
                  {{ new Date(movement.transaction_date).toLocaleDateString() }}
                </td>
                <td class="px-4 py-2 border border-gray-700 text-gray-300">
                  {{ movement.description }}
                </td>
                <td
                  class="px-4 py-2 border border-gray-700 font-bold"
                  :class="movement.type === 'income' ? 'text-green-500' : 'text-red-500'"
                >
                  {{ movement.type === 'income' ? '+' : '-' }} R$
                  {{ movement.amount.toFixed(2) }}
                </td>
              </tr>
              <tr v-if="financialMovements.length === 0">
                <td
                  colspan="3"
                  class="px-4 py-2 border border-gray-700 text-center text-gray-400"
                >
                  Nenhuma movimentação encontrada.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
    <!-- Modal de Confirmação para Exclusão de Projeto -->
    <div v-if="isDeleteModalOpen" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50">
      <div class="max-w-xl w-full mx-auto bg-gray-800 rounded-xl overflow-hidden">
        <div class="max-w-sm mx-auto pt-12 pb-8 px-5 text-center">
          <div class="inline-flex items-center justify-center w-12 h-12 mb-5 bg-gray-600 rounded-full">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" height="24" width="24">
              <path fill="#FF4842" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 14h-2v-2h2v2zm0-4h-2V7h2v5z"></path>
            </svg>
          </div>
          <h4 class="text-xl text-gray-100 font-semibold mb-5">
            Tem certeza que deseja excluir o projeto?
          </h4>
          <p class="text-gray-300 font-medium">
            Esta ação não pode ser desfeita. O projeto será excluído permanentemente.
          </p>
        </div>
        <div class="flex justify-center pt-5 pb-6 px-6 text-right bg-gray-900 -mb-2">
          <button
            @click="closeDeleteModal"
            class="inline-block w-full sm:w-auto py-3 px-5 mb-2 mr-4 text-center font-semibold leading-6 text-gray-200 bg-gray-500 hover:bg-gray-400 rounded-lg transition duration-200"
          >
            Cancelar
          </button>
          <button
            @click="deleteProjectConfirmed"
            class="inline-block w-full sm:w-auto py-3 px-5 mb-2 text-center font-semibold leading-6 text-blue-50 bg-red-500 hover:bg-red-600 rounded-lg transition duration-200"
          >
            Deletar
          </button>
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
  /* Estilo base para os botões com ícones */
  .btn-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    position: relative;
    border-radius: 0.375rem; /* Equivalente a rounded-lg */
    padding: 0.5rem;
    font-size: 0.75rem; /* Equivalente a text-xs */
    font-weight: 700; /* Equivalente a font-bold */
    color: #fff; /* Texto branco */
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); /* Equivalente a shadow-md */
    transition: all 0.3s ease-in-out;
    width: 2.5rem; /* Largura padrão do botão */
    height: 2.5rem; /* Altura padrão do botão */
    overflow: hidden;
  }

  /* Ícone centralizado */
  .btn-icon .icon {
    width: 1.25rem; /* Tamanho do ícone */
    height: 1.25rem;
    transition: transform 0.3s ease-in-out;
    z-index: 10; /* Garante que o ícone esteja acima do texto */
    position: relative;
  }

  /* Texto do botão */
  .btn-text {
    position: absolute;
    left: 50%; /* Centraliza horizontalmente */
    transform: translateX(-50%);
    opacity: 0;
    z-index: 5; /* Mantém abaixo do ícone */
    white-space: nowrap;
    transition: all 0.3s ease-in-out;
  }

  /* Mostrar texto ao passar o mouse */
  .btn-icon:hover {
    width: auto; /* Expande o botão para acomodar o texto */
    padding-right: 2.5rem; /* Adiciona espaço para o texto */
    padding-left: 1rem;
  }

  .btn-icon:hover .btn-text {
    opacity: 1; /* Torna o texto visível */
    position: relative; /* Garante que o texto fique ao lado do ícone */
  }

  /* Espaçamento entre os botões */
  .p-6 .space-x-4 > *:not(:last-child) {
    margin-right: 1rem; /* Espaçamento horizontal entre os botões */
  }
</style>
