<template>
  <div class="min-h-screen flex flex-col bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">
    <!-- Cabeçalho -->
    <header class="relative bg-gray-900 text-white dark:bg-gray-800 dark:text-gray-200 overflow-hidden">
      <!-- Imagem de Fundo -->
      <div class="absolute inset-0">
        <div v-if="!imageErrors['header']">
          <img
            :src="getImagePath('/images/header-structura.png')"
            alt="Background"
            class="absolute inset-0 w-full h-full object-cover zoom-image"
            :style="{ transform: `translateY(${parallaxOffset}px) scale(${scale})`, opacity: 0.4}"
            @error="handleImageError('header')"
          />
        </div>
        <div v-else class="h-screen flex items-center justify-center bg-gray-800 dark:bg-gray-700" style="height: 100vh;">
          <span class="material-symbols-outlined text-7xl text-gray-500 dark:text-gray-400">
            image_not_supported
          </span>
        </div>
      </div>

      <!-- Efeito de Luz (Degradê) -->
      <div
        style="position: absolute; bottom: 0; left: 0; right: 0; height: 100px; background: linear-gradient(to top, rgba(255, 204, 0, 0.5), transparent); z-index: 2;"
      ></div>

      <!-- Conteúdo do Cabeçalho -->
      <div class="relative max-w-7xl mx-auto px-6 py-16 text-center z-10">
        <h1 class="text-5xl font-bold uppercase tracking-wide text-yellow-500">Elos Arquitetura e Engenharia</h1>
        <p class="text-xl mt-6 max-w-3xl mx-auto text-gray-200 dark:text-gray-300">
          Construindo laços, realizando sonhos. De um sonho, criamos os elos entre nossos clientes e seus objetivos.
        </p>
      </div>
    </header>

    <!-- Projetos Realizados -->
    <section class="relative bg-gray-100 dark:bg-gray-900 py-16">
      <div class="relative max-w-7xl mx-auto px-6">
        <h2 class="text-4xl font-bold text-gray-800 dark:text-gray-100 mb-12 text-center uppercase">Projetos Realizados</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <div
            v-for="project in projects"
            :key="project.name"
            class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden hover:shadow-2xl transition-all duration-300 group"
          >
            <div class="w-full h-64 overflow-hidden">
              <div v-if="!imageErrors[project.name]">
                <img
                  :src="getImagePath(project.image)"
                  alt="Imagem do projeto"
                  class="blur-md w-full h-full object-cover object-center op transition-transform duration-500 hover:scale-105 group-hover:blur-none"
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
import { ref, onMounted, onUnmounted } from 'vue';

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
const scale = ref(1);
const parallaxOffset = ref(0); // Controle do deslocamento do parallax

const handleImageError = (key) => {
  imageErrors.value[key] = true;
};

const getImagePath = (path) => path;

// Função para ajustar a escala da imagem dinamicamente
const adjustScale = () => {
  const viewportRatio = window.innerWidth / window.innerHeight;
  const imageRatio = 2;

  scale.value = viewportRatio < imageRatio ? imageRatio / viewportRatio : viewportRatio / imageRatio;
};

// Função para ajustar o deslocamento do parallax
const handleScroll = () => {
  parallaxOffset.value = window.scrollY * 0.4; // Controle do fator do parallax
};

onMounted(() => {
  adjustScale();
  window.addEventListener('resize', adjustScale);
  window.addEventListener('scroll', handleScroll); // Adiciona o evento de rolagem
});

onUnmounted(() => {
  window.removeEventListener('resize', adjustScale);
  window.removeEventListener('scroll', handleScroll);
});
</script>

<style scoped>
  .zoom-image {
    transition: transform 0.5s ease;
  }
  .blur-md {
    filter: blur(10px);
    transition: filter 0.5s ease, transform 0.5s ease;
  }
  .group:hover .blur-md {
    filter: blur(0);
  }
</style>
