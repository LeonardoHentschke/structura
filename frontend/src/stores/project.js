import { defineStore } from "pinia";
import { useAuthStore } from "@/stores/auth";

export const useProjectStore = defineStore("projectStore", {
  state: () => {
    return {
      projects: [],
      project: null,
      errors: {},
    };
  },
  actions: {
    /******************* Get all projects *******************/
    async getAllProjects() {
      const res = await fetch("/api/projects", {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      });
      const data = await res.json();
      this.projects = data;
      return data;
    },

    /******************* Get a single project *******************/
    async getProject(id) {
      const res = await fetch(`/api/projects/${id}`, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      });
      const data = await res.json();
      this.project = data;
      return data;
    },

    /******************* Create a project *******************/
    async createProject(formData) {
      try {
        const res = await fetch("/api/projects", {
          method: "post",
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
          },
          body: JSON.stringify(formData),
        });

        if (!res.ok) {
          const errorData = await res.json();
          this.errors = errorData.errors || {};
          return { status: res.status, errors: this.errors };
        }

        const data = await res.json();
        this.errors = {};
        return { status: res.status, data };
      } catch (error) {
        console.error("Erro ao criar o projeto:", error);
        throw error;
      }
    },

    /******************* Update a project *******************/
    async updateProject(projectId, formData) {
      try {
        const res = await fetch(`/api/projects/${projectId}`, {
          method: "put",
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
          },
          body: JSON.stringify(formData),
        });

        if (!res.ok) {
          const errorData = await res.json();
          this.errors = errorData.errors || {};
          return { status: res.status, errors: this.errors };
        }

        const data = await res.json();
        this.errors = {};
        this.projects = this.projects.map((project) =>
          project.id === projectId ? data : project
        );

        return { status: res.status, data };
      } catch (error) {
        console.error("Erro ao atualizar o projeto:", error);
        throw error;
      }
    },

    /******************* Delete a project *******************/
    async deleteProject(projectId) {
      try {
        const res = await fetch(`/api/projects/${projectId}`, {
          method: "delete",
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
          },
        });

        if (res.ok) {
          this.projects = this.projects.filter(
            (project) => project.id !== projectId
          );
          return { status: res.status };
        } else {
          const data = await res.json();
          this.errors = data.errors || {};
          return { status: res.status, errors: this.errors };
        }
      } catch (error) {
        console.error("Erro ao excluir o projeto:", error);
        throw error;
      }
    },

    /******************* Create an address *******************/
    async createAddress(formData) {
      try {
        const res = await fetch("/api/projects/addresses", {
          method: "post",
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
          },
          body: JSON.stringify(formData),
        });

        if (!res.ok) {
          const errorData = await res.json();
          this.errors = errorData.errors || {};
          throw new Error("Erro ao criar o endereço");
        }

        const data = await res.json();
        return data;
      } catch (error) {
        console.error("Erro ao criar o endereço:", error);
        throw error;
      }
    },

    /******************* Get all addresses of a client *******************/
    async getClientAddresses(clientId) {
      const res = await fetch(`/api/projects/clients/${clientId}/addresses`, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      });
      if (!res.ok) {
        throw new Error("Erro ao buscar endereços do cliente");
      }
      const data = await res.json();
      return data;
    },
  },
});
