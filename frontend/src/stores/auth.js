import { defineStore } from "pinia";

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
        const res = await fetch("/api/user", {
          headers: {
            authorization: `Bearer ${localStorage.getItem("token")}`,
          },
        });
        const data = await res.json();
        if (res.ok) {
          this.user = data;
        }
      }
    },
    /******************* Login or Register user *******************/
    async authenticate(apiRoute, formData) {
      const res = await fetch(`/api/${apiRoute}`, {
        method: "post",
        body: JSON.stringify(formData),
      });

      const data = await res.json();
      if (data.errors) {
        this.errors = data.errors;
      } else {
        this.errors = {};
        localStorage.setItem("token", data.token);
        this.user = data.user;
        this.router.push({ name: "dashboard" });
      }
    },
    /******************* Logout user *******************/
    async logout() {
      const res = await fetch("/api/logout", {
        method: "post",
        headers: {
          authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      });

      const data = await res.json();
      console.log(data);

      if (res.ok) {
        this.user = null;
        this.errors = {};
        localStorage.removeItem("token");
        this.router.push({ name: "home" });
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
  },
});
