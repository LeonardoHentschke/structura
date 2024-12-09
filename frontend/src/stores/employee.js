import { defineStore } from "pinia";

export const useEmployeeStore = defineStore("employeeStore", {
  state: () => ({
    employees: [],
    employee: null,
    errors: {},
  }),
  actions: {
    async getAllEmployees() {
      const res = await fetch("/api/employees", {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      });
      return await res.json();
    },
    async getEmployee(id) {
      const res = await fetch(`/api/employees/${id}`, {
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
        },
      });
      return await res.json();
    },
    async createEmployee(formData) {
      const res = await fetch("/api/employees", {
        method: "POST",
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
          "Content-Type": "application/json",
        },
        body: JSON.stringify(formData),
      });
      return await res.json();
    },
    async updateEmployee(id, formData) {
      const res = await fetch(`/api/employees/${id}`, {
        method: "PUT",
        headers: {
          Authorization: `Bearer ${localStorage.getItem("token")}`,
          "Content-Type": "application/json",
        },
        body: JSON.stringify(formData),
      });
      return await res.json();
    },
    async deleteEmployee(id) {
      try {
        const response = await fetch(`/api/employees/${id}`, {
          method: "DELETE",
          headers: {
            "Authorization": `Bearer ${localStorage.getItem("token")}`,
            "Content-Type": "application/json",
          },
        });
    
        if (!response.ok) {
          throw new Error("Erro ao excluir funcionário");
        }
    
        console.log("Funcionário excluído com sucesso:", id);
      } catch (error) {
        console.error("Erro ao excluir funcionário:", error);
        throw error;
      }
    },    
  },
});
