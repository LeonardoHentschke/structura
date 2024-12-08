<template>
  <div class="min-h-screen p-8 bg-gray-100 dark:bg-gray-900">
    <!-- Resumo Executivo -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
      <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 text-white p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-bold">Total de Projetos</h3>
        <p class="text-3xl font-extrabold">{{ totalProjects }}</p>
        <p class="text-sm">Projetos cadastrados</p>
      </div>
      <div class="bg-gradient-to-r from-green-500 to-green-600 text-white p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-bold">Receita Total</h3>
        <p class="text-3xl font-extrabold">R$ {{ totalIncome.toFixed(2) }}</p>
        <p class="text-sm">Entradas financeiras</p>
      </div>
      <div class="bg-gradient-to-r from-red-500 to-red-600 text-white p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-bold">Despesa Total</h3>
        <p class="text-3xl font-extrabold">R$ {{ totalExpense.toFixed(2) }}</p>
        <p class="text-sm">Saídas financeiras</p>
      </div>
      <div class="bg-gradient-to-r from-gray-900 to-gray-800 text-white p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-bold">Saldo Atual</h3>
        <p :class="{'text-green-600': balance >= 0, 'text-red-600': balance < 0}" class="text-3xl font-extrabold">
          R$ {{ balance.toFixed(2) }}
        </p>
        <p class="text-sm">Receitas menos despesas</p>
      </div>
    </div>

    <!-- Gráficos Informativos -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
      <div class="bg-gray-800 p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-bold text-yellow-500 mb-4">Distribuição por Situação</h3>
        <BarChart :data="projectSituationData" index="label" :categories="['value']" class="h-72" />
      </div>
      <div class="bg-gray-800 p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-bold text-yellow-500 mb-4">Distribuição por Tipo</h3>
        <BarChart :data="projectTypeData" index="label" :categories="['value']" class="h-72" />
      </div>
    </div>

    <div class="bg-gray-800 p-6 rounded-lg shadow-md mb-8">
      <h3 class="text-lg font-bold text-yellow-500 mb-4">Movimentação Financeira Mensal</h3>
      <LineChart :data="monthlyFinancialData" index="month" :categories="['income', 'expense']" class="h-96" />
    </div>

    <!-- Mapa de Projetos -->
    <div class="bg-gray-800 p-6 rounded-lg shadow-md">
      <h3 class="text-lg font-bold text-yellow-500 mb-4">Mapa de Projetos</h3>
      <div id="projects-map" class="h-96 w-full rounded-lg shadow-md"></div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useProjectStore } from '@/stores/project';
import { useFinancialStore } from '@/stores/financial';
import BarChart from '@/components/ui/chart-bar/BarChart.vue';
import LineChart from '@/components/ui/chart-line/LineChart.vue';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';

const projectStore = useProjectStore();
const financialStore = useFinancialStore();
const mapInstance = ref(null);
let mapInitialized = false;

const totalProjects = ref(0);
const totalIncome = ref(0);
const totalExpense = ref(0);
const balance = ref(0);
const projectSituationData = ref([]);
const projectTypeData = ref([]);
const monthlyFinancialData = ref([]);

const initializeProjectsMap = async () => {
  console.log("Iniciando o mapa...");
  const mapContainer = document.getElementById("projects-map");

  if (!mapContainer) {
    console.error("Elemento do mapa não encontrado.");
    return;
  }

  console.log("Elemento do mapa encontrado:", mapContainer);

  if (mapInstance.value) {
    console.log("Recriando o mapa existente...");
    mapInstance.value.remove();
  }

  mapInstance.value = L.map(mapContainer).setView([-29.4661, -51.9614], 12);
  mapInstance.value.invalidateSize(); // Força o Leaflet a recalcular o tamanho
  console.log("Mapa configurado.");

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "&copy; OpenStreetMap contributors",
  }).on("tileerror", (error) => {
    console.error("Erro ao carregar tiles do mapa:", error);
  }).addTo(mapInstance.value);

  projectStore.projects.forEach((project) => {
    if (project.address?.latitude && project.address?.longitude) {
      console.log(`Adicionando marcador para o projeto: ${project.name}`);
      L.marker([project.address.latitude, project.address.longitude])
        .addTo(mapInstance.value)
        .bindPopup(
          `<strong>${project.name}</strong><br>${project.address.street}, ${project.address.city}`
        );
    } else {
      console.warn(`Coordenadas inválidas para o projeto: ${project.name}`);
    }
  });
};



onMounted(async () => {
  const projects = await projectStore.getAllProjects();
  const finances = await financialStore.getAllFinances();

  totalProjects.value = projects.length;
  totalIncome.value = finances.filter((f) => f.type === 'income').reduce((sum, f) => sum + f.amount, 0);
  totalExpense.value = finances.filter((f) => f.type === 'expense').reduce((sum, f) => sum + f.amount, 0);
  balance.value = totalIncome.value - totalExpense.value;

  projectSituationData.value = Object.entries(
    projects.reduce((acc, project) => {
      acc[project.situation.name] = (acc[project.situation.name] || 0) + 1;
      return acc;
    }, {})
  ).map(([label, value]) => ({ label, value }));

  projectTypeData.value = Object.entries(
    projects.reduce((acc, project) => {
      acc[project.type.name] = (acc[project.type.name] || 0) + 1;
      return acc;
    }, {})
  ).map(([label, value]) => ({ label, value }));

  monthlyFinancialData.value = Object.entries(
    finances.reduce((acc, f) => {
      const month = new Date(f.transaction_date).toLocaleString('default', { month: 'short' });
      acc[month] = acc[month] || { month, income: 0, expense: 0 };
      acc[month][f.type] += f.amount;
      return acc;
    }, {})
  ).map(([_, data]) => data);

  initializeProjectsMap();
});
</script>

<style scoped>
#projects-map {
  height: 100%; /* Altura total */
  width: 100%;  /* Largura total */
  min-height: 400px; /* Altura mínima */
  position: relative; /* Necessário para renderização em layouts complexos */
  background: #f0f0f0; /* Cor de fundo para verificar visualmente */
}

.card {
  border-radius: 0.75rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}
</style>
