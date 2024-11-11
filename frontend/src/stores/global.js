// src/stores/global.js
import { defineStore } from 'pinia';

export const useGlobalStore = defineStore('global', {
  state: () => ({
    isLoading: false,
    loadingStartTime: null, // Novo campo para guardar o tempo de início do loading
  }),
  actions: {
    startLoading() {
      this.isLoading = true;
      this.loadingStartTime = Date.now(); // Armazenar o tempo de início
    },
    async finishLoading() {
      const minLoadingTime = 500; // 500ms de atraso mínimo

      const elapsedTime = Date.now() - this.loadingStartTime;
      const remainingTime = Math.max(minLoadingTime - elapsedTime, 0);

      // Espera pelo tempo restante, se houver
      await new Promise(resolve => setTimeout(resolve, remainingTime));

      this.isLoading = false;
      this.loadingStartTime = null; // Resetar o tempo de início
    }
  }
});
