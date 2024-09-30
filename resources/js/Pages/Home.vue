<script setup>
import { ref } from 'vue';

// Dados de projetos realizados
const projects = ref([
  {
    name: 'Residencial Horizon',
    description: 'Projeto arquitetônico completo de um edifício residencial de alto padrão.',
    image: '/public/images/project1.jpg',
  },
  {
    name: 'Centro Empresarial Solarium',
    description: 'Gestão de obras e desenvolvimento estrutural de um complexo empresarial.',
    image: '/public/images/project2.jpg',
  },
  {
    name: 'Praça Nova Mall',
    description: 'Consultoria de arquitetura e engenharia para revitalização de um shopping center.',
    image: '/public/images/project3.jpg',
  },
]);

// Diferenciais da empresa
const values = [
  { title: 'Sustentabilidade', description: 'Projetos focados em soluções sustentáveis e eco-friendly.' },
  { title: 'Inovação', description: 'Uso de tecnologias de ponta para garantir eficiência e qualidade.' },
  { title: 'Comprometimento', description: 'Nossa equipe está comprometida com a entrega de excelência em cada projeto.' },
];

// Objeto para rastrear erros de imagem
const imageErrors = ref({});

// Função para lidar com erro de imagem
const handleImageError = (key) => {
  imageErrors.value[key] = true;
};

// Função para obter o caminho da imagem
const getImagePath = (path) => {
  return path;
};
</script>

<template>
  <div class="bg-gray-100 min-h-screen flex flex-col">
    <header class="relative bg-gray-900 text-white">
      <div class="absolute inset-0">
        <div v-if="!imageErrors['header']">
          <img
            :src="getImagePath('/images/header-structura.png')"
            alt="Background"
            class="w-full h-full object-cover opacity-20"
            @error="handleImageError('header')"
          />
        </div>
        <div v-else class="w-full h-full flex items-center justify-center bg-gray-800">
          <span class="material-symbols-outlined text-7xl text-gray-500">
            image_not_supported
          </span>
        </div>
      </div>
      <div class="relative max-w-7xl mx-auto px-6 py-16 text-center">
        <h1 class="text-5xl font-bold uppercase tracking-wide text-yellow-500">Elos Arquitetura e Engenharia</h1>
        <p class="text-xl mt-6 max-w-3xl mx-auto text-gray-200">
          Construindo laços, realizando sonhos. De um sonho, criamos os elos entre nossos clientes e seus objetivos.
        </p>
      </div>
      <div class="absolute bottom-0 left-0 right-0 h-8 bg-yellow-500 clip-path-polygon"></div>
    </header>

    <section class="relative bg-gray-100 py-16">
      <div class="relative max-w-7xl mx-auto px-6">
        <h2 class="text-4xl font-bold text-gray-800 mb-12 text-center uppercase">Projetos Realizados</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
          <div
            v-for="project in projects"
            :key="project.name"
            class="bg-white shadow-xl rounded-lg overflow-hidden hover:shadow-2xl transition-all duration-300"
          >
            <div class="w-full h-56 bg-gray-200 flex items-center justify-center relative">
              <div v-if="!imageErrors[project.name]">
                <img
                  :src="getImagePath(project.image)"
                  alt="Imagem do projeto"
                  class="w-full h-full object-cover"
                  @error="handleImageError(project.name)"
                />
              </div>
              <div v-else class="w-full h-full flex items-center justify-center">
                <span class="material-symbols-outlined text-7xl text-gray-500">
                  image_not_supported
                </span>
              </div>
            </div>
            <div class="p-6">
              <h3 class="text-2xl font-semibold text-gray-800">{{ project.name }}</h3>
              <p class="text-gray-600 mt-4">{{ project.description }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

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
