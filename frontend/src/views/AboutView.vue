<template>
  <div class="about-page">

    <!-- Hero Section -->
    <section class="hero-section flex flex-col items-center justify-center min-h-screen bg-gradient-to-b from-gray-100 via-white to-gray-200 dark:from-gray-800 dark:via-gray-900 dark:to-gray-800 relative">
      <img src="http://localhost:5173/images/structura.png" alt="Structura Logo" class="logo-animation w-40 h-40 mb-8">
      <h1 class="text-6xl font-bold text-primary tracking-wider uppercase dark:text-yellow-400">Structura</h1>
      <p class="text-xl text-gray-600 dark:text-gray-300 mt-4 tracking-wide">
        Plataforma de Gestão em Engenharia
      </p>
      <div class="scroll-indicator mt-20">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-10 h-10 text-gray-500 dark:text-gray-300 animate-bounce">
          <path fill-rule="evenodd" d="M11.47 13.28a.75.75 0 0 0 1.06 0l7.5-7.5a.75.75 0 0 0-1.06-1.06L12 11.69 5.03 4.72a.75.75 0 0 0-1.06 1.06l7.5 7.5Z" clip-rule="evenodd" />
          <path fill-rule="evenodd" d="M11.47 19.28a.75.75 0 0 0 1.06 0l7.5-7.5a.75.75 0 1 0-1.06-1.06L12 17.69l-6.97-6.97a.75.75 0 0 0-1.06 1.06l7.5 7.5Z" clip-rule="evenodd" />
        </svg>
      </div>
    </section>

    <!-- Sobre a Empresa -->
    <section class="company-info flex flex-col md:flex-row items-center justify-between py-20 px-8 bg-white dark:bg-gray-800">
      <div class="w-full md:w-1/2 text-gray-800 dark:text-gray-300">
        <h2 class="text-4xl font-semibold mb-6">Sobre a Structura</h2>
        <p class="text-lg leading-relaxed mb-4">
          O sistema oferece soluções integradas para o controle de projetos, funcionários, clientes, e movimentações financeiras, garantindo uma visão completa e organizada do andamento de cada obra, do planejamento à finalização.
        </p>
        <p class="text-lg leading-relaxed">
          Com foco na organização e na eficiência, a plataforma Structura se tornou essencial para empresas que desejam padronizar e profissionalizar seus processos.
        </p>
      </div>
      <div class="w-full md:w-1/2 flex justify-center mt-12 md:mt-0">
        <img src="http://localhost:5173/images/structura.png" alt="Structura Logo" class="w-64 h-64">
      </div>
    </section>

    <!-- Módulos e Funcionalidades -->
    <section class="modules-section bg-gray-100 dark:bg-gray-900 py-20">
      <div class="container mx-auto">
        <h2 class="text-4xl font-semibold text-center text-gray-800 dark:text-gray-300 mb-10">Funcionalidades</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          
          <!-- Módulo 1: Cadastro de Clientes -->
          <div class="module-card p-6 bg-white dark:bg-gray-700 shadow-md rounded-lg text-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="black" class="w-16 h-16 mx-auto mb-4 dark:stroke-gray-300">
              <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
            </svg>
            <h3 class="text-xl font-semibold mb-2 text-black dark:text-gray-200">Cadastro de Clientes</h3>
            <p class="text-gray-600 dark:text-gray-300">
              Gerencie todos os seus clientes com facilidade e tenha todas as informações em um só lugar.
            </p>
          </div>

          <!-- Módulo 2: Gerenciamento de Projetos -->
          <div class="module-card p-6 bg-white dark:bg-gray-700 shadow-md rounded-lg text-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="black" class="w-16 h-16 mx-auto mb-4 dark:stroke-gray-300">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
            </svg>
            <h3 class="text-xl font-semibold mb-2 text-black dark:text-gray-200">Gerenciamento de Projetos</h3>
            <p class="text-gray-600 dark:text-gray-300">
              Acompanhe o progresso dos projetos em tempo real com visualizações geográficas.
            </p>
          </div>

          <!-- Módulo 3: Controle Financeiro -->
          <div class="module-card p-6 bg-white dark:bg-gray-700 shadow-md rounded-lg text-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="black" class="w-16 h-16 mx-auto mb-4 dark:stroke-gray-300">
              <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v7.5m2.25-6.466a9.016 9.016 0 0 0-3.461-.203c-.536.072-.974.478-1.021 1.017a4.559 4.559 0 0 0-.018.402c0 .464.336.844.775.994l2.95 1.012c.44.15.775.53.775.994 0 .136-.006.27-.018.402-.047.539-.485.945-1.021 1.017a9.077 9.077 0 0 1-3.461-.203M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
            </svg>
            <h3 class="text-xl font-semibold mb-2 text-black dark:text-gray-200">Controle Financeiro</h3>
            <p class="text-gray-600 dark:text-gray-300">
              Monitore entradas e saídas, e tenha uma visão completa das finanças dos seus projetos.
            </p>
          </div>

        </div>
      </div>
    </section>


    <!-- Visão e Missão -->
    <section class="vision-mission-section bg-primary dark:bg-yellow-600 py-20 text-white dark:text-gray-900">
      <div class="container mx-auto flex flex-col md:flex-row justify-between">
        <div class="w-full md:w-1/2 px-6 mb-10 md:mb-0">
          <h2 class="text-4xl font-semibold mb-4">Nossa Visão</h2>
          <p class="text-lg leading-relaxed">
            Ser a plataforma de referência em gestão de projetos de engenharia, facilitando a comunicação entre equipes e otimizando a execução de cada etapa.
          </p>
        </div>
        <div class="w-full md:w-1/2 px-6">
          <h2 class="text-4xl font-semibold mb-4">Nossa Missão</h2>
          <p class="text-lg leading-relaxed">
            Fornecer soluções inovadoras e eficientes que garantam uma visão clara e precisa do progresso de cada obra, promovendo a profissionalização dos processos de gestão.
          </p>
        </div>
      </div>
    </section>
  </div>
</template>
