<template>
  <div class="min-h-screen flex flex-col bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <!-- Cabeçalho -->
    <header class="relative overflow-hidden bg-gray-900 text-white dark:bg-gray-800 dark:text-gray-200">
      <!-- Imagem de Fundo -->
      <div class="absolute inset-0">
        <div v-if="!imageErrors['header']">
          <img
            :src="getImagePath('/images/header-structura.png')"
            alt="Background"
            class="w-full h-full object-cover opacity-20 dark:opacity-30"
            @error="handleImageError('header')"
          />
        </div>
        <div v-else class="w-full h-full flex items-center justify-center bg-gray-800 dark:bg-gray-700">
          <span class="material-symbols-outlined text-7xl text-gray-500 dark:text-gray-400">
            image_not_supported
          </span>
        </div>
      </div>
      
      <!-- Conteúdo do Cabeçalho -->
      <div class="relative max-w-7xl mx-auto px-6 py-16 text-center z-10">
        <h1 class="text-5xl font-bold uppercase tracking-wide text-yellow-500">Elos Arquitetura e Engenharia</h1>
        <p class="text-xl mt-6 max-w-3xl mx-auto text-gray-200 dark:text-gray-300">
          Construindo laços, realizando sonhos. De um sonho, criamos os elos entre nossos clientes e seus objetivos.
        </p>
      </div>
      
      <!-- Barra Amarela no Rodapé do Cabeçalho -->
      <div class="absolute bottom-0 left-0 right-0 h-6 bg-yellow-500 clip-path-polygon"></div>
    </header>

    <!-- Projetos Realizados -->
    <section class="relative bg-gray-100 dark:bg-gray-900 py-16">
      <div class="relative max-w-7xl mx-auto px-6">
        <h2 class="text-4xl font-bold text-gray-800 dark:text-gray-100 mb-12 text-center uppercase">Projetos Realizados</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div
            v-for="project in projects"
            :key="project.name"
            class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden hover:shadow-2xl transition-all duration-300"
          >
            <div class="w-full h-64 overflow-hidden">
              <div v-if="!imageErrors[project.name]">
                <img
                  :src="getImagePath(project.image)"
                  alt="Imagem do projeto"
                  class="w-full h-full object-cover object-center transition-transform duration-500 hover:scale-105"
                  @error="handleImageError(project.name)"
                />
              </div>
              <div v-else class="w-full h-full flex items-center justify-center bg-gray-200 dark:bg-gray-700">
                <span class="material-symbols-outlined text-7xl text-gray-500 dark:text-gray-300">
                  image_not_supported
                </span>
              </div>
            </div>
            <div class="p-6">
              <h3 class="text-2xl font-semibold text-gray-800 dark:text-gray-100">{{ project.name }}</h3>
              <p class="text-gray-600 dark:text-gray-400 mt-2">{{ project.description }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref } from 'vue';

// Dados dos projetos realizados
const projects = ref([
  {
    name: 'Residencial Horizon',
    description: 'Projeto arquitetônico completo de um edifício residencial de alto padrão.',
    image: '/images/project1.jpg',
  },
  {
    name: 'Centro Empresarial Solarium',
    description: 'Gestão de obras e desenvolvimento estrutural de um complexo empresarial.',
    image: '/images/project2.jpg',
  },
  {
    name: 'Praça Nova Mall',
    description: 'Consultoria de arquitetura e engenharia para revitalização de um shopping center.',
    image: '/images/project3.jpg',
  },
]);

const imageErrors = ref({});

const handleImageError = (key) => {
  imageErrors.value[key] = true;
};

const getImagePath = (path) => {
  return path;
};
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined');

.material-symbols-outlined {
  font-variation-settings:
    'FILL' 0,
    'wght' 400,
    'GRAD' 0,
    'opsz' 48;
}

.clip-path-polygon {
  clip-path: polygon(0% 100%, 100% 0%, 100% 100%);
}
</style>
