import { defineStore } from "pinia";
import { useAuthStore } from "@/stores/auth";

export const useClientsStore = defineStore("clientsStore", {
  state: () => {
    return {
      clients: [],
      client: null,
      errors: {},
    };
  },
  actions: {
    /******************* Get all clients *******************/
    async getAllClients() {
      const res = await fetch("/api/clients", {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      });
      const data = await res.json();
      this.clients = data;
      return data;
    },

    /******************* Get a single client *******************/
    async getClient(id) {
      const res = await fetch(`/api/clients/${id}`, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      });
      const data = await res.json();
      this.client = data;
      return data;
    },

    /******************* Create a client *******************/
    async createClient(formData) {
      const res = await fetch("/api/clients", {
        method: "post",
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
        body: JSON.stringify(formData),
      });

      const data = await res.json();
      
      if (data.errors) {
        this.errors = data.errors;
      } else {
        this.errors = {};
      }
    },

    /******************* Update a client *******************/
    async updateClient(clientId, formData) {
      const res = await fetch(`/api/clients/${clientId}`, {
        method: "put",
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
        body: JSON.stringify(formData),
      });

      const data = await res.json();
      if (data.errors) {
        this.errors = data.errors;
      } else {
        this.errors = {};
        this.router.push({ name: "clientList" });
      }
    },

    /******************* Delete a client *******************/
    async deleteClient(clientId) {
      const res = await fetch(`/api/clients/${clientId}`, {
        method: "delete",
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      });

      if (res.ok) {
        this.router.push({ name: "clientList" });
        this.clients = this.clients.filter(client => client.id !== clientId);
      } else {
        const data = await res.json();
        this.errors = data.errors || {};
      }
    },

    /******************* Get all addresses of a client with project linkage check *******************/
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

        const addresses = await res.json();

        // Agora, vamos verificar se cada endereço está vinculado a um projeto
        for (let address of addresses) {
          const projectsLinked = await this.getAddressProjects(address.id);
          address.hasProjects = projectsLinked.length > 0; // Adiciona um campo indicando se o endereço possui projetos
        }

        return addresses;
      } catch (error) {
        console.error("Erro ao buscar endereços do cliente:", error);
        return [];
      }
    },

    /******************* Get all projects linked to an address *******************/
    async getAddressProjects(addressId) {
      try {
        const res = await fetch(`/api/clients/addresses/${addressId}/projects`, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
          },
        });

        if (!res.ok) {
          throw new Error("Erro ao buscar projetos vinculados ao endereço");
        }

        return await res.json(); // Espera que os projetos venham no formato de uma lista
      } catch (error) {
        console.error("Erro ao buscar projetos vinculados ao endereço:", error);
        return [];
      }
    },
  },
});
