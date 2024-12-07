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
      try {
        const res = await fetch("/api/projects", {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
          },
        });
    
        if (!res.ok) {
          const errorData = await res.json();
          console.error("Erro ao obter projetos:", errorData);
          this.projects = []; // Define a lista de projetos vazia em caso de erro
          return []; // Retorna array vazio em caso de erro para uso em outras partes do código
        }
    
        const projects = await res.json(); // Obtenha os dados da resposta
    
        // Atualize o estado dos projetos no store
        this.projects = projects;
    
        // Retorne a lista de projetos para outras funções que precisem dos dados
        return projects;
      } catch (error) {
        console.error("Erro ao buscar projetos:", error);
        this.projects = []; // Define a lista de projetos vazia em caso de erro
        return []; // Retorna array vazio em caso de erro para uso em outras partes do código
      }
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
            "Content-Type": "application/json"
          },
          body: JSON.stringify(formData),
        });

        if (!res.ok) {
          const errorData = await res.json();
          console.error("Erro ao criar o projeto:", errorData);
          // Log para ver a mensagem de erro específica do backend
          console.log("Erro detalhado:", errorData.errors);
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
        // Certifique-se de incluir o campo 'name' em formData
        const res = await fetch(`/api/projects/${projectId}`, {
          method: "put",
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
            "Content-Type": "application/json" // Importante para que a API reconheça que o corpo da requisição é JSON
          },
          body: JSON.stringify(formData), // formData deve conter o campo 'name'
        });

        if (!res.ok) {
          const errorData = await res.json();
          this.errors = errorData.errors || {};
          return { status: res.status, errors: this.errors };
        }

        const data = await res.json();
        this.errors = {};
        // Atualize o projeto na lista localmente
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
          // Atualize o estado do Pinia para remover o projeto excluído
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

    /******************* Create a project *******************/
    async createProject(formData) {
      try {
        // Verifique se todos os campos necessários estão preenchidos
        if (!formData.name || !formData.client_id || !formData.type_id || !formData.situation_id) {
          console.error("Formulário incompleto, verifique os campos obrigatórios");
          return { status: 400, errors: { message: "Formulário incompleto. Por favor, preencha todos os campos obrigatórios." } };
        }

        console.log("Dados enviados para criar o projeto:", formData); // Log para verificar o conteúdo do formData

        const res = await fetch("/api/projects", {
          method: "post",
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
            "Content-Type": "application/json"
          },
          body: JSON.stringify(formData),
        });

        if (!res.ok) {
          // Se a resposta não estiver ok, tente obter os detalhes do erro
          const errorData = await res.json();
          console.error("Erro ao criar o projeto:", errorData);
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

    /******************* Get all addresses of a client *******************/
    async getClientAddresses(clientId) {
      try {
        const res = await fetch(`/api/clients/${clientId}/addresses`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
          },
        });

        if (!res.ok) {
          throw new Error("Erro ao buscar endereços do cliente");
        }

        return await res.json(); // Espera que os endereços venham no formato de uma lista
      } catch (error) {
        console.error("Erro ao buscar endereços do cliente:", error);
        return [];
      }
    },
    /******************* Create an address *******************/
    async createAddress(clientId, formData) {
      try {
        const res = await fetch(`/api/clients/${clientId}/addresses`, {
          method: "POST",
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
            "Content-Type": "application/json"
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
    /******************* Get all employees *******************/
    async getAllEmployees() {
      try {
        const res = await fetch("/api/employees", {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
          },
        });

        if (!res.ok) {
          const errorData = await res.json();
          console.error("Erro ao obter funcionários:", errorData);
          return []; // Retorna uma lista vazia em caso de erro
        }

        const employees = await res.json(); // Obtenha os dados da resposta
        return employees; // Retorna a lista de funcionários para uso em outras partes do código
      } catch (error) {
        console.error("Erro ao buscar funcionários:", error);
        return []; // Retorna uma lista vazia em caso de erro
      }
    },
  },
});
