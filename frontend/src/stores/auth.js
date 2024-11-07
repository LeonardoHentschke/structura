import { defineStore } from "pinia";
import axios from "axios";

export const useAuthStore = defineStore("authStore", {
  state: () => {
    return {
      user: null,
      errors: {},
    };
  },
  actions: {
    /******************* Get authenticated user *******************/
    async getUser() {
      if (localStorage.getItem("token")) {
        try {
          const res = await axios.get("/api/user", {
            headers: {
              authorization: `Bearer ${localStorage.getItem("token")}`,
            },
          });
          this.user = res.data;
        } catch (error) {
          console.error(error);
        }
      }
    },
    /******************* Update User Profile *******************/
    async updateProfile(profileData) {
      // Verifica se o usuário autenticado está tentando atualizar seu próprio perfil
      if (this.user && this.user.id === profileData.id) {
        try {
          const res = await fetch(`/api/user`, {
            method: "PUT",
            headers: {
              Authorization: `Bearer ${localStorage.getItem("token")}`,
              "Content-Type": "application/json",
            },
            body: JSON.stringify(profileData),
          });
    
          const data = await res.json();
    
          // Verifica se há erros na resposta e atualiza o estado
          if (res.ok) {
            this.user = data.user; // Atualiza o estado do usuário com os dados retornados
            this.errors = {};
            return true;
          } else {
            this.errors = data.errors || { general: ["Erro ao atualizar perfil"] };
            return false;
          }
        } catch (error) {
          console.error("Erro ao atualizar perfil:", error);
          this.errors = { general: ["Erro inesperado. Por favor, tente novamente."] };
          return false;
        }
      } else {
        console.error("Acesso negado: o usuário não pode atualizar este perfil.");
        return false;
      }
    },    
    /******************* Login or Register user *******************/
    async authenticate(apiRoute, formData) {
      try {
        const res = await axios.post(`/api/${apiRoute}`, formData);
        this.errors = {};
        localStorage.setItem("token", res.data.token);
        this.user = res.data.user;
        this.router.push({ name: "dashboard" });
      } catch (error) {
        if (error.response && error.response.data.errors) {
          this.errors = error.response.data.errors;
        } else {
          this.errors = { general: ["Erro inesperado. Por favor, tente novamente."] };
        }
      }
    },
    /******************* Logout user *******************/
    async logout() {
      try {
        const res = await axios.post("/api/logout", null, {
          headers: {
            authorization: `Bearer ${localStorage.getItem("token")}`,
          },
        });

        if (res.status === 200) {
          this.user = null;
          this.errors = {};
          localStorage.removeItem("token");
          this.router.push({ name: "home" });
        }
      } catch (error) {
        console.error("Erro ao tentar fazer logout:", error);
      }
    },
  },
});