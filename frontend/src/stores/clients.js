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
      const authStore = useAuthStore();
      if (authStore.user.id === formData.created_by) {
        const res = await fetch(`/api/clients/${clientId}`, {
          method: "put",
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`,
            "Content-Type": "application/json",
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
      }
    },

    /******************* Delete a client *******************/
    async deleteClient(clientId) {
      const authStore = useAuthStore();
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
  },
});
