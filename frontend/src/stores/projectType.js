import { defineStore } from "pinia";
import { useRouter } from 'vue-router';

export const useProjectTypesStore = defineStore("projectTypesStore", {
  state: () => {
    return {
      errors: {},
      types: [],
    };
  },
  actions: {

    /******************* Get all project type *******************/
    async getAllProjectTypes() {
      const res = await fetch("/api/project-types");
      const data = await res.json();
      this.types = data;
      return data;
    },

    /******************* Get a project type *******************/
    async getProjectType(id) {
      const res = await fetch(`/api/project-types/${id}`);
      const data = await res.json();
      return data.type;
    },

    /******************* Create a project type *******************/
    async createProjectType(formData) {
      const res = await fetch("/api/project-types", {
        method: "POST",
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

    /******************* Delete a project type *******************/
    async deleteProjectType(id) {
      const res = await fetch(`/api/project-types/${id}`, {
        method: "DELETE",
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      });

      if (res.ok) {
        this.types = this.types.filter(type => type.id !== id);
      }
    },

    /******************* Update a project type *******************/
    async updateProjectType(id, formData) {
      const res = await fetch(`/api/project-types/${id}`, {
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
        const index = this.types.findIndex(type => type.id === id);
        if (index !== -1) {
          this.types[index] = data.type;
        }
        this.errors = {};
      }
    },
  },
});
