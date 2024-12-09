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
      <!-- Gráfico de Distribuição por Situação -->
      <div class="bg-gray-800 p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-bold text-yellow-500 mb-4">Distribuição por Situação</h3>
        <div id="situation-chart" class="h-72"></div>
      </div>

      <!-- Gráfico de Distribuição por Tipo -->
      <div class="bg-gray-800 p-6 rounded-lg shadow-md">
        <h3 class="text-lg font-bold text-yellow-500 mb-4">Distribuição por Tipo</h3>
        <div id="type-chart" class="h-72"></div>
      </div>
    </div>

    <div class="bg-gray-800 p-6 rounded-lg shadow-md mb-8">
      <h3 class="text-lg font-bold text-yellow-500 mb-4">Movimentação Financeira Mensal</h3>
      <div id="financial-chart" class="h-96"></div>
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
import * as echarts from 'echarts';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';

const projectStore = useProjectStore();
const financialStore = useFinancialStore();

const totalProjects = ref(0);
const totalIncome = ref(0);
const totalExpense = ref(0);
const balance = ref(0);
const projectSituationData = ref([]);
const projectTypeData = ref([]);
const monthlyFinancialData = ref([]);
const mapInstance = ref(null);

const initializeProjectsMap = async () => {
  const mapContainer = document.getElementById("projects-map");
  if (!mapContainer) {
    console.error("Elemento do mapa não encontrado.");
    return;
  }

  if (mapInstance.value) {
    mapInstance.value.remove();
  }

  mapInstance.value = L.map(mapContainer).setView([-29.4661, -51.9614], 12);

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "&copy; OpenStreetMap contributors",
  }).addTo(mapInstance.value);

  projectStore.projects.forEach((project) => {
    if (project.address?.latitude && project.address?.longitude) {
      L.marker([project.address.latitude, project.address.longitude])
        .addTo(mapInstance.value)
        .bindPopup(
          `<strong>${project.name}</strong><br>${project.address.street}, ${project.address.city}`
        );
    }
  });
};

const renderCharts = () => {
  // Gráfico de Distribuição por Situação
  const situationChart = echarts.init(document.getElementById('situation-chart'));
  situationChart.setOption({
    tooltip: {
      trigger: 'item',
      backgroundColor: '#1a202c',
      textStyle: { color: '#f9fafb', fontSize: 14 },
      borderWidth: 1,
      borderColor: '#ffd700',
    },
    xAxis: {
      type: 'category',
      data: projectSituationData.value.map((item) => item.label),
      axisLabel: { color: '#f9fafb', fontWeight: 'bold' },
    },
    yAxis: {
      type: 'value',
      axisLabel: { color: '#f9fafb', fontWeight: 'bold' },
    },
    series: [
      {
        data: projectSituationData.value.map((item) => item.value),
        type: 'bar',
        itemStyle: {
          color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
            { offset: 0, color: '#ffd700' },
            { offset: 1, color: '#facc15' },
          ]),
        },
        emphasis: {
          itemStyle: {
            color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
              { offset: 0, color: '#ffa500' },
              { offset: 1, color: '#ffc107' },
            ]),
          },
        },
        barWidth: '50%',
      },
    ],
  });

  // Gráfico de Distribuição por Tipo
  const typeChart = echarts.init(document.getElementById('type-chart'));
  typeChart.setOption({
  tooltip: {
    trigger: 'item',
    backgroundColor: '#1a202c',
    textStyle: { color: '#f9fafb' },
    borderWidth: 1,
    borderColor: '#7fff00',
  },
  xAxis: {
    type: 'category',
    data: projectTypeData.value.map((item) => item.label),
    axisLabel: {
      color: '#f9fafb',
      fontSize: 14,
      fontWeight: 'bold',
    },
    axisLine: {
      lineStyle: { color: '#32cd32' }, // Linha do eixo com cor verde.
    },
  },
  yAxis: {
    type: 'value',
    axisLabel: {
      color: '#f9fafb',
      fontSize: 14,
    },
    axisLine: {
      lineStyle: { color: '#32cd32' }, // Linha do eixo com cor verde.
    },
    splitLine: {
      lineStyle: { color: 'rgba(255, 255, 255, 0.2)' }, // Linhas de grade discretas.
    },
  },
  series: [
    {
      data: projectTypeData.value.map((item) => item.value),
      type: 'bar',
      itemStyle: {
        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
          { offset: 0, color: '#7fff00' }, // Gradiente do verde claro.
          { offset: 1, color: '#228b22' }, // Gradiente do verde escuro.
        ]),
        shadowBlur: 10,
        shadowColor: 'rgba(0, 0, 0, 0.5)',
      },
      barWidth: '50%',
      emphasis: {
        itemStyle: {
          color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
            { offset: 0, color: '#98fb98' }, // Verde mais claro no hover.
            { offset: 1, color: '#32cd32' }, // Verde médio no hover.
          ]),
        },
      },
      animationEasing: 'cubicOut',
      animationDelay: (idx) => idx * 100,
    },
  ],
});


  // Gráfico de Movimentação Financeira Mensal
  const financialChart = echarts.init(document.getElementById('financial-chart'));
  financialChart.setOption({
  tooltip: {
    trigger: 'axis',
    backgroundColor: '#1a202c',
    textStyle: { color: '#f9fafb' },
    borderWidth: 1,
    borderColor: '#34d399',
    formatter: (params) => {
      const tooltipContent = params.map(
        (item) => `${item.seriesName}: R$ ${item.value.toLocaleString()}`
      );
      return `<div style="text-align: left; font-size: 14px;">
                <strong>${params[0].axisValue}</strong><br/>
                ${tooltipContent.join('<br/>')}
              </div>`;
    },
  },
  xAxis: {
    type: 'category',
    data: monthlyFinancialData.value.map((item) => item.month),
    axisLabel: {
      color: '#f9fafb',
      fontWeight: 'bold',
      fontSize: 14,
    },
    axisLine: {
      lineStyle: { color: '#34d399' },
    },
    axisTick: {
      alignWithLabel: true,
    },
  },
  yAxis: {
    type: 'value',
    axisLabel: {
      color: '#f9fafb',
      fontWeight: 'bold',
      fontSize: 14,
    },
    axisLine: {
      lineStyle: { color: '#34d399' },
    },
    splitLine: {
      lineStyle: { color: 'rgba(255, 255, 255, 0.2)', type: 'dashed' },
    },
  },
  series: [
    {
      name: 'Receita',
      data: monthlyFinancialData.value.map((item) => item.income),
      type: 'line',
      smooth: true,
      lineStyle: {
        width: 3,
        color: '#34d399',
      },
      areaStyle: {
        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
          { offset: 0, color: 'rgba(52, 211, 153, 0.8)' },
          { offset: 1, color: 'rgba(52, 211, 153, 0.2)' },
        ]),
      },
      symbol: 'circle',
      symbolSize: 8,
      itemStyle: {
        color: '#34d399',
        borderColor: '#ffffff',
        borderWidth: 2,
      },
      label: {
        show: true,
        position: 'top',
        color: '#34d399',
        fontWeight: 'bold',
        formatter: (val) => `R$ ${val.data.toLocaleString()}`,
      },
    },
    {
      name: 'Despesa',
      data: monthlyFinancialData.value.map((item) => item.expense),
      type: 'line',
      smooth: true,
      lineStyle: {
        width: 3,
        color: '#f87171',
      },
      areaStyle: {
        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
          { offset: 0, color: 'rgba(248, 113, 113, 0.8)' },
          { offset: 1, color: 'rgba(248, 113, 113, 0.2)' },
        ]),
      },
      symbol: 'circle',
      symbolSize: 8,
      itemStyle: {
        color: '#f87171',
        borderColor: '#ffffff',
        borderWidth: 2,
      },
      label: {
        show: true,
        position: 'top',
        color: '#f87171',
        fontWeight: 'bold',
        formatter: (val) => `R$ ${val.data.toLocaleString()}`,
      },
    },
  ],
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

  renderCharts();
  initializeProjectsMap();
});
</script>

<style scoped>
.leaflet-tile-layer {
  filter: brightness(0.9) saturate(1.2);
}

.h-72 {
  height: 18rem;
  transition: transform 0.3s;
}
.h-72:hover {
  transform: scale(1.02);
}

.text-yellow-500 {
  font-family: 'Inter', sans-serif;
  font-weight: bold;
  text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
}
</style>
