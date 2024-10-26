import { defineStore } from "pinia";
import { useAuthStore } from "@/stores/auth";

export const useProjectSituationsStore = defineStore("projectSituationsStore", {
  state: () => {
    return {
      errors: {},
      situations: [],
    };
  },
  actions: {

    /******************* Get all project situations *******************/
    async getAllProjectSituations() {
      const res = await fetch("/api/project-situations");
      const data = await res.json();
      this.situations = data;
      return data;
    },
    
    /******************* Get a project situation *******************/
    async getProjectSituation(id) {
      const res = await fetch(`/api/project-situations/${id}`);
      const data = await res.json();
      return data.situation;
    },
    
    /******************* Create a project situation *******************/
    async createProjectSituation(formData) {
      const res = await fetch("/api/project-situations", {
        method: "POST",
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
      }
    },
    
    /******************* Delete a project situation *******************/
    async deleteProjectSituation(id) {
      const res = await fetch(`/api/project-situations/${id}`, {
        method: "DELETE",
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      });

      if (res.ok) {
        this.situations = this.situations.filter(situation => situation.id !== id);
      }
    },
    
    /******************* Update a project situation *******************/
    async updateProjectSituation(id, formData) {
      const res = await fetch(`/api/project-situations/${id}`, {
        method: "PUT",
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
        body: JSON.stringify(formData),
      });

      const data = await res.json();
      if (data.errors) {
        this.errors = data.errors;
      } else {
        const index = this.situations.findIndex(situation => situation.id === id);
        if (index !== -1) {
          this.situations[index] = data;
        }
        this.errors = {};
      }
    },
  },
});
