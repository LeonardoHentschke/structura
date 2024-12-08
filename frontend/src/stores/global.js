// src/stores/global.js
import { defineStore } from "pinia";

export const useGlobalStore = defineStore("global", {
  state: () => ({
    isLoading: false,
    loadingStartTime: null, // Armazena o tempo de início do loading
    minLoadingThreshold: 300, // Tempo mínimo (ms) para exibir o loader
  }),
  actions: {
    startLoading() {
      if (this.isLoading) {
        // Evitar múltiplas ativações de loading
        return;
      }
      this.isLoading = true;
      this.loadingStartTime = Date.now();
    },
    async finishLoading() {
      if (!this.isLoading) {
        // Garantir que o finishLoading só é chamado após startLoading
        return;
      }

      const elapsedTime = Date.now() - this.loadingStartTime;
      const remainingTime = Math.max(this.minLoadingThreshold - elapsedTime, 0);

      // Aguarda o tempo restante apenas se necessário
      if (remainingTime > 0) {
        await new Promise((resolve) => setTimeout(resolve, remainingTime));
      }

      this.isLoading = false;
      this.loadingStartTime = null; // Resetar o tempo de início
    },
    async withLoader(action) {
      try {
        this.startLoading();
        await action(); // Executa a ação passada
      } catch (error) {
        console.error("Erro durante a execução da ação com loader:", error);
      } finally {
        this.finishLoading();
      }
    },
  },
});
