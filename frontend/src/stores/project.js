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
      const res = await fetch("/api/projects", {
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
        return res;
      }
    },

    /******************* Update a project *******************/
    async updateProject(projectId, formData) {
      const res = await fetch(`/api/projects/${projectId}`, {
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
        this.router.push({ name: "projectList" });
        this.projects = this.projects.map(project =>
          project.id === projectId ? data : project
        );
      }
    },

    /******************* Delete a project *******************/
    async deleteProject(projectId) {
      const res = await fetch(`/api/projects/${projectId}`, {
        method: "delete",
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      });

      if (res.ok) {
        this.router.push({ name: "projectList" });
        this.projects = this.projects.filter(project => project.id !== projectId);
      } else {
        const data = await res.json();
        this.errors = data.errors || {};
      }
    },
  },
});
